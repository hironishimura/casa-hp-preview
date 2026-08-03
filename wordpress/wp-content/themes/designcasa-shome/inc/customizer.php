<?php
/**
 * カスタマイザー（外観 > カスタマイズ）
 *
 * 電話番号・住所などを、コードを触らずに変更できるようにします。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * カスタマイザーの設定を登録する。
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザー。
 */
function dcs_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'dcs_company',
		array(
			'title'       => '会社情報（design casa）',
			'priority'    => 20,
			'description' => 'ここで変更した内容は、ヘッダー・フッター・お問い合わせページ・構造化データにまとめて反映されます。',
		)
	);

	$fields = array(
		'name'      => '会社名',
		'brand'     => 'ブランド表記',
		'zip'       => '郵便番号（ハイフンなし）',
		'address'   => '住所',
		'tel'       => '電話番号',
		'freedial'  => 'フリーダイヤル',
		'fax'       => 'FAX',
		'hours'     => '営業時間',
		'holiday'   => '定休日',
		'founded'   => '設立',
		'ceo'       => '代表者',
		'license_kensetsu'     => '建設業許可',
		'license_takken'       => '宅建業免許',
		'license_kenchikushi'  => '一級建築士事務所登録',
		'business'  => '事業内容',
		'lat'       => '緯度（地図・構造化データ用）',
		'lng'       => '経度（地図・構造化データ用）',
	);

	$defaults = dcs_company_defaults();

	foreach ( $fields as $key => $label ) {
		$wp_customize->add_setting(
			'dcs_company_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'dcs_company_' . $key,
			array(
				'label'   => $label,
				'section' => 'dcs_company',
				'type'    => 'text',
			)
		);
	}

	/* 問い合わせの通知先 */
	$wp_customize->add_setting(
		'dcs_contact_email',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		)
	);
	$wp_customize->add_control(
		'dcs_contact_email',
		array(
			'label'       => 'お問い合わせの通知先メールアドレス',
			'description' => '空欄の場合は「設定 > 一般」の管理者メールアドレスに届きます。',
			'section'     => 'dcs_company',
			'type'        => 'email',
		)
	);

	/* Googleマップの埋め込みURL */
	$wp_customize->add_setting(
		'dcs_map_src',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'dcs_map_src',
		array(
			'label'       => 'Googleマップの埋め込みURL',
			'description' => 'Googleマップ →「共有」→「地図を埋め込む」で表示される iframe の src= の中身（https://www.google.com/maps/embed?… ）を貼り付けてください。',
			'section'     => 'dcs_company',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'dcs_customize_register' );

/**
 * 地図の埋め込みURLを返す。
 *
 * @return string
 */
function dcs_map_src() {
	$src = get_theme_mod( 'dcs_map_src', '' );
	if ( $src ) {
		return $src;
	}

	/* 未設定のときは住所検索の埋め込みにフォールバックする */
	return 'https://maps.google.co.jp/maps?output=embed&q=' . rawurlencode( '栃木県宇都宮市平出町3563-3' ) . '&z=16';
}
