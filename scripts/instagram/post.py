#!/usr/bin/env python3
"""施工例を1件ぶんカルーセルにして Instagram へ投稿する。

works.json から未投稿の施工例を1件選び、写真とキャプションを組み立てて
Instagram Graph API（Instagram Login ルート）で公開する。
投稿した施工例は posted.json に記録し、同じ家を二度出さない。

外部ライブラリは使わない（GitHub Actions でそのまま動かすため）。

    python3 scripts/instagram/post.py --dry-run   # 投稿せず中身だけ確認
    python3 scripts/instagram/post.py             # 実際に投稿する
"""

import argparse
import json
import os
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from datetime import datetime, timedelta, timezone

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(os.path.dirname(HERE))
WORKS_JSON = os.path.join(ROOT, "wordpress/wp-content/themes/designcasa-shome/data/works.json")
SETTINGS_JSON = os.path.join(HERE, "settings.json")
SKIP_JSON = os.path.join(HERE, "skip_photos.json")
POSTED_JSON = os.path.join(HERE, "posted.json")

JST = timezone(timedelta(hours=9))
GRAPH = "https://graph.instagram.com"
VERSION = os.environ.get("IG_GRAPH_VERSION", "v23.0")


class ApiError(Exception):
    pass


def load_json(path, default=None):
    if not os.path.exists(path):
        if default is None:
            sys.exit(f"必要なファイルがありません: {path}")
        return default
    with open(path, encoding="utf-8") as fh:
        return json.load(fh)


def api(method, path, params, token):
    """Graph API を叩いて JSON を返す。"""
    url = f"{GRAPH}/{VERSION}/{path}"
    data = dict(params, access_token=token)
    if method == "GET":
        req = urllib.request.Request(f"{url}?{urllib.parse.urlencode(data)}")
    else:
        req = urllib.request.Request(url, data=urllib.parse.urlencode(data).encode(), method="POST")
    try:
        with urllib.request.urlopen(req, timeout=60) as res:
            return json.loads(res.read().decode())
    except urllib.error.HTTPError as exc:
        body = exc.read().decode(errors="replace")
        try:
            err = json.loads(body)["error"]
        except Exception:
            raise ApiError(f"HTTP {exc.code}: {body[:400]}") from None
        code = err.get("code")
        msg = err.get("message", "")
        if code == 190:
            raise ApiError(
                "アクセストークンが無効か期限切れです。\n"
                "  → 設定手順書の「トークンを取り直す」を実行し、"
                "GitHub の Secrets の IG_ACCESS_TOKEN を更新してください。"
            ) from None
        raise ApiError(f"Instagram API エラー (code={code}): {msg}") from None


def pick_work(works, posted, settings, only_slug=None):
    """次に投稿する施工例と、それが何周目かを返す。"""
    if only_slug:
        for w in works:
            if w["slug"] == only_slug:
                return w, posted.get("cycle", 1)
        sys.exit(f"施工例が見つかりません: {only_slug}")

    cycle = posted.get("cycle", 1)
    done = {p["slug"] for p in posted.get("posted", []) if p.get("cycle", 1) == cycle}

    remaining = [w for w in works if w["slug"] not in done]
    if not remaining:
        # 一巡したので次の周に進める。過去の記録は履歴として残す。
        cycle += 1
        remaining = list(works)
        print(f"すべての施工例を投稿し終えたため、{cycle}周目に入ります。")

    if settings.get("投稿順") == "random":
        import random
        return random.choice(remaining), cycle
    return remaining[0], cycle


def photos_for(work, skip):
    """アスペクト比が使える写真だけを返す。"""
    return [g for g in work["gallery"] if g["file"] not in skip]


def build_caption(work, photos, settings):
    """投稿本文を組み立てる。上限を超えたら写真の説明を後ろから削る。"""
    lines = [f"{work['title']}｜{work['pref']}・{work['type']}"]
    note = (settings.get("事例の注記") or "").strip()
    if note:
        # 栃木県外の実例のため、自社施工と誤解されないよう出典を添える
        lines.append(note)
    lines += ["", work["catch"]]

    tags = []
    for t in list(work.get("tags", [])) + [work.get("type")] + settings["固定ハッシュタグ"]:
        t = (t or "").strip().replace(" ", "")
        if t and t not in tags:
            tags.append(t)
    tag_line = " ".join("#" + t for t in tags[: settings["ハッシュタグの上限"]])

    url = settings["施工例ページのベースURL"].rstrip("/") + "/" + work["slug"] + "/"
    footer = ["", f"▸ 間取りと写真の全点はこちら", url, ""] + settings["署名"] + ["", tag_line]

    limit = settings["キャプションの上限文字数"]
    details = [f"{i}｜{p['caption']}" for i, p in enumerate(photos, 1)]

    while True:
        body = "\n".join(lines + ([""] + details if details else []) + footer)
        if len(body) <= limit or not details:
            return body
        details.pop()


