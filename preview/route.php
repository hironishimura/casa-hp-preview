<?php
/**
 * ルーティング
 *
 * パス文字列を受け取り、対応するテーマテンプレートを描画したHTMLを返します。
 */

/**
 * メインループを差し替える。
 *
 * @param array $posts 投稿の配列。
 */
function dcs_set_main( $posts ) {
	$q        = new WP_Query( array( 'post_type' => '__none__' ) );
	$q->posts = array_values( $posts );
	$GLOBALS['dcs_main'] = $q;

	/* WordPress と同じく、テンプレート読み込み前に $post をセットしておく
	   （これがないと wp_head() の時点でタイトル・canonical・OGPが取れない） */
	$GLOBALS['post'] = $q->posts ? $q->posts[0] : null;
}

/**
 * 1ページ分のHTMLを組み立てる。
 *
 * @param string $path URLパス（例 /works/hiraya/）。
 * @return array{html:string,status:int}
 */
function dcs_render_path( $path ) {
	$seg = array_values( array_filter( explode( '/', trim( $path, '/' ) ) ) );
	$seg = array_map( 'rawurldecode', $seg );

	$GLOBALS['dcs_page']  = 1;
	$GLOBALS['dcs_pages'] = 1;
	$GLOBALS['dcs_ctx']   = array();

	$tpl    = null;
	$status = 200;

	if ( ! $seg ) {
		$GLOBALS['dcs_ctx'] = array( 'type' => 'front' );
		dcs_set_main( array() );
		$tpl = 'front-page.php';

	} elseif ( 'works' === $seg[0] ) {

		if ( count( $seg ) >= 3 && 'feature' === $seg[1] ) {
			$term = null;
			foreach ( $GLOBALS['dcs_terms']['dc_work_tag'] as $t ) {
				if ( $t->slug === $seg[2] || $t->name === $seg[2] ) { $term = $t; }
			}
			if ( $term ) {
				$GLOBALS['dcs_ctx'] = array(
					'type'     => 'tax',
					'taxonomy' => 'dc_work_tag',
					'object'   => $term,
					'base'     => home_url( '/works/feature/' . $term->slug . '/' ),
				);
				dcs_set_main(
					dcs_pv_query(
						array(
							'post_type'      => 'dc_work',
							'posts_per_page' => -1,
							'orderby'        => array( 'menu_order' => 'DESC' ),
							'tax_query'      => array( array( 'terms' => array( $term->term_id ) ) ),
						)
					)
				);
				$tpl = 'taxonomy-dc_work_tag.php';
			}
		} elseif ( count( $seg ) >= 3 && 'page' === $seg[1] ) {
			$GLOBALS['dcs_page'] = max( 1, (int) $seg[2] );
			$tpl = 'archive-dc_work.php';
		} elseif ( 1 === count( $seg ) ) {
			$tpl = 'archive-dc_work.php';
		} else {
			$found = dcs_pv_query( array( 'post_type' => 'dc_work', 'name' => $seg[1] ) );
			if ( $found ) {
				$GLOBALS['dcs_ctx'] = array( 'type' => 'single', 'post_type' => 'dc_work' );
				dcs_set_main( $found );
				$tpl = 'single-dc_work.php';
			}
		}

		if ( 'archive-dc_work.php' === $tpl ) {
			$all = dcs_pv_query( array( 'post_type' => 'dc_work', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'DESC' ) ) );
			$per = 12;
			$GLOBALS['dcs_pages'] = (int) ceil( count( $all ) / $per );
			$GLOBALS['dcs_ctx']   = array( 'type' => 'archive', 'post_type' => 'dc_work', 'base' => home_url( '/works/' ) );
			dcs_set_main( array_slice( $all, ( $GLOBALS['dcs_page'] - 1 ) * $per, $per ) );
		}
	} elseif ( 'architect' === $seg[0] ) {

		if ( 1 === count( $seg ) ) {
			$GLOBALS['dcs_ctx'] = array( 'type' => 'archive', 'post_type' => 'dc_architect', 'base' => home_url( '/architect/' ) );
			dcs_set_main( dcs_pv_query( array( 'post_type' => 'dc_architect', 'posts_per_page' => -1 ) ) );
			$tpl = 'archive-dc_architect.php';
		} else {
			$found = dcs_pv_query( array( 'post_type' => 'dc_architect', 'name' => $seg[1] ) );
			if ( $found ) {
				$GLOBALS['dcs_ctx'] = array( 'type' => 'single', 'post_type' => 'dc_architect' );
				dcs_set_main( $found );
				$tpl = 'single-dc_architect.php';
			}
		}
	} elseif ( 'spec' === $seg[0] ) {

		if ( 1 === count( $seg ) ) {
			$GLOBALS['dcs_ctx'] = array( 'type' => 'archive', 'post_type' => 'dc_spec', 'base' => home_url( '/spec/' ) );
			dcs_set_main( dcs_pv_query( array( 'post_type' => 'dc_spec', 'posts_per_page' => -1 ) ) );
			$tpl = 'archive-dc_spec.php';
		} else {
			$found = dcs_pv_query( array( 'post_type' => 'dc_spec', 'name' => $seg[1] ) );
			if ( $found ) {
				$GLOBALS['dcs_ctx'] = array( 'type' => 'single', 'post_type' => 'dc_spec' );
				dcs_set_main( $found );
				$tpl = 'single-dc_spec.php';
			}
		}
	} else {
		$page = get_page_by_path( $seg[0] );
		if ( $page ) {
			$GLOBALS['dcs_ctx'] = array( 'type' => 'page' );
			dcs_set_main( array( $page ) );
			$tpl = 'page-' . $seg[0] . '.php';
			if ( ! file_exists( DCS_THEME_DIR . '/' . $tpl ) ) { $tpl = 'page.php'; }
		}
	}

	if ( ! $tpl ) {
		$status             = 404;
		$GLOBALS['dcs_ctx'] = array( 'type' => '404' );
		dcs_set_main( array() );
		$tpl = '404.php';
	}

	ob_start();
	include DCS_THEME_DIR . '/' . $tpl;

	return array(
		'html'   => ob_get_clean(),
		'status' => $status,
	);
}
