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

## 投稿先アカウントと Meta アプリ

- Instagram：**@d_casa_u**（design casa 宇都宮）
- Meta アプリ：**d-casaインスタ自動投稿**（アプリID `4529601620646485`／ビジネス：エスホーム）

| 行き先 | URL |
|---|---|
| ダッシュボード | https://developers.facebook.com/apps/4529601620646485/dashboard/ |
| ユースケースのカスタマイズ | https://developers.facebook.com/apps/4529601620646485/use_cases/customize/ |
| テスター招待の承認（Instagram側） | https://www.instagram.com/accounts/manage_access/ |

> 管理画面には Instagram ユーザーID `17841441355355354` も表示されますが、
> **登録は不要**です。API が返すIDとは体系が違い一致しないため、
> スクリプトは `me`（トークンの持ち主自身）を使う設計にしています。

## STEP 1　Instagram をプロアカウントにする（お客さま作業・5分）

APIで投稿するには**プロアカウント**である必要があります。個人アカウントでは投稿できません。

1. Instagram アプリ →　プロフィール → 右上の三本線 → 「設定とプライバシー」
2. 「アカウントの種類とツール」→「プロアカウントに切り替える」
3. カテゴリは「住宅リフォーム」「建設会社」など。**「ビジネス」**を選ぶ
4. 切り替え後、フォロワー数などは変わりません。いつでも個人に戻せます

> Facebookページは**不要**です。今回は Facebook を介さない接続方法を使います。

## STEP 2　Meta で連携アプリを作る（10分）

1. https://developers.facebook.com/apps/ を開く（Facebookアカウントでログイン）
2. 「アプリを作成」→ アプリ名を入力（例：`d-casaインスタ自動投稿`）
3. ユースケースの画面で、左のフィルターを **「すべて (20)」に切り替える**
   → **おすすめの6件には Instagram が出てきません**。ここで詰まりやすいので注意
4. **「Instagramでメッセージとコンテンツを管理」** を選ぶ
5. ビジネスポートフォリオは **エスホーム**（@d_casa_u を管理しているほう）
6. 「要件」は「要件を特定できませんでした」と出ますが**正常**。そのまま次へ
7. 「概要」で **アプリを作成し切る**（ここまで押さないとアプリはできません）

## STEP 3　トークンとユーザーIDを取得する（15分）

アプリのダッシュボードで
**「Instagramでメッセージとコンテンツを管理」ユースケースをカスタマイズ** をクリック。
左の **「Instagramログインによる API設定」** が作業画面です。

### 3-1　必須の権限を入れる

「1. 必要なメッセージアクセス許可を追加する」の
**「Add all required permissions」** を押します。次の3つが入ります。

- `instagram_business_basic`
- `instagram_business_manage_comments`
- `instagram_business_manage_messages`

### 3-2　投稿権限を自分で足す（重要）

> ⚠️ **投稿に必須の `instagram_business_content_publish` は、上のリストに含まれていません。**
> 「任意の権限」扱いのため、自分で追加する必要があります。
> これを忘れると、投稿時に権限エラーで失敗します。

左メニューの **「アクセス許可と機能」** を開き、
`instagram_business_content_publish` を検索して **追加** してください。

### 3-3　アカウントをテスターにする

開発モードのアプリには、テスター権限のあるアカウントしか接続できません。

1. 左メニュー下部の **「アプリの役割」→「役割」**
2. **Instagramテスター** として `d_casa_u` を追加
3. **Instagram アプリ側で招待を承認する**
   → 設定とプライバシー → ウェブサイトのアクセス許可 → テスターの招待 → 承認

### 3-4　トークンを生成する

> ⚠️ 「2. アクセストークンを生成する」の **「トークンを生成」ボタンは動作しません**。
> ポップアップが認可画面まで進まず、instagram.com に飛ばされます（2026-09-05 時点）。
> 以下の手順で直接取得してください。

**(1) 認可してコードを得る**

ブラウザで次のURLを開き、**「許可」** を押します。

```
https://www.instagram.com/oauth/authorize?client_id=1374089647683781&redirect_uri=https%3A%2F%2Fhironishimura.github.io%2Fcasa-hp-preview%2F&scope=instagram_business_basic%2Cinstagram_business_content_publish&response_type=code
```

