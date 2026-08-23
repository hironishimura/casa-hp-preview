<?php
/**
 * SEO（タイトル / メタディスクリプション / OGP / canonical / 構造化データ）
 *
 * SEOプラグインを入れなくても検索結果に必要な情報が揃うようにしています。
 * Yoast SEO や SEO SIMPLE PACK を入れた場合は、そちらが優先されるよう自動で無効化します。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * 他のSEOプラグインが有効かどうか。
 *
 * @return bool
 */
function dcs_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || class_exists( 'SEOPress' ) || defined( 'SSP_VERSION' );
}

/**
 * 現在の表示ページのSEO情報を返す。
 *
 * @return array{title:string,desc:string,robots:string,image:string}
 */
function dcs_seo_context() {
	$site   = get_bloginfo( 'name' );
	$title  = '';
	$desc   = '';
	$robots = 'index, follow, max-image-preview:large';
	$image  = get_theme_file_uri( 'assets/img/common/hero.jpg' );

	if ( is_front_page() ) {
		$title = '宇都宮の注文住宅｜建築家とつくる design casa（デザインカーサ）｜株式会社エスホーム';
		$desc  = '宇都宮市・栃木県で注文住宅を建てるなら工務店の株式会社エスホームへ。全国の建築家と design casa でつくるデザイン住宅を、耐震等級3・断熱等級6以上の標準仕様で。平屋・エアコン1台の全館空調・ペットと暮らす家まで施工例多数。資料請求受付中。';
	} elseif ( is_singular() ) {
		$post_id = get_the_ID();
		$title   = dcs_meta( 'dcs_seo_title', $post_id );
		$desc    = dcs_meta( 'dcs_seo_desc', $post_id );

		if ( ! $title ) {
			$title = get_the_title( $post_id );
			if ( 'dc_work' === get_post_type() ) {
				$area  = dcs_meta( 'dcs_work_area', $post_id );
				$area  = $area ? $area : '宇都宮市';
				$title = sprintf( '%s｜%sの注文住宅 施工例｜design casa × エスホーム', $title, $area );
			} elseif ( 'dc_architect' === get_post_type() ) {
				$title = sprintf( '%s｜design casa 登録建築家｜宇都宮で建築家とつくる注文住宅', $title );
			} elseif ( 'dc_spec' === get_post_type() ) {
				$title = sprintf( '%s｜宇都宮の高性能住宅の標準仕様｜エスホーム', $title );
			} else {
				$title = sprintf( '%s｜%s', $title, $site );
			}
		}
		if ( ! $desc ) {
			$desc = dcs_trim_desc( get_the_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
		}
		if ( has_post_thumbnail( $post_id ) ) {
			$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			if ( $src ) {
				$image = $src[0];
			}
		}
	} elseif ( is_post_type_archive( 'dc_work' ) ) {
		$title = '施工例一覧｜宇都宮市・栃木県の注文住宅と平屋｜design casa × エスホーム';
		$desc  = '宇都宮市を中心に栃木県で手がけた注文住宅の施工例一覧。平屋・二階建て・中庭のある家・ペットと暮らす家など、建築家がデザインした実例を写真と間取りの考え方つきで紹介します。宇都宮で工務店をお探しの方はぜひご覧ください。';
	} elseif ( is_post_type_archive( 'dc_architect' ) ) {
		$title = '建築家紹介｜宇都宮で建築家と建てる注文住宅 design casa｜エスホーム';
		$desc  = '宇都宮市・栃木県で建築家とつくる注文住宅 design casa の登録建築家をご紹介。数百棟の実績を持つ建築家が、あなたの敷地と暮らしに合わせてデザイン住宅を設計します。依頼の進め方や要望の伝え方は、宇都宮の工務店エスホームがあいだに入ってお手伝いします。';
	} elseif ( is_post_type_archive( 'dc_spec' ) ) {
		$title = '家の仕様一覧｜耐震等級3・断熱等級6以上が標準｜宇都宮の高気密高断熱住宅';
		$desc  = '宇都宮市の工務店エスホームの標準仕様。地震に強い家をつくる耐震等級3、断熱等級6以上の高断熱・高気密、エアコン1台で家中を快適にする全館空調まで、構造・断熱・空調・建材の仕様をすべて公開しています。';
	} elseif ( is_tax( 'dc_work_tag' ) ) {
		$term  = get_queried_object();
		$title = sprintf( '%sの施工例｜宇都宮市・栃木県の注文住宅｜design casa × エスホーム', $term->name );
		$desc  = $term->description ? dcs_trim_desc( $term->description ) : sprintf( '宇都宮市・栃木県で建てた「%s」の注文住宅事例をまとめました。建築家とつくるデザイン住宅を、耐震等級3・断熱等級6以上の標準仕様で。', $term->name );
	} elseif ( is_tax( 'dc_spec_cat' ) ) {
		// 仕様カテゴリは「施工例」ではないので、別の文面にする.
		$term  = get_queried_object();
		$title = sprintf( '%sの仕様｜宇都宮の高気密高断熱住宅｜design casa × エスホーム', $term->name );
		$desc  = $term->description ? dcs_trim_desc( $term->description ) : sprintf( '宇都宮市の工務店エスホームの標準仕様のうち「%s」に関する内容をまとめました。耐震等級3・断熱等級6以上を標準とした家づくりの中身を公開しています。', $term->name );
	} elseif ( is_search() ) {
		$title  = sprintf( '「%s」の検索結果｜%s', get_search_query(), $site );
		$robots = 'noindex, follow';
	} elseif ( is_404() ) {
		$title  = 'ページが見つかりません｜' . $site;
		$robots = 'noindex, follow';
	} else {
		$title = wp_get_document_title();
	}

	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}

	/* 確認用プレビューを書き出すときだけ、検索エンジンに登録させない */
	if ( defined( 'DCS_NOINDEX' ) && DCS_NOINDEX ) {
		$robots = 'noindex, nofollow';
	}

	return array(
		'title'  => $title,
		'desc'   => dcs_trim_desc( $desc ),
		'robots' => $robots,
		'image'  => $image,
	);
}

