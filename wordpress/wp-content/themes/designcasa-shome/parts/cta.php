<?php
/**
 * 共通CTA（各ページの最後に置く）
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cta">
	<div class="cta__media">
		<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/common/cta.jpg' ) ); ?>"
			alt="夜、窓に灯りがともる design casa の注文住宅" loading="lazy" decoding="async" width="1600" height="1067">
	</div>
	<div class="cta__body">
		<p class="cta__eyebrow"><span class="tick" aria-hidden="true"></span>CONTACT</p>
		<h2 class="cta__title">まだ、ぼんやりで<br class="br-sp">かまいません。</h2>
		<p class="cta__lead">
			土地が決まっていない。予算がわからない。何から始めればいいのか見当がつかない。
			その段階のご相談がいちばん多く、いちばん役に立てる段階です。
			宇都宮市の工務店として、しつこい営業は一切いたしません。
		</p>
		<div class="cta__actions">
			<a class="btn btn--fill" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				資料請求・ご相談（無料）
				<span class="btn__note">1分で入力できます</span>
			</a>
			<a class="btn btn--line" href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ); ?>">
				<?php echo esc_html( dcs_company( 'freedial' ) ); ?>
				<span class="btn__note"><?php echo esc_html( dcs_company( 'hours' ) ); ?>／火・水定休</span>
			</a>
		</div>
	</div>
</section>
