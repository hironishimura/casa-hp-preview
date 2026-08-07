# 引き継ぎメモ（design casa 宇都宮 / エスホーム）

最終更新：2026-08-07　テーマ v1.3.0

---

## いまどこまで進んでいるか

| 項目 | 状態 |
|---|---|
| WordPressテーマ `designcasa-shome` | **完成**（v1.3.0） |
| 施工例67件・写真659点（1枚ごとにコメント） | **完成** |
| 建築家13名（並び順：内山→敷浪→小倉→深瀬→他9名） | **完成** |
| 家の仕様8項目・建材写真50点 | **完成** |
| 全コンテンツのGutenbergブロック化 | **完成** |
| 内容確認用プレビュー（GitHub Pages） | **公開中** |
| **d-casa.jp への設置** | **設置済み**（v1.2.0 が稼働中。v1.3.0 への入れ替え待ち） |

### 公開中のプレビュー
https://hironishimura.github.io/casa-hp-preview/
（noindex + robots.txt で検索エンジンには出ません）

### リポジトリ
https://github.com/hironishimura/casa-hp-preview （public）

---

## 次にやること

**お客さまが d-casa.jp のテーマを v1.3.0 に入れ替える**
（`wordpress/designcasa-shome-lite.zip` 2.6MB。手順は `WordPressへの設置手順.md`）

入れ替えたあと、管理画面で **必ず1回だけ**：

1. 「design casa 初期データ」→ **「固定ページの文章を作り直す」** を押す
   （トップページの文章の修正を反映させるため。数秒で終わります。
   　写真の取り込みはやり直しません）
2. これでタームのスラッグ修復とパーマリンク再構築も同時に走ります

> **なぜ必要か**：ページの文章はデータベースに保存されているため、
> テーマZIPを入れ替えただけでは古い文章のままです。

---

## v1.3.0 で直したこと

| 症状 | 原因 | 直した場所 |
|---|---|---|
| 施工例の絞り込みタブが全部404 | タームのスラッグが日本語（URLエンコード）で作られていた | `inc/content.php` の `dcs_term_slugs()` にローマ字対応表を追加。`dcs_ensure_terms()` / `dcs_sync_term_slugs()` で付与・修復 |
| 建築家名・電話番号が暗背景に暗文字で読めない | `.entry-content a{color:var(--text)}` が暗いセクション内でも効いていた | `style.css`：暗背景セクション内のリンク色を反転（ボタンは `:not()` で除外） |
| 対応エリアの「・」の位置がずれる | `.entry-content ul{list-style:disc}` が flex 並びの `.area__list` にも当たっていた | `style.css`：テーマ独自の一覧はマーカーと字下げを打ち消す |
| ― | トップの狙いキーワードを「注文住宅 宇都宮」に | `inc/page-content.php` / `inc/content.php` の文章、`inc/seo.php` のタイトル |

### ⚠️ Claudeはパスワードを入力してログインできません
`.env` に管理画面のIDとパスワードがありますが、**Claudeは認証情報を入力する操作ができません**。
サーバーへの反映（アップロード・有効化・取り込み）は必ずお客さまに実行していただきます。
そのぶん「ZIPをアップして有効化 → ボタン1つ」で終わるところまで用意済みです。

---

## 作業環境の再開方法

PHP は Homebrew で導入済み（`/opt/homebrew/bin/php`）。不要になったら `brew uninstall php`。

```bash
cd "/Users/Nishi/Library/Mobile Documents/com~apple~CloudDocs/0プログラム/ClaudeObsidian/CASA_HP"
```

### ローカルで見た目を確認する
```bash
php -S 127.0.0.1:8787 -t . preview/router.php
```
→ http://localhost:8787/ （`.claude/launch.json` の `casa-preview` でも起動できます）

### 静的プレビューを作り直す
```bash
php preview/build.php "https://hironishimura.github.io/casa-hp-preview" docs
```

### 配布用ZIP（軽量版）を作り直す
```bash
cd wordpress/wp-content/themes
zip -rqX ../../designcasa-shome-lite.zip designcasa-shome \
  -x "designcasa-shome/assets/img/works/*" "designcasa-shome/assets/img/architect/*" \
     "designcasa-shome/assets/img/spec/*" "*.DS_Store"
```

### 公開（GitHub Pages）
```bash
git add -A && git commit -m "..." && git push origin main
```
数分で https://hironishimura.github.io/casa-hp-preview/ に反映されます。

