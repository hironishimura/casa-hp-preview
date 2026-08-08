# design casa 宇都宮（d-casa.jp）サイト改善 ─ 引き継ぎメモ

最終更新：2026-08-08　テーマ v1.4.0

## ひとことで言うと

WordPressテーマは**完成済み**。GitHub Pages のプレビューは**最新の v1.4.0**（お客さま指摘の内容修正を反映）。
本番 d-casa.jp は **v1.2.0 のまま**。

**進め方は合意済み：修正は GitHub Pages で見ていただき、納得できた段階で本番へまとめて1回反映する。**
毎回WordPressへアップロードするのは負担が大きいため、この形にしています。

---

## 進め方（合意済みの運用フロー）

```
① 修正の依頼を受ける
      ↓
② テーマを直す（wordpress/wp-content/themes/designcasa-shome/）
      ↓
③ php preview/build.php ... docs   → git push
      ↓
④ GitHub Pages で確認していただく（2〜3分で反映・お客さまの作業ゼロ）
      ↓
   ②〜④を必要な回数くり返す
      ↓
⑤ 納得いただけたら、お客さまが d-casa.jp へ1回だけ反映
```

**⑤で伝えること（修正の種類で手順が変わる）**

| 修正の種類 | 本番への反映方法 |
|---|---|
| デザイン・レイアウト・機能（CSS / テンプレート / PHP） | ZIPを入れ替えるだけで反映 |
| **ページの文章・仕様ページ・施工例タグ** | ZIP入れ替え後、**「design casa 初期データ」→「固定ページの文章を作り直す」を1回押す** |

文章はデータベース側に保存されているため、ZIPの入れ替えだけでは古い文章が残ります。ここは毎回案内すること。
v1.4.0 からこのボタンは、固定ページに加えて**仕様ページ（dc_spec）と施工例の絞り込みタグも `data/*.json` の内容で作り直します**（取り込み済みの写真は再利用。JSONから消えた仕様ページは添付写真ごと削除、使われなくなったタグも削除）。

---

## いまどこまで進んでいるか

| 項目 | 状態 |
|---|---|
| WordPressテーマ `designcasa-shome` | **完成**（v1.4.0） |
| 施工例67件・写真659点（1枚ごとにコメント） | **完成** |
| 建築家13名（並び順：内山→敷浪→小倉→深瀬→他9名） | **完成** |
| 家の仕様9項目・建材写真50点（サウナと表札を別項目に分割） | **完成** |
| 全コンテンツのGutenbergブロック化 | **完成** |
| 内容確認用プレビュー（GitHub Pages） | **公開中** |
| **d-casa.jp への設置** | **設置済み**（v1.2.0 が稼働中。v1.4.0 への入れ替え待ち） |

### 公開中のプレビュー
https://hironishimura.github.io/casa-hp-preview/
（noindex + robots.txt で検索エンジンには出ません）

### リポジトリ
https://github.com/hironishimura/casa-hp-preview （public）

---

## 次にやること

**① お客さまに v1.4.0 の修正内容を GitHub Pages で確認していただく**
（2026-08-08 のご指摘13件を反映済み。下の「v1.4.0 で直したこと」参照）

**② 納得いただけたら、お客さまが d-casa.jp のテーマを v1.4.0 に入れ替える**
（`wordpress/designcasa-shome-lite.zip` 2.6MB。手順は `WordPressへの設置手順.md`）

入れ替えたあと、管理画面で **必ず1回だけ**：

1. 「design casa 初期データ」→ **「固定ページの文章を作り直す」** を押す
   （文章修正の反映に加え、仕様ページの再生成＝サウナ／表札の分割・旧「オプション」ページの削除・
   　「天窓」タグの削除もこのボタンで行われます。サウナの写真8点を再取り込みするため数十秒かかります）
2. これでタームのスラッグ修復とパーマリンク再構築も同時に走ります

> **なぜ必要か**：ページの文章はデータベースに保存されているため、
> テーマZIPを入れ替えただけでは古い文章のままです。

**③ 未確定項目の残り**：問い合わせ通知先メールの設定とテスト送信（未実施）

---

## v1.4.0 で直したこと（2026-08-08 お客さま指摘13件）

