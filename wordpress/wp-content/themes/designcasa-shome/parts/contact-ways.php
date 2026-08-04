<?php
/**
 * 電話・メール・フォームの3経路
 *
 * ショートコード [dcs_contact_ways] から呼ばれます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ways">
	<div class="way way--tel">
		<p class="way__label">お電話でのご相談</p>
		<p class="way__main"><a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ); ?>"><?php echo esc_html( dcs_company( 'freedial' ) ); ?></a></p>
		<p class="way__sub">
			TEL <a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'tel' ) ) ); ?>"><?php echo esc_html( dcs_company( 'tel' ) ); ?></a>／FAX <?php echo esc_html( dcs_company( 'fax' ) ); ?><br>
			<?php echo esc_html( dcs_company( 'hours' ) ); ?>／定休日 火曜・水曜<br>
			いちばん早くお答えできます。
		</p>
	</div>
	<div class="way">
		<p class="way__label">メールでのご相談</p>
		<p class="way__main way__main--mail">
			<a href="mailto:<?php echo esc_attr( dcs_contact_email() ); ?>?subject=<?php echo rawurlencode( 'design casa 宇都宮についてのお問い合わせ' ); ?>"><?php echo esc_html( dcs_contact_email() ); ?></a>
		</p>
		<p class="way__sub">
			フォームを使わずに直接お送りいただいてもかまいません。<br>
			お名前・ご連絡先・ご相談内容をお書き添えください。
		</p>
	</div>
	<div class="way way--form">
		<p class="way__label">フォームでのご相談</p>
		<p class="way__main way__main--sm">24時間受付</p>
		<p class="way__sub">
			下のフォームからどうぞ。入力は1分ほどです。<br>
			2営業日以内にご返信します。
		</p>
		<p><a class="btn btn--fill" href="#form">フォームへ移動する</a></p>
	</div>
</div>
