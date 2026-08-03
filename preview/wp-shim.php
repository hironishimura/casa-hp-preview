<?php
/**
 * プレビュー専用の WordPress 互換シム
 *
 * WordPressを入れずに、テーマのテンプレートをそのまま描画して見た目を確認するための
 * 「最小限のWordPressのふり」をするファイルです。**本番では使いません。**
 * preview/ フォルダごと削除して問題ありません。
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'DCS_THEME_DIR', dirname( __DIR__ ) . '/wordpress/wp-content/themes/designcasa-shome' );

/* DCS_SITE / DCS_ASSET_BASE / DCS_BUILD は build.php 側で先に定義される場合がある */
if ( ! defined( 'DCS_SITE' ) ) { define( 'DCS_SITE', '' ); }
if ( ! defined( 'DCS_ASSET_BASE' ) ) { define( 'DCS_ASSET_BASE', '/wordpress/wp-content/themes/designcasa-shome' ); }
if ( ! defined( 'DCS_BUILD' ) ) { define( 'DCS_BUILD', false ); }

define( 'DCS_THEME_URI', DCS_SITE . DCS_ASSET_BASE );

/**
 * 施工例タグの URL 用スラッグ（日本語をURLに出さないための対応表）。
 *
 * @return array
 */
function dcs_tag_slugs() {
	return array(
		'2階建て' => '2kai', '平屋' => 'hiraya', '木天井' => 'ki-tenjo',
		'スケルトン階段' => 'skeleton-kaidan', '畳コーナー' => 'tatami-corner',
		'吹き抜け' => 'fukinuke', 'ウッドデッキ' => 'wood-deck', 'ガレージ' => 'garage',
		'中庭' => 'nakaniwa', '勾配天井' => 'kobai-tenjo', 'アイランドキッチン' => 'island-kitchen',
		'眺望' => 'chobo', '大開口' => 'dai-kaiko', '2階リビング' => '2kai-living',
		'書斎' => 'shosai', '小上がり' => 'koagari', '小上がり畳' => 'koagari-tatami',
		'造作カウンター' => 'zosaku-counter', 'バルコニー' => 'balcony', '土間' => 'doma',
		'造作本棚' => 'zosaku-hondana', 'ダークトーン' => 'dark-tone', 'パントリー' => 'pantry',
		'造作洗面' => 'zosaku-senmen', '室内窓' => 'shitsunai-mado', 'スタディコーナー' => 'study-corner',
		'カーポート' => 'carport', '間接照明' => 'kansetsu-shomei', '対面キッチン' => 'taimen-kitchen',
		'庭' => 'niwa', '造作棚' => 'zosaku-tana', '回遊動線' => 'kaiyu-dosen',
		'ルーフテラス' => 'roof-terrace', 'ファミリークローゼット' => 'family-closet',
		'梁見せ' => 'hari-mise', '板塀' => 'ita-bei', 'ネイビー' => 'navy',
		'造作家具' => 'zosaku-kagu', '併用住宅' => 'heiyo-jutaku', '深い軒' => 'fukai-noki',
		'造作ソファ' => 'zosaku-sofa', '縁側' => 'engawa', 'ジャパンディ' => 'japandi',
		'コの字' => 'kono-ji', 'ランドリー' => 'laundry', '土間収納' => 'doma-shuno',
		'ルーフバルコニー' => 'roof-balcony', 'スカイバス' => 'sky-bath', '窓辺ベンチ' => 'madobe-bench',
		'大屋根' => 'oyane', '狭小地' => 'kyoshochi', '高窓' => 'takamado',
		'ボルダリング' => 'bouldering', 'スキップフロア' => 'skip-floor', '造作ベンチ' => 'zosaku-bench',
		'天窓' => 'tenmado', 'アプローチ' => 'approach', 'モルタル' => 'mortar',
		'ジャグジー' => 'jacuzzi', '夜景' => 'yakei', '趣味室' => 'shumi-shitsu',
		'木ルーバー' => 'ki-louver', 'ロフト' => 'loft', '変形地' => 'henkeichi',
		'ハンモック' => 'hammock', '柱見せ' => 'hashira-mise',
	);
}

