<?php
/**
 * サイト共通の設定値
 *
 * ここを書き換えるだけで、電話番号・住所・営業時間などが全ページに反映されます。
 * （WordPress管理画面「外観 > カスタマイズ」からも編集できます）
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * 会社情報のデフォルト値。
 *
 * @return array
 */
function dcs_company_defaults() {
	return array(
		'name'        => '株式会社エスホーム',
		'name_en'     => 'S HOME Co., Ltd.',
		'brand'       => 'design casa 宇都宮',
		'zip'         => '321-0901',
		'address'     => '栃木県宇都宮市平出町3563-3',
		'tel'         => '028-613-6606',
		'freedial'    => '0120-934-606',
		'fax'         => '028-613-6616',
		'hours'       => '9:00〜17:00',
		'holiday'     => '毎週火曜日・水曜日（事前にご連絡いただければ対応可能です）',
		/* ↓ 公開前に社内の正式表記を入力してください。空欄の項目はサイトに表示されません。 */
		'founded'     => '',
		'ceo'         => '西村',
		'license_kensetsu' => '',
		'license_takken'   => '',
		'license_kenchikushi' => '',
		'business'    => '注文住宅の設計・施工／リノベーション／土地仲介',
		'lat'         => '36.5741',
		'lng'         => '139.9241',
	);
}

/**
 * 会社情報を1件取得する。
 *
 * @param string $key キー。
 * @return string
 */
function dcs_company( $key ) {
	$defaults = dcs_company_defaults();
	$value    = get_theme_mod( 'dcs_company_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );

	return $value;
}

/**
 * 電話番号を tel: リンク用に整形する。
 *
 * @param string $tel 電話番号。
 * @return string
 */
function dcs_tel_href( $tel ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $tel );
}

/**
 * 対応エリア（メイン）。
 *
 * @return array
 */
function dcs_areas_main() {
	return array(
		'宇都宮市', '鹿沼市', 'さくら市', '矢板市', '大田原市', '那須烏山市',
		'真岡市', '下野市', '塩谷町', '高根沢町', '芳賀町', '市貝町',
		'茂木町', '益子町', '上三川町', '壬生町',
	);
}

/**
 * 対応エリア（要相談）。
 *
 * @return array
 */
function dcs_areas_sub() {
	return array( '日光市', '那須塩原市', '小山市', '栃木市', '那珂川町' );
}

/**
 * 問い合わせ先メールアドレス。
 *
 * @return string
 */
function dcs_contact_email() {
	$mod = get_theme_mod( 'dcs_contact_email', '' );

	return $mod ? $mod : get_option( 'admin_email' );
}