サイトにリダイレクトされ、アドレスバーが `...?code=XXXXX#_` になります。
この **`code=` から `#_` の手前まで**が認証コードです（1時間で失効・1回限り）。

**(2) app secret を控える**

「InstagramログインによるAPI設定」画面の最上部、`Instagram app secret` の
**「表示」** を押して32桁の文字列をコピーします。

> ⚠️ 「アプリの設定 → ベーシック」にある app secret とは**別物**です。
> `client_id` に Instagram アプリID を使うので、Instagram app secret でなければ通りません。

**(3) 短期トークンに交換**

```bash
IG_SECRET='32桁のapp secret'; echo "文字数: ${#IG_SECRET}"
```

32 と出ることを確認してから、

```bash
curl -s -X POST https://api.instagram.com/oauth/access_token \
  -F client_id=1374089647683781 -F client_secret="$IG_SECRET" \
  -F grant_type=authorization_code \
  -F redirect_uri=https://hironishimura.github.io/casa-hp-preview/ \
  -F code='(1)のコード'
```

**(4) 60日トークンに交換**

```bash
curl -s -G https://graph.instagram.com/access_token \
  -d grant_type=ig_exchange_token -d client_secret="$IG_SECRET" \
  -d access_token='(3)の短期トークン'
```

`expires_in` が約 5183944（60日）で返る `access_token` が最終的な値です。

**(5) 動作確認**

```bash
curl -s "https://graph.instagram.com/v23.0/me?fields=id,username&access_token=(4)のトークン"
```

`username` が `d_casa_u` なら成功です。
（`id` は管理画面の番号と一致しませんが、正常です）

**(6) 後始末**

```bash
unset IG_SECRET
```

app secret がシェル履歴に残るので、`~/.zsh_history` からも消しておいてください。

### 触らなくてよい項目

| 項目 | 理由 |
|---|---|
| 3. Webhooksを設定する | コメント通知の受信用。投稿だけなら不要 |
| 4. Instagramビジネスログイン | 他人にログインさせる場合の設定。不要 |
| Instagram app secret | 今回は使わない。**表示・共有しないこと** |
| 技術提供者になる | 他社データにアクセスする場合の申請。不要 |
| 「公開」が未公開のまま | 自社アカウントへの投稿は開発モードのまま動く |

## STEP 4　GitHub に登録する（5分）

https://github.com/hironishimura/casa-hp-preview/settings/secrets/actions

「New repository secret」で次の2つを登録します。

| 名前 | 中身 |
|---|---|
| `IG_ACCESS_TOKEN` | STEP 3 で取得した60日トークン |

> `IG_USER_ID` は**不要**です。スクリプトは `me`（トークンの持ち主自身）を使います。
> Instagram には体系の違うIDが2つあり、Metaの管理画面に出ている番号と
> APIが返す番号が一致しないため、取り違えを避ける設計にしています。

### あわせて登録を推奨：`GH_PAT`

トークンは60日で切れます。これを**自動で延長し続ける**ために、もう1つ登録します。

1. https://github.com/settings/personal-access-tokens/new を開く
2. Repository access → `casa-hp-preview` のみ
3. Permissions → Repository permissions → **Secrets** を **Read and write** に
4. 有効期限は「No expiration」または1年
5. 発行された文字列を `GH_PAT` という名前で Secrets に登録

未登録でも投稿はできますが、**60日ごとに STEP 3 をやり直す必要**があります。

## STEP 5　テスト投稿で確認する

https://github.com/hironishimura/casa-hp-preview/actions
→ 左の「Instagram 自動投稿」→「Run workflow」

**実行モード**を3段階で選べます。**この順に確認**してください。

| モード | 何をするか |
|---|---|
| `check` | トークンの有効性と**投稿先アカウント名**を表示（投稿しない） |
| `dry-run` | 次に投稿される施工例と**本文**を表示（投稿しない） |
| `post` | **実際に投稿する** |

`check` で `@d_casa_u` と出ることを必ず確認してから先へ進んでください。
別のアカウント名が出た場合は、トークンを取り直しです。

> 定時実行（火・金 10:00）は常に `post` で動きます。

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