def create_carousel(ig_user_id, token, photos, caption, base_url):
    """子コンテナ→親コンテナの順に作り、公開できる状態の creation_id を返す。"""
    children = []
    for i, p in enumerate(photos, 1):
        res = api("POST", f"{ig_user_id}/media", {
            "image_url": base_url + p["file"],
            "is_carousel_item": "true",
        }, token)
        children.append(res["id"])
        print(f"  {i}/{len(photos)} アップロード: {p['file']}")

    parent = api("POST", f"{ig_user_id}/media", {
        "media_type": "CAROUSEL",
        "children": ",".join(children),
        "caption": caption,
    }, token)
    return parent["id"]


def wait_ready(container_id, token, timeout=300):
    """Instagram 側の処理が終わるまで待つ。"""
    deadline = time.time() + timeout
    while time.time() < deadline:
        res = api("GET", container_id, {"fields": "status_code,status"}, token)
        status = res.get("status_code")
        if status == "FINISHED":
            return
        if status == "ERROR":
            raise ApiError(f"Instagram 側で画像の処理に失敗しました: {res.get('status')}")
        time.sleep(5)
    raise ApiError("Instagram 側の処理が5分で終わりませんでした。")


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--dry-run", action="store_true", help="投稿せず、選ばれた施工例と本文を表示する")
    ap.add_argument("--slug", help="施工例を指定して投稿する（テスト用）")
    args = ap.parse_args()

    settings = load_json(SETTINGS_JSON)
    works = load_json(WORKS_JSON)
    skip = set(load_json(SKIP_JSON).get("skip", {}))
    posted = load_json(POSTED_JSON, {"posted": []})

    work, cycle = pick_work(works, posted, settings, args.slug)
    photos = photos_for(work, skip)[: settings["1投稿あたりの最大枚数"]]
    if len(photos) < 2:
        sys.exit(f"{work['title']}: 使える写真が{len(photos)}枚しかなく、カルーセルを作れません。")

    caption = build_caption(work, photos, settings)

    print(f"施工例: {work['title']}（{work['slug']}）")
    print(f"写真  : {len(photos)}枚 / ギャラリー{len(work['gallery'])}枚中")
    print(f"本文  : {len(caption)}文字")
    print("-" * 60)
    print(caption)
    print("-" * 60)

    if args.dry_run:
        print("\n[dry-run] 実際の投稿は行いませんでした。")
        return

    token = os.environ.get("IG_ACCESS_TOKEN")
    ig_user_id = os.environ.get("IG_USER_ID")
    if not token or not ig_user_id:
        sys.exit("環境変数 IG_ACCESS_TOKEN と IG_USER_ID が設定されていません。")

    creation_id = create_carousel(ig_user_id, token, photos, caption, settings["画像のベースURL"])
    print("Instagram 側の処理を待っています…")
    wait_ready(creation_id, token)

    result = api("POST", f"{ig_user_id}/media_publish", {"creation_id": creation_id}, token)

    # ここから先で失敗しても投稿自体は成功しているため、記録を必ず残す。
    # 記録が残らないと次回に同じ施工例をもう一度投稿してしまう。
    permalink = ""
    try:
        permalink = api("GET", result["id"], {"fields": "permalink"}, token).get("permalink", "")
    except ApiError as exc:
        print(f"投稿は成功しましたが、URLの取得に失敗しました: {exc}")
    print(f"投稿しました: {permalink or result['id']}")

    posted["cycle"] = cycle
    posted.setdefault("posted", []).append({
        "slug": work["slug"],
        "title": work["title"],
        "photos": len(photos),
        "cycle": cycle,
        "at": datetime.now(JST).isoformat(timespec="seconds"),
        "permalink": permalink,
    })
    with open(POSTED_JSON, "w", encoding="utf-8") as fh:
        json.dump(posted, fh, ensure_ascii=False, indent=2)
        fh.write("\n")


if __name__ == "__main__":
    try:
        main()
    except ApiError as exc:
        sys.exit(f"\n投稿に失敗しました。\n{exc}")
