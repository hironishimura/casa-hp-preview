<?php
/**
 * パンくずリスト
 *
 * 表示用HTMLと、構造化データ（BreadcrumbList）の両方で同じ配列を使います。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * パンくずの項目を配列で返す。
 *
 * @return array<int,array{label:string,url:string}>
 */
function dcs_breadcrumb_items() {
	$items = array(
		array(
			'label' => 'ホーム',
			'url'   => home_url( '/' ),
		),
	);

	if ( is_front_page() ) {
		return $items;
	}

	if ( is_singular( 'dc_work' ) ) {
		$items[] = array(
			'label' => '施工例',
			'url'   => get_post_type_archive_link( 'dc_work' ),
		);
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular( 'dc_architect' ) ) {
		$items[] = array(
			'label' => '建築家紹介',
			'url'   => get_post_type_archive_link( 'dc_architect' ),
		);
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular( 'dc_spec' ) ) {
		$items[] = array(
			'label' => '家の仕様',
			'url'   => get_post_type_archive_link( 'dc_spec' ),
		);
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_post_type_archive( 'dc_work' ) ) {
		$items[] = array(
			'label' => '施工例',
			'url'   => '',
		);
	} elseif ( is_post_type_archive( 'dc_architect' ) ) {
		$items[] = array(
			'label' => '建築家紹介',
			'url'   => '',
		);
	} elseif ( is_post_type_archive( 'dc_spec' ) ) {
		$items[] = array(
			'label' => '家の仕様',
			'url'   => '',
		);
	} elseif ( is_tax( 'dc_work_tag' ) ) {
		$items[] = array(
			'label' => '施工例',
			'url'   => get_post_type_archive_link( 'dc_work' ),
		);
		$items[] = array(
			'label' => single_term_title( '', false ),
			'url'   => '',
		);
	} elseif ( is_tax( 'dc_spec_cat' ) ) {
		$items[] = array(
			'label' => '家の仕様',
			'url'   => get_post_type_archive_link( 'dc_spec' ),
		);
		$items[] = array(
			'label' => single_term_title( '', false ),
			'url'   => '',
		);
	} elseif ( is_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor ) {
			$items[] = array(
				'label' => get_the_title( $ancestor ),
				'url'   => get_permalink( $ancestor ),
			);
		}
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular() ) {
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_search() ) {
		$items[] = array(
			'label' => '検索結果',
			'url'   => '',
		);
	} elseif ( is_404() ) {
		$items[] = array(
			'label' => 'ページが見つかりません',
			'url'   => '',
		);
	}

	return $items;
}

/**
 * パンくずリストを出力する。
 */
function dcs_breadcrumb() {
	$items = dcs_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="crumbs" aria-label="パンくずリスト">
		<div class="wrap">
			<ol class="crumbs__list">
				<?php foreach ( $items as $item ) : ?>
					<li class="crumbs__item">
						<?php if ( $item['url'] ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php else : ?>
							<span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</nav>
	<?php
}
