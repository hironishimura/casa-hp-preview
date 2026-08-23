<?php
/**
 * design casa 宇都宮 / 株式会社エスホーム 公式テーマ
 *
 * FTPでこのフォルダごと wp-content/themes/ にアップロードし、
 * 「外観 > テーマ」で有効化すると、固定ページ・メニュー・施工例などが自動生成されます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

define( 'DCS_VERSION', '1.4.4' );

/**
 * 画像をテーマに同梱しない場合の取得元。
 *
 * テーマZIPを軽くするため、初期データの写真はここから取り込みます。
 * テーマ内に assets/img/ がある場合は、そちらが優先されます。
 */
if ( ! defined( 'DCS_REMOTE_ASSETS' ) ) {
	define( 'DCS_REMOTE_ASSETS', 'https://hironishimura.github.io/casa-hp-preview/theme' );
}

require_once get_theme_file_path( 'inc/config.php' );
require_once get_theme_file_path( 'inc/cpt.php' );
require_once get_theme_file_path( 'inc/meta.php' );
require_once get_theme_file_path( 'inc/breadcrumb.php' );
require_once get_theme_file_path( 'inc/seo.php' );
require_once get_theme_file_path( 'inc/content.php' );
require_once get_theme_file_path( 'inc/blocks.php' );
require_once get_theme_file_path( 'inc/page-content.php' );
require_once get_theme_file_path( 'inc/contact-form.php' );
require_once get_theme_file_path( 'inc/customizer.php' );
require_once get_theme_file_path( 'inc/setup-data.php' );

/**
 * テーマの基本サポート設定。
 */
function dcs_theme_setup() {
	load_theme_textdomain( 'designcasa-shome', get_theme_file_path( 'languages' ) );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	add_image_size( 'dcs-card', 900, 600, true );      // 一覧カード用.
	add_image_size( 'dcs-wide', 1600, 900, true );     // ヒーロー・大判用.
	add_image_size( 'dcs-tall', 900, 1200, true );     // 縦位置ギャラリー用.

	register_nav_menus(
		array(
			'primary' => 'ヘッダーメニュー',
			'footer'  => 'フッターメニュー',
		)
	);
}
add_action( 'after_setup_theme', 'dcs_theme_setup' );

/**
 * フロント側のCSS・JSを読み込む。
 */
function dcs_enqueue_assets() {
	/* Google Fonts（明朝＝見出し／ゴシック＝本文／等幅＝数値） */
	wp_enqueue_style(
		'dcs-fonts',
		'https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;600;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&family=IBM+Plex+Mono:wght@400;500&display=swap',
		array(),
		null // phpcs:ignore
	);

	wp_enqueue_style( 'dcs-style', get_stylesheet_uri(), array( 'dcs-fonts' ), DCS_VERSION );

	wp_enqueue_script( 'dcs-main', get_theme_file_uri( 'assets/js/main.js' ), array(), DCS_VERSION, true );
	wp_localize_script(
		'dcs-main',
		'DCS',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dcs_enqueue_assets' );

/**
 * フォント配信元へ先に接続しておく。
 *
 * @param array  $urls          URL群。
 * @param string $relation_type 種別。
 * @return array
 */
function dcs_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'dcs_resource_hints', 10, 2 );

/**
 * 不要な出力を止めて表示を軽くする。
 */
function dcs_cleanup_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'dcs_cleanup_head' );

/**
 * 画像に遅延読み込みと復号ヒントを付ける（体感速度＝SEOに効く）。
 *
 * @param array $attr 属性。
 * @return array
 */
function dcs_image_attr( $attr ) {
	if ( ! isset( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'dcs_image_attr' );

/**
 * 抜粋の文字数と末尾。
 *
 * @return int
 */
function dcs_excerpt_length() {
	return 90;
}
add_filter( 'excerpt_length', 'dcs_excerpt_length' );
add_filter( 'excerpt_mblength', 'dcs_excerpt_length' );

/**
 * 抜粋の末尾記号。
 *
 * @return string
 */
function dcs_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'dcs_excerpt_more' );

/**
 * 施工例カードを描画する。
 *
 * @param int|WP_Post $post 投稿。
 */
function dcs_work_card( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}
	$id    = $post->ID;
	$area  = dcs_meta( 'dcs_work_area', $id );
	$str   = dcs_meta( 'dcs_work_structure', $id );
	$floor = dcs_meta( 'dcs_work_floor', $id );
	$terms = get_the_terms( $id, 'dc_work_tag' );
	?>
	<a class="card" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
		<figure class="card__fig">
			<?php if ( has_post_thumbnail( $id ) ) : ?>
				<?php echo get_the_post_thumbnail( $id, 'dcs-card', array( 'alt' => esc_attr( get_the_title( $id ) . '｜' . $area . 'の注文住宅 施工例' ) ) ); ?>
			<?php endif; ?>
		</figure>
		<div class="card__body">
			<h3 class="card__title"><?php echo esc_html( get_the_title( $id ) ); ?></h3>
			<p class="card__meta">
				<?php if ( $str ) : ?><span class="card__type"><?php echo esc_html( $str ); ?></span><?php endif; ?>
				<?php if ( $area ) : ?><span><?php echo esc_html( $area ); ?></span><?php endif; ?>
			</p>
			<?php if ( $floor ) : ?><p class="card__price"><?php echo esc_html( $floor ); ?></p><?php endif; ?>
			<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
				<p class="card__tags">
					<?php echo esc_html( implode( '　/　', wp_list_pluck( array_slice( $terms, 0, 4 ), 'name' ) ) ); ?>
				</p>
			<?php endif; ?>
		</div>
	</a>
	<?php
}

/**
 * ページ下部の共通CTAを描画する。
 */
function dcs_cta() {
	get_template_part( 'parts/cta' );
}

/**
 * 定義リストの1行を描画する（値が空なら何も出さない）。
 *
 * 会社情報の未入力項目を「空欄のまま公開してしまう」事故を防ぎます。
 *
 * @param string $label 見出し。
 * @param string $value 値（HTML可）。
 */
function dcs_dl_row( $label, $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return;
	}
	printf( '<div><dt>%s</dt><dd>%s</dd></div>', esc_html( $label ), $value ); // phpcs:ignore WordPress.Security.EscapeOutput
}