### 全ファイルの構文チェック
```bash
for f in $(find wordpress preview -name '*.php'); do php -l "$f" | grep -v "No syntax"; done
```

---

## 仕組みのポイント

- **写真はテーマに同梱していません。** ZIPを軽くするため、取り込み時に
  GitHub Pages（`DCS_REMOTE_ASSETS`、`functions.php` で定義）からダウンロードして
  メディアライブラリに保存します。取り込み後は外部に依存しません。
  → **プレビューのリポジトリを消すと、未取り込みの環境で写真が入らなくなります。**
- **`preview/` は確認専用**（WordPress関数を最小限だけ真似た土台）。本番には不要。
- **一覧など自動で増える部分はショートコード**（`[dcs_works]` など9種、`inc/blocks.php`）。
  それ以外はすべてブロックなので、ブロックエディタで編集できます。
- 施工例の並び順は「整理番号」（`menu_order`）。数字が大きいほど上。

---

## 未確定・要確認の項目（公開前に必ず）

`inc/config.php` で**空欄にしてあります**。空欄の項目はサイトに表示されません。
「外観 → カスタマイズ → 会社情報」から入力してください。

- 会社成立（設立日）
- 建設業許可番号
- 宅地建物取引業免許番号
- 一級建築士事務所登録番号

> 一度これらを推測値で書いてしまい、事実と異なる情報になるため全て削除した経緯があります。
> **確認できた値以外は書かないこと。**

その他：
- **問い合わせ通知先メール**の設定とテスト送信（未実施）
- 施工例の延床面積・価格・家族構成・竣工年は空欄（分かるものだけ入力）
- 建築家13名の氏名は design casa 公式サイトの掲載時点。公開前に照合

---

## 掲載データの扱い

- 施工例の写真は **design casa（カーサプロジェクト）全国の施工実例**。
  エスホームの自社施工物件ではないため、一覧・詳細・フッターに明記済み。
- **仕様フォルダの価格表・掛率・発注書は社外秘**と判断し、一切掲載していません。
- 建材写真は利用条件の表記に従って選定：
  - KUUMAサウナ「HP・SNS利用可」→ 掲載
  - PLYキッチン `00_製品写真` → 掲載
  - PLY `01_施工事例（SNS利用不可）` → **掲載せず**

---

## 元データ（Box）へのアクセスについて

`/Users/Nishi/Library/CloudStorage/Box-Box/...` は macOS のプライバシー保護により
**コマンドから読めません**（`Operation not permitted`）。
今回は Finder で `casa_HP_data` を作業フォルダ直下にコピーして解決しました。
このコピーは `.gitignore` で除外済み（26GB・社外秘資料を含むため）。

---

## 直近で直した不具合（再発したら疑う場所）

0. **プレビューだけで使っていた仕組みは、本番では動かない**
   施工例タグのローマ字スラッグ対応表が `preview/wp-shim.php` にしかなく、
   本番のWordPressでは日本語スラッグになってタブが全滅していた。
   → **プレビューで正しく見えても、それがテーマ側の実装とは限らない。**
   URLやスラッグを疑うときは `preview/` ではなく `wordpress/` 側を読むこと。
1. **ヘッダーの `backdrop-filter` がメニューを閉じ込めていた**
   `position:fixed` の子要素の基準がヘッダー内に変わる仕様。`::before` に移して解消。
   → `.site-head` に `filter` / `transform` / `backdrop-filter` を**直接付けないこと**。
2. **ブロック化で `.hero` が消え、ヘッダーが常に `is-solid` に**
   `main.js` の判定に `.hero-block` を追加して解消。
3. **メニュー文字が暗い背景に暗い文字**
   `.site-head.is-solid` の詳細度が勝っていた。詳細度を上げて解消。
4. **サブディレクトリ公開でメニューのリンクが全滅**
   `preview/wp-shim.php` の `wp_nav_menu` が `home_url()` を通していなかった。
5. iPad横1024pxで横並びメニューがはみ出す → ハンバーガー切替を1080pxに変更。

---

## 検証のコツ

ブラウザのプレビューペインが非表示のとき、**CSSトランジションが進まない**ため
`getComputedStyle` が古い値を返します。色や開閉を計測するときは先に
`*{transition:none !important}` を注入してください。誤診の原因になります。

また、リンク切れを調べるときは**相対パスも含めて**検査すること
（絶対URLだけ見て、壊れていたヘッダーメニューを見落とした前例あり）。
