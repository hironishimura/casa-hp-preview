# design casa 宇都宮 ／ 株式会社エスホーム

建築家とつくる注文住宅ブランド **design casa** の加盟工務店・株式会社エスホーム（栃木県宇都宮市）のWebサイト一式です。

## 内容確認用プレビュー

**https://hironishimura.github.io/casa-hp-preview/**

> ⚠️ **これは公開前の内容確認用サイトです。** 正式サイトではありません。
> 検索エンジンには登録されないよう `noindex` と `robots.txt` を設定しています。
> 会社概要の一部（設立日・許可番号など）は未確定のため掲載していません。
> フォームは送信できません（WordPressに設置後に有効になります）。

## このリポジトリの中身

| フォルダ | 内容 |
|---|---|
| `wordpress/wp-content/themes/designcasa-shome/` | **本体。** WordPressテーマ。FTPで設置して有効化するだけで動きます |
| `wordpress/README.md` | 設置手順・SEO設計・公開前チェックリスト |
| `docs/` | 内容確認用の静的プレビュー（169ページ・自動生成物） |
| `preview/` | WordPressなしでテーマを描画するための確認用の仕組み（本番不要） |
| `app/weather/` | **気温・湿度・絶対湿度アプリ。** 静的ファイルのみ。プレビューでは `/weather/` で開けます |
| `scripts/` | Instagram自動投稿と気象データ取り込みのスクリプト |

## 気温・湿度・絶対湿度アプリ

**https://hironishimura.github.io/casa-hp-preview/weather/**

現在地の気温・相対湿度・絶対湿度（容積／重量）・露点を、気象庁のアメダス実況と
数値予報から表示します。窓や壁の表面温度を入れて結露するかどうかを見る機能もあります。

出どころ・仕組み・確認のしかたは `app/weather/README.md` にまとめてあります。

## 掲載写真について

施工例の写真は **design casa（カーサプロジェクト）およびその加盟工務店による施工実例**です。
株式会社エスホームの自社施工物件ではありません。誤認を避けるため、一覧・詳細・フッターにその旨を明記しています。
写真の著作権は各権利者に帰属します。

## プレビューを作り直す

```bash
php preview/build.php https://hironishimura.github.io/casa-hp-preview docs
```

ローカルで確認する場合：

```bash
php -S 127.0.0.1:8787 -t . preview/router.php
```