/**
 * ディスクリプションを整形する。
 *
 * @param string $text 元テキスト。
 * @return string
 */
function dcs_trim_desc( $text ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $text ) ) );

	return mb_substr( $text, 0, 120 );
}

/**
 * <title> を差し替える。
 *
 * @param string $title タイトル。
 * @return string
 */
function dcs_filter_title( $title ) {
	if ( dcs_seo_plugin_active() || is_admin() || is_feed() ) {
		return $title;
	}
	$ctx = dcs_seo_context();

	return $ctx['title'] ? $ctx['title'] : $title;
}
add_filter( 'pre_get_document_title', 'dcs_filter_title', 20 );

/**
 * <head> にメタタグを出力する。
 */
function dcs_head_meta() {
	if ( dcs_seo_plugin_active() ) {
		return;
	}

	$ctx  = dcs_seo_context();
	$url  = dcs_current_url();
	$site = get_bloginfo( 'name' );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $ctx['desc'] ) );
	printf( '<meta name="robots" content="%s">' . "\n", esc_attr( $ctx['robots'] ) );
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

	printf( '<meta property="og:type" content="%s">' . "\n", is_front_page() ? 'website' : 'article' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $ctx['title'] ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $ctx['desc'] ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $ctx['image'] ) );
	printf( '<meta property="og:locale" content="ja_JP">' . "\n" );
	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
}
add_action( 'wp_head', 'dcs_head_meta', 1 );

/**
 * 現在のURLを返す。
 *
 * @return string
 */
function dcs_current_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_query_var( 'post_type' ) ? get_query_var( 'post_type' ) : get_post_type() );
	}
	if ( is_tax() || is_category() || is_tag() ) {
		return get_term_link( get_queried_object() );
	}

	return home_url( add_query_arg( array() ) );
}

/* =========================================================
   構造化データ（JSON-LD）
   ========================================================= */

/**
 * JSON-LD を出力する。
 */
