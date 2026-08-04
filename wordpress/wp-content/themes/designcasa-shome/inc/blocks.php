<?php
/**
 * ブロック（Gutenberg）まわり
 *
 * 1. ブロックマークアップを組み立てる関数群
 *    → 取り込み時に、すべての本文をブロックとして保存します。
 *      管理画面のブロックエディタでそのまま編集できます。
 * 2. 一覧など「自動で増える部分」用のショートコード
 *    → ブロックエディタでは「ショートコード」ブロックとして扱えます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================
   ブロックマークアップの組み立て
   ========================================================= */

/**
 * 段落ブロック。
 *
 * @param string $text      本文（HTML可）。
 * @param string $class_name 追加クラス。
 * @return string
 */
function dcs_b_paragraph( $text, $class_name = '' ) {
	$text = trim( $text );
	if ( '' === $text ) {
		return '';
	}
	$attr  = $class_name ? ' {"className":"' . esc_attr( $class_name ) . '"}' : '';
	$class = $class_name ? ' class="' . esc_attr( $class_name ) . '"' : '';

	return sprintf( "<!-- wp:paragraph%s -->\n<p%s>%s</p>\n<!-- /wp:paragraph -->", $attr, $class, $text );
}

/**
 * 見出しブロック。
 *
 * @param int    $level      レベル（2〜4）。
 * @param string $text       見出し。
 * @param string $class_name 追加クラス。
 * @return string
 */
function dcs_b_heading( $level, $text, $class_name = '' ) {
	$args = array();
	if ( 2 !== (int) $level ) {
		$args['level'] = (int) $level;
	}
	if ( $class_name ) {
		$args['className'] = $class_name;
	}
	$attr  = $args ? ' ' . wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
	$class = $class_name ? ' class="' . esc_attr( $class_name ) . '"' : '';

	return sprintf(
		"<!-- wp:heading%s -->\n<h%d%s>%s</h%d>\n<!-- /wp:heading -->",
		$attr,
		(int) $level,
		$class,
		esc_html( $text ),
		(int) $level
	);
}

/**
 * 画像ブロック（キャプションつき）。
 *
 * @param int    $att_id  添付ID。
 * @param string $caption キャプション。
 * @param string $size    画像サイズ。
 * @return string
 */
