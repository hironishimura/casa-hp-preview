<?php
/**
 * フッター
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="site-foot">
	<div class="wrap">
		<div class="site-foot__top">
			<div class="site-foot__brand">
				<span class="brand__mark">design casa</span>
				<p class="site-foot__by">
					建築家とつくる注文住宅 design casa 加盟工務店<br>
					<strong><?php echo esc_html( dcs_company( 'name' ) ); ?></strong>
				</p>
				<address class="site-foot__addr">
					〒<?php echo esc_html( dcs_company( 'zip' ) ); ?> <?php echo esc_html( dcs_company( 'address' ) ); ?><br>
					TEL <a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'tel' ) ) ); ?>"><?php echo esc_html( dcs_company( 'tel' ) ); ?></a>
					／ フリーダイヤル <a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ); ?>"><?php echo esc_html( dcs_company( 'freedial' ) ); ?></a><br>
					営業時間 <?php echo esc_html( dcs_company( 'hours' ) ); ?>／定休日 火曜・水曜
				</address>
				<p class="site-foot__area">
					<strong>対応エリア</strong>：宇都宮市を中心に、鹿沼市・さくら市・矢板市・大田原市・那須烏山市・真岡市・下野市・塩谷町・高根沢町・芳賀町・市貝町・茂木町・益子町・上三川町・壬生町
				</p>
			</div>

			<nav class="site-foot__nav" aria-label="フッターメニュー">
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'site-foot__list',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				}
				?>
			</nav>
		</div>

		<p class="site-foot__credit">掲載写真：design casa（カーサプロジェクト）の施工実例</p>
		<p class="site-foot__copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( dcs_company( 'name' ) ); ?>. All rights reserved.</p>
	</div>
</footer>

<div class="mobilebar">
	<a class="mobilebar__tel" href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ); ?>">電話で相談</a>
	<a class="mobilebar__cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">資料請求・ご相談（無料）</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