function dcs_jsonld() {
	$graph = array();
	$home  = home_url( '/' );

	/* 事業者情報：ローカルSEOの土台 */
	$graph[] = array(
		'@type'       => 'HomeAndConstructionBusiness',
		'@id'         => $home . '#organization',
		'name'        => dcs_company( 'name' ),
		'alternateName' => dcs_company( 'brand' ),
		'url'         => $home,
		'telephone'   => dcs_company( 'tel' ),
		'faxNumber'   => dcs_company( 'fax' ),
		'image'       => get_theme_file_uri( 'assets/img/common/hero.jpg' ),
		'priceRange'  => '¥¥',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'postalCode'      => dcs_company( 'zip' ),
			'addressRegion'   => '栃木県',
			'addressLocality' => '宇都宮市',
			'streetAddress'   => '平出町3563-3',
			'addressCountry'  => 'JP',
		),
		'geo'         => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => dcs_company( 'lat' ),
			'longitude' => dcs_company( 'lng' ),
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
				'opens'     => '09:00',
				'closes'    => '17:00',
			),
		),
		'areaServed'  => array_map(
			function ( $a ) {
				return array(
					'@type' => 'City',
					'name'  => $a,
				);
			},
			dcs_areas_main()
		),
		'knowsAbout'  => array( '注文住宅', '平屋', '高気密高断熱住宅', 'エコハウス', '全館空調', '耐震等級3', 'デザイン住宅' ),
	);

	/* サイト情報 */
	$graph[] = array(
		'@type'     => 'WebSite',
		'@id'       => $home . '#website',
		'url'       => $home,
		'name'      => get_bloginfo( 'name' ),
		'inLanguage' => 'ja',
		'publisher' => array( '@id' => $home . '#organization' ),
	);

	/* パンくず */
	$crumbs = dcs_breadcrumb_items();
	if ( count( $crumbs ) > 1 ) {
		$items = array();
		foreach ( $crumbs as $i => $c ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $c['label'],
			);
			if ( ! empty( $c['url'] ) ) {
				$item['item'] = $c['url'];
			}
			$items[] = $item;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	/* 施工例＝制作実績として */
	if ( is_singular( 'dc_work' ) ) {
		$graph[] = array(
			'@type'       => 'CreativeWork',
			'name'        => get_the_title(),
			'description' => dcs_trim_desc( get_the_excerpt() ),
			'image'       => get_the_post_thumbnail_url( null, 'full' ),
			'creator'     => array( '@id' => $home . '#organization' ),
			'locationCreated' => array(
				'@type' => 'Place',
				'name'  => dcs_meta( 'dcs_work_area' ),
			),
		);
	}

	/* FAQ（トップと流れページ） */
	$faqs = dcs_faq_items();
	if ( $faqs && ( is_front_page() || is_page( 'flow' ) || is_page( 'contact' ) ) ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => array_map(
				function ( $f ) {
					return array(
						'@type'          => 'Question',
						'name'           => $f['q'],
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $f['a'],
						),
					);
				},
				$faqs
			),
		);
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dcs_jsonld', 5 );

/* =========================================================
   SEOメタボックス（ページ単位でタイトル・説明を上書き）
   ========================================================= */

/**
 * SEOメタボックスを追加する。
 */
function dcs_add_seo_box() {
	foreach ( array( 'page', 'post', 'dc_work', 'dc_architect', 'dc_spec' ) as $pt ) {
		add_meta_box( 'dcs_seo_box', '検索エンジン向け設定（SEO）', 'dcs_render_seo_box', $pt, 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes', 'dcs_add_seo_box' );

/**
 * SEOメタボックスを描画する。
 *
 * @param WP_Post $post 投稿。
 */
function dcs_render_seo_box( $post ) {
	wp_nonce_field( 'dcs_save_seo', 'dcs_seo_nonce' );
	$title = get_post_meta( $post->ID, 'dcs_seo_title', true );
	$desc  = get_post_meta( $post->ID, 'dcs_seo_desc', true );
	?>
	<p><label for="dcs_seo_title"><strong>検索結果のタイトル</strong>（30〜35文字目安・空欄なら自動生成）</label><br>
	<input type="text" id="dcs_seo_title" name="dcs_seo_title" value="<?php echo esc_attr( $title ); ?>" class="large-text"></p>
	<p><label for="dcs_seo_desc"><strong>検索結果の説明文</strong>（90〜120文字目安・空欄なら本文から自動生成）</label><br>
	<textarea id="dcs_seo_desc" name="dcs_seo_desc" rows="3" class="large-text"><?php echo esc_textarea( $desc ); ?></textarea></p>
	<?php
}

/**
 * SEOメタボックスを保存する。
 *
 * @param int $post_id 投稿ID。
 */
function dcs_save_seo( $post_id ) {
	if ( ! isset( $_POST['dcs_seo_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['dcs_seo_nonce'] ) ), 'dcs_save_seo' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['dcs_seo_title'] ) ) {
		update_post_meta( $post_id, 'dcs_seo_title', sanitize_text_field( wp_unslash( $_POST['dcs_seo_title'] ) ) );
	}
	if ( isset( $_POST['dcs_seo_desc'] ) ) {
		update_post_meta( $post_id, 'dcs_seo_desc', sanitize_textarea_field( wp_unslash( $_POST['dcs_seo_desc'] ) ) );
	}
}
add_action( 'save_post', 'dcs_save_seo' );
