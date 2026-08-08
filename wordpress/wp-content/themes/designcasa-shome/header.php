<?php
/**
 * ヘッダー
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<?php wp_head(); ?>
</head>
<body <?php body_class( is_front_page() ? 'is-front' : 'is-sub' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">本文へスキップ</a>

<header class="site-head<?php echo is_front_page() ? '' : ' is-solid'; ?>">
	<div class="site-head__inner">
		<div class="brand-wrap">
			<p class="brand__seo">宇都宮のデザイン注文住宅なら、design casa宇都宮</p>
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="brand__mark">design casa</span>
				<span class="brand__sub">UTSUNOMIYA / S HOME</span>
			</a>
		</div>

		<nav class="gnav" id="gnav" aria-label="メインメニュー">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'gnav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			}
			?>
			<a class="gnav__cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">資料請求・ご相談（無料）</a>
		</nav>

		<a class="head-cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">資料請求（無料）</a>

		<button class="navtoggle" type="button" aria-expanded="false" aria-controls="gnav" aria-label="メニューを開く">
			<span class="navtoggle__bars" aria-hidden="true"></span>
			<span class="navtoggle__text">MENU</span>
		</button>
	</div>
</header>

<main id="main" class="<?php echo is_front_page() ? '' : 'page'; ?>">