function dcs_b_image( $att_id, $caption = '', $size = 'large' ) {
	$att_id = (int) $att_id;
	if ( ! $att_id ) {
		return '';
	}
	$src = wp_get_attachment_image_url( $att_id, $size );
	if ( ! $src ) {
		return '';
	}
	$alt = (string) get_post_meta( $att_id, '_wp_attachment_image_alt', true );

	$attrs = wp_json_encode(
		array(
			'id'        => $att_id,
			'sizeSlug'  => $size,
			'linkDestination' => 'none',
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	$fig = sprintf(
		'<figure class="wp-block-image size-%s"><img src="%s" alt="%s" class="wp-image-%d"/>%s</figure>',
		esc_attr( $size ),
		esc_url( $src ),
		esc_attr( $alt ),
		$att_id,
		$caption ? '<figcaption class="wp-element-caption">' . esc_html( $caption ) . '</figcaption>' : ''
	);

	return "<!-- wp:image {$attrs} -->\n{$fig}\n<!-- /wp:image -->";
}

/**
 * リストブロック。
 *
 * @param string[] $items   項目。
 * @param bool     $ordered 番号つきか。
 * @return string
 */
function dcs_b_list( $items, $ordered = false ) {
	$items = array_filter( array_map( 'trim', $items ) );
	if ( ! $items ) {
		return '';
	}
	$tag  = $ordered ? 'ol' : 'ul';
	$attr = $ordered ? ' {"ordered":true}' : '';
	$li   = '';
	foreach ( $items as $i ) {
		$li .= "<!-- wp:list-item -->\n<li>{$i}</li>\n<!-- /wp:list-item -->\n";
	}

	return "<!-- wp:list{$attr} -->\n<{$tag} class=\"wp-block-list\">\n{$li}</{$tag}>\n<!-- /wp:list -->";
}

/**
 * 区切り線ブロック。
 *
 * @return string
 */
function dcs_b_separator() {
	return "<!-- wp:separator -->\n<hr class=\"wp-block-separator has-alpha-channel-opacity\"/>\n<!-- /wp:separator -->";
}

/**
 * ショートコードブロック（一覧など自動更新される部分）。
 *
 * @param string $shortcode 例 [dcs_works count="6"]。
 * @return string
 */
function dcs_b_shortcode( $shortcode ) {
	return "<!-- wp:shortcode -->\n{$shortcode}\n<!-- /wp:shortcode -->";
}

/**
 * グループブロックで包む。
 *
 * @param string $inner      中身。
 * @param string $class_name クラス。
 * @return string
 */
function dcs_b_group( $inner, $class_name = '' ) {
	$args  = array( 'layout' => array( 'type' => 'constrained' ) );
	if ( $class_name ) {
		$args['className'] = $class_name;
	}
	$attr  = ' ' . wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	$class = 'wp-block-group' . ( $class_name ? ' ' . $class_name : '' );

	return "<!-- wp:group{$attr} -->\n<div class=\"" . esc_attr( $class ) . "\">\n{$inner}\n</div>\n<!-- /wp:group -->";
}

/**
 * ボタンブロック。
 *
 * @param string $label ラベル。
 * @param string $url   リンク先。
 * @return string
 */
function dcs_b_button( $label, $url ) {
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">\n"
		. "<!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\""
		. esc_url( $url ) . "\">" . esc_html( $label ) . "</a></div>\n<!-- /wp:button -->\n"
		. "</div>\n<!-- /wp:buttons -->";
}

/**
 * ブロックをつなぐ。
 *
 * @param array $blocks ブロック文字列の配列。
 * @return string
 */
function dcs_b_join( $blocks ) {
	return implode( "\n\n", array_filter( array_map( 'trim', $blocks ) ) );
}

/**
 * かんたんなMarkdown（見出し・リスト・強調）をブロックへ変換する。
 *
 * @param string $text 本文。
 * @return string
 */
function dcs_markdown_to_blocks( $text ) {
	$out = array();
	foreach ( preg_split( '/\n{2,}/', trim( (string) $text ) ) as $block ) {
		$block = trim( $block );
		if ( '' === $block ) {
			continue;
		}
		if ( 0 === strpos( $block, '## ' ) ) {
			$out[] = dcs_b_heading( 2, substr( $block, 3 ) );
		} elseif ( 0 === strpos( $block, '### ' ) ) {
			$out[] = dcs_b_heading( 3, substr( $block, 4 ) );
		} elseif ( preg_match( '/^(\d+\.|-)\s/', $block ) ) {
			$ordered = (bool) preg_match( '/^\d+\.\s/', $block );
			$items   = array();
			foreach ( preg_split( '/\n/', $block ) as $line ) {
				$items[] = dcs_b_inline( preg_replace( '/^(\d+\.|-)\s*/', '', trim( $line ) ) );
			}
			$out[] = dcs_b_list( $items, $ordered );
		} else {
			$out[] = dcs_b_paragraph( dcs_b_inline( $block ) );
		}
	}

	return dcs_b_join( $out );
}

/**
 * **強調** と改行をHTMLへ変換する。
 *
 * @param string $text テキスト。
 * @return string
 */
function dcs_b_inline( $text ) {
	$text = esc_html( $text );
	$text = preg_replace( '/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text );

	return str_replace( "\n", '<br>', $text );
}

/* =========================================================
   ショートコード（自動で増える部分）
   ========================================================= */

/**
 * 施工例の一覧。[dcs_works count="6"]
 *
 * @param array $atts 属性。
 * @return string
 */
function dcs_sc_works( $atts ) {
	$a = shortcode_atts(
		array(
			'count' => 6,
			'tag'   => '',
		),
		$atts
	);

	$args = array(
		'post_type'      => 'dc_work',
		'posts_per_page' => (int) $a['count'],
		'orderby'        => array(
			'menu_order' => 'DESC',
			'date'       => 'DESC',
		),
	);
	if ( $a['tag'] ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'dc_work_tag',
				'field'    => 'name',
				'terms'    => array( $a['tag'] ),
			),
		);
	}

	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) {
		return '';
	}

	ob_start();
	echo '<div class="works">';
	while ( $q->have_posts() ) {
		$q->the_post();
		dcs_work_card();
	}
	echo '</div>';
	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'dcs_works', 'dcs_sc_works' );