/* =========================================================
   データストア
   ========================================================= */
$GLOBALS['dcs_posts'] = array();   // ID => post object
$GLOBALS['dcs_att']   = array();   // ID => attachment
$GLOBALS['dcs_terms'] = array();   // taxonomy => slug => term
$GLOBALS['dcs_next']  = 1;

/**
 * 投稿オブジェクトを作る。
 */
function dcs_pv_post( $args ) {
	$p = (object) array_merge(
		array(
			'ID'           => $GLOBALS['dcs_next']++,
			'post_title'   => '',
			'post_name'    => '',
			'post_type'    => 'post',
			'post_content' => '',
			'post_excerpt' => '',
			'menu_order'   => 0,
			'post_date'    => '2026-04-01 10:00:00',
			'meta'         => array(),
			'terms'        => array(),
			'thumb'        => 0,
		),
		$args
	);
	$GLOBALS['dcs_posts'][ $p->ID ] = $p;

	return $p;
}

/**
 * 添付ファイルを作る。
 */
function dcs_pv_att( $rel, $caption, $alt ) {
	$id = $GLOBALS['dcs_next']++;
	$GLOBALS['dcs_att'][ $id ] = (object) array(
		'ID'      => $id,
		'url'     => DCS_THEME_URI . '/assets/img/' . ltrim( $rel, '/' ),
		'caption' => $caption,
		'alt'     => $alt,
	);

	return $id;
}

/**
 * タームを作る。
 */
function dcs_pv_term( $taxonomy, $name ) {
	$map  = dcs_tag_slugs();
	$slug = isset( $map[ $name ] ) ? $map[ $name ] : rawurlencode( $name );
	if ( isset( $GLOBALS['dcs_terms'][ $taxonomy ][ $name ] ) ) {
		return $GLOBALS['dcs_terms'][ $taxonomy ][ $name ];
	}
	$t = (object) array(
		'term_id'     => $GLOBALS['dcs_next']++,
		'name'        => $name,
		'slug'        => $slug,
		'taxonomy'    => $taxonomy,
		'count'       => 0,
		'description' => '',
	);
	$GLOBALS['dcs_terms'][ $taxonomy ][ $name ] = $t;

	return $t;
}

/* =========================================================
   エスケープ・サニタイズ
   ========================================================= */
function esc_html( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_url_raw( $t ) { return (string) $t; }
function esc_textarea( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_js( $t ) { return addslashes( (string) $t ); }
function esc_html__( $t, $d = '' ) { return esc_html( $t ); }
function __( $t, $d = '' ) { return $t; }
function _e( $t, $d = '' ) { echo $t; } // phpcs:ignore
function wp_kses_post( $t ) { return $t; }
function wp_strip_all_tags( $t ) { return strip_tags( (string) $t ); }
function sanitize_text_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_textarea_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_email( $t ) { return filter_var( (string) $t, FILTER_SANITIZE_EMAIL ); }
function sanitize_key( $t ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $t ) ); }
function sanitize_title( $t ) { return rawurlencode( (string) $t ); }
function wp_unslash( $t ) { return $t; }
function absint( $t ) { return abs( (int) $t ); }
function is_email( $t ) { return (bool) filter_var( $t, FILTER_VALIDATE_EMAIL ); }
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }
function is_wp_error( $t ) { return false; }
function selected( $a, $b, $echo = true ) { $r = ( (string) $a === (string) $b ) ? ' selected' : ''; if ( $echo ) { echo $r; } return $r; }
function checked( $a, $b = true, $echo = true ) { $r = ( $a == $b ) ? ' checked' : ''; if ( $echo ) { echo $r; } return $r; } // phpcs:ignore

