<?php
/**
 * 資料請求・お問い合わせフォーム
 *
 * プラグインなしで動きます。送信内容はメール送信と同時に管理画面にも保存されるため、
 * メールが届かない事故があっても内容が消えません。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * 問い合わせ保存用の投稿タイプを登録する。
 */
function dcs_register_inquiry() {
	register_post_type(
		'dc_inquiry',
		array(
			'label'           => 'お問い合わせ',
			'labels'          => array(
				'name'          => 'お問い合わせ',
				'singular_name' => 'お問い合わせ',
				'all_items'     => 'お問い合わせ一覧',
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_position'   => 26,
			'menu_icon'       => 'dashicons-email-alt',
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'supports'        => array( 'title', 'editor' ),
		)
	);
}
add_action( 'init', 'dcs_register_inquiry' );

/**
 * 問い合わせ内容の選択肢。
 *
 * @return array
 */
function dcs_inquiry_purposes() {
	return array(
		'資料請求（無料）',
		'家づくりの相談がしたい',
		'土地探しから相談したい',
		'施工例・完成見学会を見たい',
		'資金計画・住宅ローンの相談',
		'見積もりがほしい',
	);
}

/**
 * ご検討時期の選択肢。
 *
 * @return array
 */
function dcs_inquiry_timings() {
	return array(
		'すぐにでも',
		'半年以内',
		'1年以内',
		'2〜3年以内',
		'まだ決めていない',
	);
}

/**
 * フォーム送信を処理する。
 */
function dcs_handle_contact() {
	if ( ! isset( $_POST['dcs_contact_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['dcs_contact_nonce'] ) ), 'dcs_contact' ) ) {
		dcs_contact_redirect( 'error' );
	}

	/* ハニーポット（自動投稿よけ）。人間には見えない項目。 */
	if ( ! empty( $_POST['dcs_website'] ) ) {
		dcs_contact_redirect( 'sent' );
	}

	$field = function ( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};

	$name    = $field( 'dcs_name' );
	$kana    = $field( 'dcs_kana' );
	$email   = isset( $_POST['dcs_email'] ) ? sanitize_email( wp_unslash( $_POST['dcs_email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$tel     = $field( 'dcs_tel' );
	$zip     = $field( 'dcs_zip' );
	$address = $field( 'dcs_address' );
	$timing  = $field( 'dcs_timing' );
	$land    = $field( 'dcs_land' );
	$message = isset( $_POST['dcs_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['dcs_message'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	$purposes = array();
	if ( isset( $_POST['dcs_purpose'] ) && is_array( $_POST['dcs_purpose'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$raw      = array_map( 'sanitize_text_field', wp_unslash( $_POST['dcs_purpose'] ) ); // phpcs:ignore
		$purposes = array_values( array_intersect( $raw, dcs_inquiry_purposes() ) );
	}

	if ( ! $name || ! $email || ! is_email( $email ) ) {
		dcs_contact_redirect( 'invalid' );
	}

	/* ---------- 管理画面に保存 ---------- */
	$lines = array(
		'お名前：' . $name,
		'ふりがな：' . $kana,
		'メール：' . $email,
		'電話：' . $tel,
		'郵便番号：' . $zip,
		'ご住所：' . $address,
		'ご希望：' . implode( '／', $purposes ),
		'ご検討時期：' . $timing,
		'土地の状況：' . $land,
		'',
		'【ご要望・ご質問】',
		$message,
	);
	$body = implode( "\n", $lines );

	wp_insert_post(
		array(
			'post_type'    => 'dc_inquiry',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s 様（%s）', $name, gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) ) ), // phpcs:ignore
			'post_content' => $body,
		)
	);

	/* ---------- 会社へ通知 ---------- */
	$to      = dcs_contact_email();
	$subject = sprintf( '【サイトからのお問い合わせ】%s 様', $name );
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);
	wp_mail( $to, $subject, $body . "\n\n---\n送信元: " . home_url( '/contact/' ), $headers );

	/* ---------- お客さまへ自動返信 ---------- */
	$auto = sprintf(
		"%s 様\n\n" .
		"このたびは %s（design casa 宇都宮）へお問い合わせいただき、ありがとうございます。\n" .
		"以下の内容で承りました。担当者より2営業日以内にご連絡いたします。\n" .
		"（火曜・水曜は定休日のため、ご返信が翌営業日になる場合があります）\n\n" .
		"────────────────────\n%s\n────────────────────\n\n" .
		"お急ぎの場合はお電話ください。\n" .
		"フリーダイヤル %s／TEL %s（%s）\n\n" .
		"%s\n〒%s %s\n%s\n",
		$name,
		dcs_company( 'name' ),
		$body,
		dcs_company( 'freedial' ),
		dcs_company( 'tel' ),
		dcs_company( 'hours' ),
		dcs_company( 'name' ),
		dcs_company( 'zip' ),
		dcs_company( 'address' ),
		home_url( '/' )
	);

	wp_mail(
		$email,
		sprintf( '【%s】お問い合わせありがとうございます', dcs_company( 'name' ) ),
		$auto,
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);

	dcs_contact_redirect( 'sent' );
}
add_action( 'template_redirect', 'dcs_handle_contact' );

/**
 * 送信後にリダイレクトする。
 *
 * @param string $status 状態。
 */
function dcs_contact_redirect( $status ) {
	$page = get_page_by_path( 'contact' );
	$url  = $page ? get_permalink( $page ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'dcs', $status, $url ) . '#form' );
	exit;
}

/**
 * 送信結果のメッセージを出力する。
 */
function dcs_contact_message() {
	$status = isset( $_GET['dcs'] ) ? sanitize_key( wp_unslash( $_GET['dcs'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $status ) {
		return;
	}

	$map = array(
		'sent'    => array( 'ok', 'ありがとうございます。送信が完了しました。確認のメールをお送りしていますのでご確認ください。2営業日以内に担当者よりご連絡いたします。' ),
		'invalid' => array( 'ng', 'お名前とメールアドレスをご確認ください。メールアドレスの形式が正しくない可能性があります。' ),
		'error'   => array( 'ng', '送信に失敗しました。お手数ですが、もう一度お試しいただくかお電話ください。' ),
	);

	if ( ! isset( $map[ $status ] ) ) {
		return;
	}

	printf(
		'<div class="formmsg formmsg--%s" role="status">%s</div>',
		esc_attr( $map[ $status ][0] ),
		esc_html( $map[ $status ][1] )
	);
}

/**
 * お問い合わせ一覧に本文の抜粋列を出す。
 *
 * @param array $cols 列。
 * @return array
 */
function dcs_inquiry_columns( $cols ) {
	$new = array();
	foreach ( $cols as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['dcs_summary'] = '内容';
		}
	}

	return $new;
}
add_filter( 'manage_dc_inquiry_posts_columns', 'dcs_inquiry_columns' );

/**
 * お問い合わせ一覧の列を描画する。
 *
 * @param string $col     列名。
 * @param int    $post_id 投稿ID。
 */
function dcs_inquiry_column( $col, $post_id ) {
	if ( 'dcs_summary' === $col ) {
		echo esc_html( mb_substr( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 0, 80 ) . '…' );
	}
}
add_action( 'manage_dc_inquiry_posts_custom_column', 'dcs_inquiry_column', 10, 2 );
