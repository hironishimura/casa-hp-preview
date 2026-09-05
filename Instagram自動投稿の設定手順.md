# Instagram 自動投稿の設定手順

施工例を1件ぶんカルーセル（写真最大10枚）にまとめ、**毎週火曜と金曜の10時**に
自動で Instagram へ投稿する仕組みです。67件あるので約8か月ぶんのストックがあります。

費用はかかりません（GitHub Actions の公開リポジトリ枠と Meta の無料API）。

---

## 素材の扱いについて

**施工例67件は、すべて栃木県外です**（静岡21件、東京8件、山形8件…）。
design casa 本部から提供された全国の実例で、
**参考事例としての掲載許諾は確認済み**です（2026-09-04 お客さま確認）。

自社施工と誤解されないよう、投稿文の冒頭に2つの情報を必ず入れています。

```
窓辺を楽しむ家｜長崎県・平屋
design casa の全国の建築実例より
```

この2行目の文言は `scripts/instagram/settings.json` の `"事例の注記"` で変更できます。
空文字 `""` にすると表示されません。

---

## 全体像

```
毎週 火・金 10:00
  → GitHub Actions が起動
  → まだ投稿していない施工例を1件選ぶ
  → works.json の写真とキャプションでカルーセルを組む
  → Instagram に投稿
  → 投稿済みとして記録（同じ家は二度出ません）
```

写真は既に GitHub Pages で公開されているものをそのまま使います。
新しくアップロードする作業はありません。

---

## STEP 1　Instagram をプロアカウントにする（お客さま作業・5分）

APIで投稿するには**プロアカウント**である必要があります。個人アカウントでは投稿できません。

1. Instagram アプリ →　プロフィール → 右上の三本線 → 「設定とプライバシー」
2. 「アカウントの種類とツール」→「プロアカウントに切り替える」
3. カテゴリは「住宅リフォーム」「建設会社」など。**「ビジネス」**を選ぶ
4. 切り替え後、フォロワー数などは変わりません。いつでも個人に戻せます

> Facebookページは**不要**です。今回は Facebook を介さない接続方法を使います。

## STEP 2　Meta で連携アプリを作る（10分）

1. https://developers.facebook.com/apps/ を開く（Facebookアカウントでログイン）
2. 「アプリを作成」→ ユースケースで **「Instagram」** を選ぶ
3. アプリ名は任意（例：`design casa 宇都宮 投稿`）
4. 作成後、左メニューの **Instagram → API設定（Instagramログインあり）** を開く

## STEP 3　トークンとユーザーIDを取得する（5分）

STEP 2 の画面の中に、順番に押していく項目が並んでいます。

1. **「Instagramアカウントを追加」** を押し、STEP 1 のアカウントでログイン・許可
2. 権限は **`instagram_business_basic`** と **`instagram_business_content_publish`** の
   2つにチェックが入っていることを確認
3. **「アクセストークンを生成」** を押す
   → 長い文字列が出ます。これが **アクセストークン**（60日間有効）
4. 同じ画面に表示されている **Instagram ユーザーID**（数字の羅列）も控える

> 画面の文言はMeta側の更新でときどき変わります。
> 迷ったら画面のスクリーンショットを送ってください。どこを押すかお伝えします。

## STEP 4　GitHub に登録する（5分）

https://github.com/hironishimura/casa-hp-preview/settings/secrets/actions

「New repository secret」で次の2つを登録します。

| 名前 | 中身 |
|---|---|
| `IG_ACCESS_TOKEN` | STEP 3 のアクセストークン |
| `IG_USER_ID` | STEP 3 のInstagramユーザーID |

### あわせて登録を推奨：`GH_PAT`

トークンは60日で切れます。これを**自動で延長し続ける**ために、もう1つ登録します。

1. https://github.com/settings/personal-access-tokens/new を開く
2. Repository access → `casa-hp-preview` のみ
3. Permissions → Repository permissions → **Secrets** を **Read and write** に
4. 有効期限は「No expiration」または1年
5. 発行された文字列を `GH_PAT` という名前で Secrets に登録

未登録でも投稿はできますが、**60日ごとに STEP 3 をやり直す必要**があります。

## STEP 5　テスト投稿で確認する

1. https://github.com/hironishimura/casa-hp-preview/actions
2. 左の「Instagram 自動投稿」→「Run workflow」
3. **まず `投稿せず内容だけ確認する` を ✔ のまま実行**し、本文が意図どおりか見る
4. 問題なければ、チェックを外してもう一度実行 → 実際に投稿されます

---

## 運用

### 投稿の曜日・時刻を変える

`.github/workflows/instagram-post.yml` の `cron: '0 1 * * 2,5'` を書き換えます。
**UTC表記**なので日本時間から9時間引いてください。

| やりたいこと | 書き方 |
|---|---|
| 火・金の10時（現在） | `0 1 * * 2,5` |
| 毎日10時 | `0 1 * * *` |
| 月・木の19時 | `0 10 * * 1,4` |

> GitHub側の混雑で数分〜数十分ずれます。分単位の正確さは期待しないでください。

### 投稿文・ハッシュタグを変える

`scripts/instagram/settings.json` を編集します。署名や固定ハッシュタグはここにあります。
写真ごとの説明文は `wordpress/wp-content/themes/designcasa-shome/data/works.json` が元データで、
サイト本体と共通です。

### 特定の写真を出さないようにする

`scripts/instagram/skip_photos.json` にファイル名を追加します。
（現在91枚が登録済み。Instagramが受け付けない縦長比率のため自動で除外しています）

### 投稿の順番

`settings.json` の `"投稿順"` を `"random"` にすると順不同になります。既定は works.json の並び順です。

### 止め方

Actions のページ →「Instagram 自動投稿」→ 右上「…」→ **Disable workflow**。
再開は同じ場所から。

---

## 困ったとき

| 症状 | 原因と対処 |
|---|---|
| メールで実行失敗の通知が来た | Actions のログを開くと日本語で理由が出ます |
| 「アクセストークンが無効か期限切れです」 | STEP 3 をやり直し、`IG_ACCESS_TOKEN` を更新 |
| 何も投稿されない・起動しない | Actions が Disable になっていないか確認 |
| 同じ家が二度投稿された | `scripts/instagram/posted.json` に記録が残っているか確認 |
| 写真が切れて表示される | その写真を `skip_photos.json` に追加 |

### 手元で試す

投稿はせず、次に何が出るかだけ確認できます。

```bash
python3 scripts/instagram/post.py --dry-run
```

---

## この仕組みのファイル

| ファイル | 役割 |
|---|---|
| `.github/workflows/instagram-post.yml` | 火・金に投稿を起動 |
| `.github/workflows/instagram-token-refresh.yml` | 週1でトークンを延長 |
| `scripts/instagram/post.py` | 投稿の本体 |
| `scripts/instagram/refresh_token.py` | トークン延長 |
| `scripts/instagram/settings.json` | 署名・ハッシュタグなどの設定 |
| `scripts/instagram/skip_photos.json` | 投稿しない写真 |
| `scripts/instagram/posted.json` | 投稿済みの記録（自動更新） |