/* =========================================================
   フック・設定（すべて何もしない）
   ========================================================= */
function add_action() {}
function add_filter() {}
function remove_action() {}
function remove_filter() {}
function do_action() {}
function apply_filters( $tag, $value ) { return $value; }
function add_theme_support() {}
function load_theme_textdomain() {}
function add_image_size() {}
function register_nav_menus() {}
function register_post_type() {}
function register_taxonomy() {}
function add_meta_box() {}
function add_management_page() {}
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function wp_localize_script() {}
function wp_add_inline_style() {}
function wp_enqueue_media() {}
function flush_rewrite_rules() {}
function wp_create_nav_menu( $n ) { return 1; }
function is_nav_menu( $n ) { return true; }
function wp_update_nav_menu_item() {}
function set_theme_mod() {}
function update_option() {}
function delete_option() {}
function update_post_meta() {}
function wp_insert_post( $a ) { return 1; }
function wp_update_post( $a ) { return 1; }
function set_post_thumbnail() {}
function wp_set_object_terms() {}
function wp_mail() { return true; }
function current_user_can() { return false; }
function wp_nonce_field( $a = '', $b = '' ) { echo '<input type="hidden" name="' . esc_attr( $b ) . '" value="preview">'; }
function wp_create_nonce( $a = '' ) { return 'preview'; }
function wp_verify_nonce() { return false; }
function current_time( $t ) { return time(); }
function get_search_query() { return isset( $_GET['s'] ) ? $_GET['s'] : ''; }
function comments_open() { return false; }
function get_option( $k, $d = '' ) {
	$map = array(
		'blogname'           => 'design casa 宇都宮｜株式会社エスホーム',
		'blogdescription'    => '建築家とつくる注文住宅 design casa の宇都宮加盟工務店',
		'admin_email'        => 'info@shome.co.jp',
		'permalink_structure' => '/%postname%/',
	);

	return isset( $map[ $k ] ) ? $map[ $k ] : $d;
}
function get_theme_mod( $k, $d = '' ) { return $d; }
function get_bloginfo( $k = 'name' ) {
	if ( 'description' === $k ) { return get_option( 'blogdescription' ); }
	if ( 'charset' === $k ) { return 'UTF-8'; }

	return get_option( 'blogname' );
}
function bloginfo( $k = 'name' ) { echo esc_html( get_bloginfo( $k ) ); }
function language_attributes() { echo 'lang="ja"'; }
function wp_get_theme() {
	return new class {
		public function get( $k ) { return '1.0.0'; }
	};
}

/* =========================================================
   URL・パス
   ========================================================= */
function home_url( $p = '/' ) { return DCS_SITE . ( '' === $p ? '/' : $p ); }
function admin_url( $p = '' ) { return '/wp-admin/' . $p; }
function site_url( $p = '/' ) { return $p; }
function get_theme_file_uri( $f = '' ) { return DCS_THEME_URI . ( $f ? '/' . ltrim( $f, '/' ) : '' ); }
function get_theme_file_path( $f = '' ) { return DCS_THEME_DIR . ( $f ? '/' . ltrim( $f, '/' ) : '' ); }
function get_stylesheet_uri() { return DCS_THEME_URI . '/style.css'; }
function add_query_arg( $a, $b = null ) { return is_array( $a ) ? '' : '?' . $a . '=' . rawurlencode( $b ); }
function wp_safe_redirect( $u ) { header( 'Location: ' . $u ); }