| 指摘 | 対応 | 直した場所 |
|---|---|---|
| 左上全ページにSEOテキストを入れる | ヘッダー左上に「宇都宮のデザイン注文住宅なら、design casa宇都宮」を全ページ表示 | `header.php`（`.brand__seo`）、`style.css` |
| 耐震等級の「消防署や警察署と同じ」は問題 | 「建築基準法の1.5倍の強さを持つ、住宅性能表示制度の最高等級」に変更 | `data/specs.json`（taishin）、`inc/content.php` |
| 断熱等級6とHEAT20 G2は別物。等級6以上を標準と表記 | 「HEAT20 G2水準」の記述を全削除し「断熱等級6以上」に統一 | `data/specs.json`、`inc/content.php`、`inc/page-content.php`、`inc/seo.php`、各テンプレート |
| 工期の表記修正 | 「契約から着工まで3〜4ヶ月、着工から5〜6ヶ月」に。流れページの一覧と全体目安（1年〜1年3ヶ月）も連動修正 | `inc/content.php`（FAQ）、`inc/page-content.php`（流れ） |
| FAQ「打ち合わせの回数や間取り変更に制限は…」削除 | FAQから削除。辻褄合わせで「制限を設けていません」系の記述もトップの帯・流れ・会社紹介から削除 | `inc/content.php`、`inc/page-content.php` |
| 制振ダンパー evoltz はグレードアップ | 「グレードアップ」明記（一覧の採用製品・詳細ページ両方） | `data/specs.json`（taishin） |
| 「暖房をほとんど使わず13℃を下回りにくい」は言い過ぎ | 削除。「暖房は必要ですが、少ないエネルギーで室温を保ちやすい」に | `data/specs.json`（dannetsu） |
| 気密測定（C値測定）は全棟ではない | 「ご希望に応じて実施」に変更。「全棟で測定」の記述を全削除 | `data/specs.json`、`inc/page-content.php`（会社紹介） |
| 天窓は使わない | 施工例の絞り込みタグ「天窓」を削除、仕様ページの天窓写真2点を削除 | `data/works.json`、`data/specs.json`、`inc/content.php`（対応表） |
| 花粉・PM2.5フィルターは言い過ぎ | 該当文を削除 | `data/specs.json`（zenkan-kucho） |
| サウナと表札は別項目に | 仕様を9項目に分割（`/spec/sauna/`・`/spec/hyosatsu/`。旧 `/spec/option/` は廃止） | `data/specs.json`、`inc/setup-data.php` |
| 松尾式ではなく独自開発のシンプルな空調システム | 「さまざまな空調システムを研究して独自開発した、シンプルでコストを抑えた全館空調」に全面書き換え | `data/specs.json`（zenkan-kucho）、`inc/page-content.php`（会社紹介） |
| 会社情報の追記 | 会社成立=昭和48年10月15日、建設業・宅建業・一級建築士事務所の番号を記入（会社概要の表に表示） | `inc/config.php` |

**しくみの変更**：「固定ページの文章を作り直す」ボタンが、仕様ページ（dc_spec）と施工例タグも
`data/*.json` に合わせて作り直すようになった（新規作成・更新・不要分の削除・menu_order 付与）。
仕様ページの並び順は specs.json の順で `menu_order` に固定。

**確認済み**：プレビュー全170ページ再生成、NGワード（消防署／警察署／HEAT20／G2／13℃／松尾／PM2.5／
全棟で気密／打ち合わせ回数の制限）の残存ゼロをgrepで確認。モバイル・メニュー開閉の色も検証済み。
※ MENUボタンが右に数px切れて見えるのは v1.3.0 以前からの既存挙動（本番も同値）で、今回の変更とは無関係。

---

## v1.3.0 で直したこと

| 症状 | 原因 | 直した場所 |
|---|---|---|
| 施工例の絞り込みタブが全部404 | タームのスラッグが日本語（URLエンコード）で作られていた | `inc/content.php` の `dcs_term_slugs()` にローマ字対応表を追加。`dcs_ensure_terms()` / `dcs_sync_term_slugs()` で付与・修復 |
| 建築家名・電話番号が暗背景に暗文字で読めない | `.entry-content a{color:var(--text)}` が暗いセクション内でも効いていた | `style.css`：暗背景セクション内のリンク色を反転（ボタンは `:not()` で除外） |
| 対応エリアの「・」の位置がずれる | `.entry-content ul{list-style:disc}` が flex 並びの `.area__list` にも当たっていた | `style.css`：テーマ独自の一覧はマーカーと字下げを打ち消す |
| ― | トップの狙いキーワードを「注文住宅 宇都宮」に | `inc/page-content.php` / `inc/content.php` の文章、`inc/seo.php` のタイトル |

### v1.3.0 のGitHub Pages上での検証結果（2026-08-08 実施）

| 確認項目 | 結果 |
|---|---|
| 配信中のテーマ | `Version: 1.3.0`（CSSヘッダーで確認） |
| 施工例の絞り込みタブ | **67本すべて200応答・リンク切れ0**。URLはローマ字（`/works/feature/hiraya/`） |
| 暗背景セクション内のリンク | 建築家名・電話番号ともに `rgb(232,234,227)` で可読。ボタンは橙地×黒文字のまま |
| 対応エリアの一覧 | `list-style:none` / `padding-left:0` でマーカー消去、横並び正常 |
| トップの見出し | h1「デザインも、性能も。どちらも諦めない宇都宮の**注文住宅**。」以下、狙いKWを反映 |

配布ZIP（`wordpress/designcasa-shome-lite.zip` 2.6MB）も v1.3.0 に更新済み。
`dcs_term_slugs` / `dcs_sync_term_slugs` / 「固定ページの文章を作り直す」がZIPに含まれることを確認済み。

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

会社成立・許可番号は **2026-08-08 にお客さまから正式表記をいただき、`inc/config.php` に記入済み**：

- 会社成立：昭和48年10月15日
- 建設業：栃木県知事　許可（般-6）第27166号
- 宅地建物取引業者：栃木県知事（2）第4237号
- 一級建築士事務所：株式会社エスホーム一級建築士事務所　栃木県知事登録（A）第2839号

> かつて推測値を書いて削除した経緯あり。**お客さまから確認できた値以外は書かないこと。**
> なお本番でカスタマイザーの同項目に値を保存済みの場合はそちらが優先される（テーマ既定値は未設定時のみ有効）。

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
