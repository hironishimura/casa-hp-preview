<?php
/**
 * プレビュー共通の初期化（シム読み込み＋データ組み立て）
 *
 * index.php（ローカルサーバー）と build.php（静的書き出し）の両方から使います。
 */

require __DIR__ . '/wp-shim.php';
require DCS_THEME_DIR . '/functions.php';

/* --- 固定ページ --- */
foreach ( dcs_page_defs() as $slug => $def ) {
	dcs_pv_post(
		array(
			'post_title' => $def['title'],
			'post_name'  => $slug,
			'post_type'  => 'page',
		)
	);
}

/* --- 家の仕様 --- */
foreach ( dcs_load_data( 'specs' ) as $i => $s ) {
	$term = dcs_pv_term( 'dc_spec_cat', $s['cat'] );
	$term->count++;

	$ids = array();
	foreach ( ( isset( $s['images'] ) ? $s['images'] : array() ) as $img ) {
		$ids[] = dcs_pv_att( $img['file'], $img['caption'], mb_substr( $img['caption'], 0, 110 ) );
	}

	dcs_pv_post(
		array(
			'post_title'   => $s['title'],
			'post_name'    => $s['slug'],
			'post_type'    => 'dc_spec',
			'post_excerpt' => $s['lead'],
			'post_content' => dcs_markdownish( $s['body'] ),
			'menu_order'   => $i,
			'thumb'        => $ids ? $ids[0] : 0,
			'meta'         => array(
				'dcs_spec_grade'   => $s['grade'],
				'dcs_spec_maker'   => $s['maker'],
				'dcs_spec_lead'    => $s['lead'],
				'dcs_seo_title'    => $s['seo_title'],
				'dcs_seo_desc'     => $s['seo_desc'],
				'dcs_spec_gallery' => implode( ',', $ids ),
			),
			'terms'        => array( $term ),
		)
	);
}

/* --- 建築家 --- */
foreach ( dcs_load_data( 'architects' ) as $i => $a ) {
	$content = '';
	foreach ( explode( "\n\n", $a['body'] ) as $para ) {
		$para = trim( $para );
		if ( $para ) { $content .= '<p>' . esc_html( $para ) . "</p>\n"; }
	}
	$thumb = 0;
	if ( ! empty( $a['image'] ) ) {
		$thumb = dcs_pv_att( 'architect/' . $a['image'], '', $a['name'] . '（' . $a['office'] . '）｜design casa 登録建築家' );
	}
	dcs_pv_post(
		array(
			'post_title'   => $a['name'],
			'post_name'    => $a['slug'],
			'post_type'    => 'dc_architect',
			'post_excerpt' => $a['policy'] ? $a['policy'] : $a['office'],
			'post_content' => $content,
			'menu_order'   => $i,
			'thumb'        => $thumb,
			'meta'         => array(
				'dcs_arch_kana'   => $a['kana'],
				'dcs_arch_office' => $a['office'],
				'dcs_arch_base'   => $a['base'],
				'dcs_arch_policy' => $a['policy'],
				'dcs_arch_career' => $a['career'],
				'dcs_arch_url'    => isset( $a['url'] ) ? $a['url'] : '',
			),
		)
	);
}

/* --- 施工例 --- */
foreach ( dcs_load_data( 'works' ) as $w ) {
	$terms = array();
	foreach ( $w['tags'] as $tag ) {
		$t = dcs_pv_term( 'dc_work_tag', $tag );
		$t->count++;
		$terms[] = $t;
	}
	$alt = sprintf( '%s｜%sの注文住宅 施工例（%s）', $w['title'], $w['pref'], $w['type'] );
	$ids = array();
	foreach ( $w['gallery'] as $g ) {
		$ids[] = dcs_pv_att( 'works/' . $g['file'], $g['caption'], $alt );
	}
	dcs_pv_post(
		array(
			'post_title'   => $w['title'],
			'post_name'    => $w['slug'],
			'post_type'    => 'dc_work',
			'post_excerpt' => sprintf( '%sで design casa の建築家が設計した%s。%s', $w['pref'], $w['type'], $w['catch'] ),
			'post_content' => dcs_work_content( $w ),
			'menu_order'   => (int) $w['no'],
			'thumb'        => $ids ? $ids[0] : 0,
			'terms'        => $terms,
			'meta'         => array(
				'dcs_work_no'        => $w['no'],
				'dcs_work_catch'     => $w['catch'],
				'dcs_work_area'      => $w['pref'],
				'dcs_work_structure' => '木造' . $w['type'],
				'dcs_work_gallery'   => implode( ',', $ids ),
			),
		)
	);
}

require __DIR__ . '/route.php';