function get_permalink( $p = null ) {
	$p = get_post( $p );
	if ( ! $p ) { return home_url( '/' ); }
	switch ( $p->post_type ) {
		case 'dc_work':      return home_url( '/works/' . $p->post_name . '/' );
		case 'dc_architect': return home_url( '/architect/' . $p->post_name . '/' );
		case 'dc_spec':      return home_url( '/spec/' . $p->post_name . '/' );
	}

	return home_url( '/' . $p->post_name . '/' );
}
function get_post_type_archive_link( $t ) {
	$map = array( 'dc_work' => '/works/', 'dc_architect' => '/architect/', 'dc_spec' => '/spec/' );

	return home_url( isset( $map[ $t ] ) ? $map[ $t ] : '/' );
}
function get_term_link( $t ) {
	if ( 'dc_work_tag' === $t->taxonomy ) { return home_url( '/works/feature/' . $t->slug . '/' ); }

	return home_url( '/spec/category/' . $t->slug . '/' );
}

/* =========================================================
   テンプレート
   ========================================================= */
function get_header() { include DCS_THEME_DIR . '/header.php'; }
function get_footer() { include DCS_THEME_DIR . '/footer.php'; }
function get_template_part( $slug, $name = null ) {
	$f = DCS_THEME_DIR . '/' . $slug . ( $name ? '-' . $name : '' ) . '.php';
	if ( file_exists( $f ) ) { include $f; }
}
function wp_head() {
	echo '<title>' . esc_html( dcs_seo_context()['title'] ) . "</title>\n";
	dcs_head_meta();
	dcs_jsonld();
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;600;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&family=IBM+Plex+Mono:wght@400;500&display=swap">' . "\n";
	echo '<link rel="stylesheet" href="' . esc_url( get_stylesheet_uri() ) . '?v=' . DCS_VERSION . "\">\n";
}
function wp_footer() {
	echo '<script src="' . esc_url( get_theme_file_uri( 'assets/js/main.js' ) ) . '"></script>' . "\n";

	if ( DCS_BUILD ) {
		/* 静的プレビューではフォーム送信ができないことを伝える */
		echo '<script>document.addEventListener("submit",function(e){e.preventDefault();alert("これは内容確認用のプレビューサイトです。\nフォームの送信はWordPressに設置してからご利用いただけます。");});</script>' . "\n";
	}

	echo '<div class="pv-badge">' . ( DCS_BUILD ? '内容確認用プレビュー（WordPress設置前）' : 'PREVIEW（ローカル・WordPress未使用）' ) . '</div>' . "\n";
	echo '<style>.pv-badge{position:fixed;left:0;bottom:0;z-index:9999;background:#101418;color:#E0A24B;font-family:"IBM Plex Mono",ui-monospace,monospace;font-size:10.5px;padding:7px 12px;letter-spacing:.08em}@media(max-width:860px){.pv-badge{bottom:52px}}</style>' . "\n";
}
function wp_body_open() {}
function body_class( $c = '' ) { echo 'class="' . esc_attr( is_array( $c ) ? implode( ' ', $c ) : $c ) . '"'; }

function has_nav_menu( $loc ) { return true; }
function wp_nav_menu( $args ) {
	$items = ( 'primary' === $args['theme_location'] )
		? array(
			array( '/concept/', 'デザインカーサとは' ),
			array( '/works/', '施工例' ),
			array( '/architect/', '建築家紹介' ),
			array( '/flow/', '家づくりの流れ' ),
			array( '/spec/', '家の仕様' ),
			array( '/company/', '施工会社紹介' ),
		)
		: array(
			array( '/concept/', 'デザインカーサとは' ),
			array( '/works/', '施工例' ),
			array( '/architect/', '建築家紹介' ),
			array( '/flow/', '家づくりの流れ' ),
			array( '/spec/', '家の仕様' ),
			array( '/company/', '施工会社紹介' ),
			array( '/contact/', '資料請求・お問い合わせ' ),
			array( '/privacy/', 'プライバシーポリシー' ),
		);

	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '">';
	foreach ( $items as $i ) {
		/* home_url() を通さないと、サブディレクトリ公開時にリンクが壊れる */
		echo '<li><a href="' . esc_url( home_url( $i[0] ) ) . '">' . esc_html( $i[1] ) . '</a></li>';
	}
	echo '</ul>';
}

