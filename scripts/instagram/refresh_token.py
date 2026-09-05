#!/usr/bin/env python3
"""Instagram の長期アクセストークン（60日）を延長する。

期限が切れると投稿が止まるため、週1回このスクリプトを走らせて
有効期限を60日先に押し戻し続ける。新しいトークンは --out で指定した
ファイルに書き出し、呼び出し側が GitHub Secrets に反映する。

    python3 scripts/instagram/refresh_token.py --out /tmp/token.txt
"""

import argparse
import json
import os
import sys
import urllib.error
import urllib.parse
import urllib.request

ENDPOINT = "https://graph.instagram.com/refresh_access_token"


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--out", help="新しいトークンの書き出し先")
    args = ap.parse_args()

    token = os.environ.get("IG_ACCESS_TOKEN")
    if not token:
        sys.exit("環境変数 IG_ACCESS_TOKEN が設定されていません。")

    query = urllib.parse.urlencode({"grant_type": "ig_refresh_token", "access_token": token})
    try:
        with urllib.request.urlopen(f"{ENDPOINT}?{query}", timeout=60) as res:
            data = json.loads(res.read().decode())
    except urllib.error.HTTPError as exc:
        body = exc.read().decode(errors="replace")
        sys.exit(
            "トークンの延長に失敗しました。期限が切れている可能性があります。\n"
            "  → 設定手順書の「トークンを取り直す」からやり直してください。\n"
            f"  応答: {body[:300]}"
        )

    days = data.get("expires_in", 0) // 86400
    print(f"トークンを延長しました。残り約 {days} 日です。")

    if args.out:
        with open(args.out, "w", encoding="utf-8") as fh:
            fh.write(data["access_token"])
        print(f"新しいトークンを {args.out} に書き出しました。")


if __name__ == "__main__":
    main()