/**
 * 建築家の名前一覧。[dcs_architects]
 *
 * @return string
 */
function dcs_sc_architects() {
	$q = new WP_Query(
		array(
			'post_type'      => 'dc_architect',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
	if ( ! $q->have_posts() ) {
		return '';
	}

	ob_start();
	echo '<ul class="names">';
	while ( $q->have_posts() ) {
		$q->the_post();
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
	}
	echo '</ul>';
	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'dcs_architects', 'dcs_sc_architects' );

/**
 * 家づくりの流れ。[dcs_flow from="1" to="4"]
 *
 * @param array $atts 属性。
 * @return string
 */
function dcs_sc_flow( $atts ) {
	$a     = shortcode_atts( array( 'from' => 1, 'to' => 14 ), $atts );
	$steps = dcs_flow_steps();
	$from  = max( 1, (int) $a['from'] );
	$to    = min( count( $steps ), (int) $a['to'] );

	ob_start();
	echo '<div class="flow">';
	for ( $i = $from; $i <= $to; $i++ ) {
		$s = $steps[ $i - 1 ];
		printf(
			'<div class="flow__step reveal"><p class="flow__num">%02d</p><div class="flow__body">
			<h3 class="flow__title">%s<span class="flow__who">%s</span></h3><p>%s</p></div></div>',
			$i,
			esc_html( $s['title'] ),
			esc_html( $s['who'] ),
			esc_html( $s['body'] )
		);
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'dcs_flow', 'dcs_sc_flow' );

/**
 * よくある質問。[dcs_faq count="12"]
 *
 * @param array $atts 属性。
 * @return string
 */
function dcs_sc_faq( $atts ) {
	$a     = shortcode_atts( array( 'count' => 99 ), $atts );
	$items = array_slice( dcs_faq_items(), 0, (int) $a['count'] );

	ob_start();
	echo '<div class="faq">';
	foreach ( $items as $f ) {
		printf(
			'<details class="faq__item"><summary class="faq__q">%s</summary><div class="faq__a"><p>%s</p></div></details>',
			esc_html( $f['q'] ),
			esc_html( $f['a'] )
		);
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'dcs_faq', 'dcs_sc_faq' );

/**
 * 標準仕様の数値。[dcs_figures]
 *
 * @return string
 */
function dcs_sc_figures() {
	ob_start();
	echo '<ul class="figures">';
	foreach ( dcs_spec_figures() as $f ) {
		printf(
			'<li class="figures__item"><p class="figures__num">%s<span>%s</span></p>
			<h3 class="figures__label">%s</h3><p class="figures__note">%s</p></li>',
			esc_html( $f['num'] ),
			esc_html( $f['unit'] ),
			esc_html( $f['label'] ),
			esc_html( $f['note'] )
		);
	}
	echo '</ul>';

	return ob_get_clean();
}
add_shortcode( 'dcs_figures', 'dcs_sc_figures' );

/**
 * 対応エリアと地図。[dcs_area map="1"]
 *
 * @param array $atts 属性。
 * @return string
 */
function dcs_sc_area( $atts ) {
	$a = shortcode_atts( array( 'map' => '1' ), $atts );

	ob_start();
	echo '<div class="area"><div>';
	echo '<p class="area__head">対応エリア</p><ul class="area__list">';
	foreach ( dcs_areas_main() as $x ) {
		echo '<li>' . esc_html( $x ) . '</li>';
	}
	echo '</ul>';
	echo '<p class="area__head" style="margin-top:32px">一部地域を除きご相談ください</p><ul class="area__list area__list--sub">';
	foreach ( dcs_areas_sub() as $x ) {
		echo '<li>' . esc_html( $x ) . '</li>';
	}
	echo '</ul>';
	echo '<p class="area__note">上記以外の地域も、まずはご相談ください。可否をお答えします。</p>';
	echo '</div>';
	if ( '1' === (string) $a['map'] ) {
		printf(
			'<div class="map"><iframe src="%s" title="株式会社エスホームの所在地" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe></div>',
			esc_url( dcs_map_src() )
		);
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'dcs_area', 'dcs_sc_area' );

/**
 * 会社概要の表。[dcs_company_table]
 *
 * @return string
 */
function dcs_sc_company_table() {
	ob_start();
	echo '<dl class="deflist">';
	dcs_dl_row( '商号', esc_html( dcs_company( 'name' ) ) );
	dcs_dl_row( '本店', '〒' . esc_html( dcs_company( 'zip' ) ) . ' ' . esc_html( dcs_company( 'address' ) ) );
	dcs_dl_row( '会社成立', esc_html( dcs_company( 'founded' ) ) );
	dcs_dl_row( '代表者', esc_html( dcs_company( 'ceo' ) ) );
	dcs_dl_row( '事業内容', esc_html( dcs_company( 'business' ) ) );
	dcs_dl_row(
		'電話 / FAX',
		sprintf(
			'TEL <a href="%1$s">%2$s</a> ／ フリーダイヤル <a href="%3$s">%4$s</a><br>FAX %5$s',
			esc_attr( dcs_tel_href( dcs_company( 'tel' ) ) ),
			esc_html( dcs_company( 'tel' ) ),
			esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ),
			esc_html( dcs_company( 'freedial' ) ),
			esc_html( dcs_company( 'fax' ) )
		)
	);
	dcs_dl_row( '営業時間', esc_html( dcs_company( 'hours' ) ) );
	dcs_dl_row( '定休日', esc_html( dcs_company( 'holiday' ) ) );
	echo '</dl>';

	$lic = array_filter(
		array(
			'建設業許可'         => dcs_company( 'license_kensetsu' ),
			'宅地建物取引業免許' => dcs_company( 'license_takken' ),
			'一級建築士事務所'   => dcs_company( 'license_kenchikushi' ),
		)
	);
	if ( $lic ) {
		echo '<ul class="license license--light">';
		foreach ( $lic as $label => $num ) {
			printf( '<li><p class="license__label">%s</p><p class="license__num">%s</p></li>', esc_html( $label ), esc_html( $num ) );
		}
		echo '</ul>';
	}

	return ob_get_clean();
}
add_shortcode( 'dcs_company_table', 'dcs_sc_company_table' );

/**
 * 問い合わせフォーム。[dcs_contact_form]
 *
 * @return string
 */
function dcs_sc_contact_form() {
	ob_start();
	get_template_part( 'parts/contact-form' );

	return ob_get_clean();
}
add_shortcode( 'dcs_contact_form', 'dcs_sc_contact_form' );

/**
 * 電話・メール・フォームの3経路。[dcs_contact_ways]
 *
 * @return string
 */
function dcs_sc_contact_ways() {
	ob_start();
	get_template_part( 'parts/contact-ways' );

	return ob_get_clean();
}
add_shortcode( 'dcs_contact_ways', 'dcs_sc_contact_ways' );

/**
 * ショートコードの一覧を、ブロックエディタの「ショートコード」ブロックで使いやすいよう説明として出す。
 *
 * @return array
 */
function dcs_shortcode_help() {
	return array(
		'[dcs_works count="6"]'    => '施工例カードを新しい順に表示（tag="平屋" で絞り込み可）',
		'[dcs_architects]'         => '建築家の名前一覧',
		'[dcs_flow from="1" to="14"]' => '家づくりの流れ',
		'[dcs_faq count="12"]'     => 'よくあるご質問',
		'[dcs_figures]'            => '標準仕様の数値（耐震等級3など）',
		'[dcs_area map="1"]'       => '対応エリアと地図',
		'[dcs_company_table]'      => '会社概要の表',
		'[dcs_contact_ways]'       => '電話・メール・フォームの3経路',
		'[dcs_contact_form]'       => '資料請求・お問い合わせフォーム',
	);
}