/* =========================================================
   ループ
   ========================================================= */
class WP_Query {
	public $posts = array();
	public $current = -1;
	public $post = null;
	public $found_posts = 0;

	public function __construct( $args = array() ) {
		$this->posts       = dcs_pv_query( $args );
		$this->found_posts = count( $this->posts );
	}
	public function have_posts() { return $this->current + 1 < count( $this->posts ); }
	public function the_post() {
		$this->current++;
		$this->post = $this->posts[ $this->current ];
		$GLOBALS['post'] = $this->post;
	}
}

/**
 * 疑似クエリ。
 */
function dcs_pv_query( $args ) {
	$type = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
	$out  = array();
	foreach ( $GLOBALS['dcs_posts'] as $p ) {
		if ( $p->post_type !== $type ) { continue; }
		if ( ! empty( $args['post__not_in'] ) && in_array( $p->ID, $args['post__not_in'], true ) ) { continue; }
		if ( ! empty( $args['name'] ) && $p->post_name !== $args['name'] ) { continue; }
		if ( ! empty( $args['meta_query'] ) ) {
			$mq = $args['meta_query'][0];
			$v  = isset( $p->meta[ $mq['key'] ] ) ? $p->meta[ $mq['key'] ] : '';
			if ( (string) $v !== (string) $mq['value'] ) { continue; }
		}
		if ( ! empty( $args['tax_query'] ) ) {
			$tq   = $args['tax_query'][0];
			$hit  = false;
			foreach ( $p->terms as $t ) {
				if ( in_array( $t->term_id, (array) $tq['terms'], true ) ) { $hit = true; break; }
			}
			if ( ! $hit ) { continue; }
		}
		$out[] = $p;
	}

	$order = isset( $args['orderby'] ) ? $args['orderby'] : '';
	if ( is_array( $order ) && isset( $order['menu_order'] ) ) {
		usort( $out, function ( $a, $b ) { return $b->menu_order <=> $a->menu_order; } );
	} elseif ( 'rand' === $order ) {
		shuffle( $out );
	} else {
		usort( $out, function ( $a, $b ) { return $a->menu_order <=> $b->menu_order; } );
	}

	$n = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : -1;
	if ( $n > 0 ) { $out = array_slice( $out, 0, $n ); }

	return $out;
}

function get_posts( $args = array() ) {
	$r = dcs_pv_query( $args );
	if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {
		return array_map( function ( $p ) { return $p->ID; }, $r );
	}

	return $r;
}

function have_posts() { return $GLOBALS['dcs_main']->have_posts(); }
function the_post() { $GLOBALS['dcs_main']->the_post(); }
function wp_reset_postdata() { $GLOBALS['post'] = isset( $GLOBALS['dcs_main']->post ) ? $GLOBALS['dcs_main']->post : null; }

