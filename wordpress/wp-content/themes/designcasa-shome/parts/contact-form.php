<?php
/**
 * 資料請求・お問い合わせフォーム
 *
 * ショートコード [dcs_contact_form] から呼ばれます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

$dcs_action = get_permalink();
?>
<div id="form" class="formwrap">
	<?php dcs_contact_message(); ?>

	<form class="form" method="post" action="<?php echo esc_url( $dcs_action ); ?>#form" novalidate>
		<?php wp_nonce_field( 'dcs_contact', 'dcs_contact_nonce' ); ?>

		<p class="form__intro"><span class="req">*</span> は必須項目です。</p>

		<fieldset class="form__set">
			<legend class="form__legend">ご希望（複数選択できます）</legend>
			<div class="checks">
				<?php foreach ( dcs_inquiry_purposes() as $i => $p ) : ?>
					<label class="check">
						<input type="checkbox" name="dcs_purpose[]" value="<?php echo esc_attr( $p ); ?>"<?php echo 0 === $i ? ' checked' : ''; ?>>
						<span><?php echo esc_html( $p ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<div class="field">
			<label class="field__label" for="dcs_name">お名前<span class="req">*</span></label>
			<input class="field__input" type="text" id="dcs_name" name="dcs_name" required autocomplete="name" placeholder="宇都宮 太郎">
		</div>

		<div class="field">
			<label class="field__label" for="dcs_kana">ふりがな</label>
			<input class="field__input" type="text" id="dcs_kana" name="dcs_kana" placeholder="うつのみや たろう">
		</div>

		<div class="field">
			<label class="field__label" for="dcs_email">メールアドレス<span class="req">*</span></label>
			<input class="field__input" type="email" id="dcs_email" name="dcs_email" required autocomplete="email" placeholder="example@example.com">
			<p class="field__help">確認のメールを自動でお送りします。届かない場合は迷惑メールフォルダをご確認ください。</p>
		</div>

		<div class="field">
			<label class="field__label" for="dcs_tel">お電話番号</label>
			<input class="field__input field__input--short" type="tel" id="dcs_tel" name="dcs_tel" autocomplete="tel" placeholder="028-000-0000">
		</div>

		<div class="field">
			<label class="field__label" for="dcs_zip">郵便番号</label>
			<input class="field__input field__input--short" type="text" id="dcs_zip" name="dcs_zip" autocomplete="postal-code" placeholder="321-0000">
			<p class="field__help">資料の郵送をご希望の場合はご記入ください。</p>
		</div>

		<div class="field">
			<label class="field__label" for="dcs_address">ご住所</label>
			<input class="field__input" type="text" id="dcs_address" name="dcs_address" autocomplete="street-address" placeholder="栃木県宇都宮市〇〇町0-0-0">
		</div>

		<div class="field">
			<label class="field__label" for="dcs_timing">ご検討時期</label>
			<select class="field__input" id="dcs_timing" name="dcs_timing">
				<option value="">選択してください</option>
				<?php foreach ( dcs_inquiry_timings() as $t ) : ?>
					<option value="<?php echo esc_attr( $t ); ?>"><?php echo esc_html( $t ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="field">
			<label class="field__label" for="dcs_land">土地の状況</label>
			<select class="field__input" id="dcs_land" name="dcs_land">
				<option value="">選択してください</option>
				<option value="所有している">所有している</option>
				<option value="購入する土地が決まっている">購入する土地が決まっている</option>
				<option value="探している">探している</option>
				<option value="建て替え">建て替え</option>
				<option value="まだ考えていない">まだ考えていない</option>
			</select>
			<p class="field__help">土地探しからのご相談も承っています（宅地建物取引業免許を保有しています）。</p>
		</div>

		<div class="field">
			<label class="field__label" for="dcs_message">ご要望・ご質問</label>
			<textarea class="field__input field__input--area" id="dcs_message" name="dcs_message" rows="7" placeholder="平屋を検討しています。予算の目安が知りたいです。／気になった施工例：〇〇の家"></textarea>
		</div>

		<!-- 自動投稿よけ（画面には表示されません） -->
		<div aria-hidden="true" style="position:absolute;left:-9999px" tabindex="-1">
			<label for="dcs_website">Website</label>
			<input type="text" id="dcs_website" name="dcs_website" tabindex="-1" autocomplete="off">
		</div>

		<div class="field__consent">
			<label class="check">
				<input type="checkbox" required>
				<span><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>" target="_blank" rel="noopener">プライバシーポリシー</a>に同意して送信します。</span>
			</label>
		</div>

		<div class="form__submit">
			<button class="btn btn--fill" type="submit">
				この内容で送信する
				<span class="btn__note">2営業日以内にご返信します</span>
			</button>
			<p class="form__note">送信後、確認のメールが自動で届きます。</p>
		</div>
	</form>
</div>