function get_post( $p = null ) {
	if ( is_object( $p ) ) { return $p; }
	if ( is_numeric( $p ) && isset( $GLOBALS['dcs_posts'][ $p ] ) ) { return $GLOBALS['dcs_posts'][ $p ]; }

	return isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
}
function get_the_ID() { $p = get_post(); return $p ? $p->ID : 0; }
function get_post_type( $p = null ) { $p = get_post( $p ); return $p ? $p->post_type : ''; }
function get_the_title( $p = null ) { $p = get_post( $p ); return $p ? $p->post_title : ''; }
function the_title() { echo esc_html( get_the_title() ); }
function get_the_content() { $p = get_post(); return $p ? $p->post_content : ''; }
function the_content() { echo get_the_content(); } // phpcs:ignore
function the_permalink() { echo esc_url( get_permalink() ); }
function has_excerpt( $p = null ) { $p = get_post( $p ); return $p && '' !== $p->post_excerpt; }
function get_the_excerpt( $p = null ) { $p = get_post( $p ); return $p ? $p->post_excerpt : ''; }
function get_post_field( $f, $p = null ) { $p = get_post( $p ); return ( $p && isset( $p->$f ) ) ? $p->$f : ''; }
function get_the_date( $f = 'Y.m.d', $p = null ) { $p = get_post( $p ); return date( $f, strtotime( $p ? $p->post_date : 'now' ) ); }
function get_post_ancestors( $p = null ) { return array(); }
function get_page_by_path( $slug ) {
	foreach ( $GLOBALS['dcs_posts'] as $p ) {
		if ( 'page' === $p->post_type && $p->post_name === $slug ) { return $p; }
	}

	return null;
}
function get_previous_post() { return dcs_pv_sibling( -1 ); }
function get_next_post() { return dcs_pv_sibling( 1 ); }
function dcs_pv_sibling( $dir ) {
	$cur  = get_post();
	if ( ! $cur ) { return null; }
	$list = dcs_pv_query( array( 'post_type' => $cur->post_type, 'orderby' => array( 'menu_order' => 'DESC' ), 'posts_per_page' => -1 ) );
	foreach ( $list as $i => $p ) {
		if ( $p->ID === $cur->ID ) {
			$j = $i + $dir;

			return isset( $list[ $j ] ) ? $list[ $j ] : null;
		}
	}

	return null;
}

function get_post_meta( $id, $key, $single = true ) {
	$p = get_post( $id );

	return ( $p && isset( $p->meta[ $key ] ) ) ? $p->meta[ $key ] : '';
}
function get_the_terms( $id, $tax ) {
	$p = get_post( $id );
	if ( ! $p ) { return false; }
	$r = array_values( array_filter( $p->terms, function ( $t ) use ( $tax ) { return $t->taxonomy === $tax; } ) );

	return $r ? $r : false;
}
function get_terms( $args ) {
	$tax = $args['taxonomy'];
	$r   = isset( $GLOBALS['dcs_terms'][ $tax ] ) ? array_values( $GLOBALS['dcs_terms'][ $tax ] ) : array();
	usort( $r, function ( $a, $b ) { return $b->count <=> $a->count; } );
	if ( ! empty( $args['number'] ) ) { $r = array_slice( $r, 0, (int) $args['number'] ); }

	return $r;
}
function wp_list_pluck( $list, $field ) {
	return array_map( function ( $i ) use ( $field ) { return is_object( $i ) ? $i->$field : $i[ $field ]; }, $list );
}

/* =========================================================
   画像
   ========================================================= */
function dcs_pv_size( $size ) {
	$map = array(
		'dcs-card'  => array( 900, 600 ),
		'dcs-wide'  => array( 1600, 900 ),
		'dcs-tall'  => array( 900, 1200 ),
		'thumbnail' => array( 150, 150 ),
		'full'      => array( 1600, 1067 ),
	);

	return isset( $map[ $size ] ) ? $map[ $size ] : array( 1600, 1067 );
}
function has_post_thumbnail( $p = null ) { $p = get_post( $p ); return $p && $p->thumb; }
function get_post_thumbnail_id( $p = null ) { $p = get_post( $p ); return $p ? $p->thumb : 0; }
function wp_get_attachment_image( $id, $size = 'full', $icon = false, $attr = array() ) {
	if ( empty( $GLOBALS['dcs_att'][ $id ] ) ) { return ''; }
	$a = $GLOBALS['dcs_att'][ $id ];
	list( $w, $h ) = dcs_pv_size( $size );
	$alt = isset( $attr['alt'] ) && $attr['alt'] ? $attr['alt'] : $a->alt;
	$ld  = isset( $attr['loading'] ) ? $attr['loading'] : 'lazy';

	return sprintf(
		'<img src="%s" alt="%s" width="%d" height="%d" loading="%s" decoding="async">',
		esc_url( $a->url ), esc_attr( $alt ), $w, $h, esc_attr( $ld )
	);
}
function wp_get_attachment_image_src( $id, $size = 'full' ) {
	if ( empty( $GLOBALS['dcs_att'][ $id ] ) ) { return false; }
	list( $w, $h ) = dcs_pv_size( $size );

	return array( $GLOBALS['dcs_att'][ $id ]->url, $w, $h );
}
function wp_get_attachment_caption( $id ) {
	return isset( $GLOBALS['dcs_att'][ $id ] ) ? $GLOBALS['dcs_att'][ $id ]->caption : '';
}
function get_the_post_thumbnail( $p = null, $size = 'full', $attr = array() ) {
	return wp_get_attachment_image( get_post_thumbnail_id( $p ), $size, false, $attr );
}
function the_post_thumbnail( $size = 'full', $attr = array() ) { echo get_the_post_thumbnail( null, $size, $attr ); } // phpcs:ignore
function get_the_post_thumbnail_url( $p = null, $size = 'full' ) {
	$s = wp_get_attachment_image_src( get_post_thumbnail_id( $p ), $size );

	return $s ? $s[0] : '';
}

/* =========================================================
   条件分岐
   ========================================================= */
function dcs_ctx( $k ) { return isset( $GLOBALS['dcs_ctx'][ $k ] ) ? $GLOBALS['dcs_ctx'][ $k ] : null; }
function is_front_page() { return 'front' === dcs_ctx( 'type' ); }
function is_home() { return false; }
function is_admin() { return false; }
function is_feed() { return false; }
function is_search() { return 'search' === dcs_ctx( 'type' ); }
function is_404() { return '404' === dcs_ctx( 'type' ); }
function is_singular( $t = null ) {
	if ( ! in_array( dcs_ctx( 'type' ), array( 'single', 'page' ), true ) ) { return false; }

	return $t ? get_post_type() === $t : true;
}
function is_page( $s = null ) {
	if ( 'page' !== dcs_ctx( 'type' ) ) { return false; }
	$p = get_post();

	return $s ? ( $p && $p->post_name === $s ) : true;
}
function is_post_type_archive( $t = null ) {
	if ( 'archive' !== dcs_ctx( 'type' ) ) { return false; }

	return $t ? dcs_ctx( 'post_type' ) === $t : true;
}
function is_tax( $t = null ) {
	if ( 'tax' !== dcs_ctx( 'type' ) ) { return false; }

	return $t ? dcs_ctx( 'taxonomy' ) === $t : true;
}
function is_archive() { return in_array( dcs_ctx( 'type' ), array( 'archive', 'tax' ), true ); }
function is_category() { return false; }
function is_tag() { return false; }
function get_queried_object() { return dcs_ctx( 'object' ); }
function get_query_var( $v ) { return dcs_ctx( 'post_type' ); }
function single_term_title( $p = '', $echo = true ) {
	$t = get_queried_object();
	$r = $t ? $t->name : '';
	if ( $echo ) { echo esc_html( $r ); }

	return $r;
}
function the_archive_title() { echo esc_html( '一覧' ); }
function wp_get_document_title() { return get_bloginfo( 'name' ); }

/* =========================================================
   ページャ
   ========================================================= */
function the_posts_pagination( $args = array() ) {
	global $dcs_pages, $dcs_page;
	if ( empty( $dcs_pages ) || $dcs_pages < 2 ) { return; }
	echo '<nav class="pagination"><div class="nav-links">';
	for ( $i = 1; $i <= $dcs_pages; $i++ ) {
		$cur = ( $i === $dcs_page ) ? ' current' : '';
		$url = ( 1 === $i ) ? dcs_ctx( 'base' ) : dcs_ctx( 'base' ) . 'page/' . $i . '/';
		echo '<a class="page-numbers' . $cur . '" href="' . esc_url( $url ) . '">' . $i . '</a>';
	}
	echo '</div></nav>';
}
function wp_link_pages( $a = array() ) {}
function get_search_form() {}
