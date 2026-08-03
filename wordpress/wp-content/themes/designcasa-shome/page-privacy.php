<?php
/**
 * Template Name: プライバシーポリシー
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

/* 管理画面で本文が入力されていればそちらを優先する */
$dcs_has_content = false;
if ( have_posts() ) {
	the_post();
	$dcs_has_content = (bool) trim( wp_strip_all_tags( get_the_content() ) );
}
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>PRIVACY POLICY</p>
		<h1 class="phero__title">プライバシーポリシー</h1>
		<p class="phero__lead">
			<?php echo esc_html( dcs_company( 'name' ) ); ?>（以下「当社」）は、お客さまの個人情報の保護を重要な責務と考え、
			以下の方針にもとづき適切に取り扱います。
		</p>
	</div>
</section>

<section class="sec sec--legal">
	<div class="wrap wrap--narrow">
		<div class="prose prose--legal">

			<?php if ( $dcs_has_content ) : ?>
				<?php the_content(); ?>
			<?php else : ?>

				<h2>1. 個人情報の定義</h2>
				<p>本ポリシーにおける「個人情報」とは、個人情報の保護に関する法律に定める個人情報、すなわち生存する個人に関する情報であって、氏名、住所、電話番号、メールアドレス等により特定の個人を識別できるものをいいます。</p>

				<h2>2. 取得する情報</h2>
				<p>当社は、資料請求・お問い合わせ・見学会のお申し込み・ご契約手続きなどにおいて、以下の情報を取得することがあります。</p>
				<ul>
					<li>氏名、ふりがな</li>
					<li>住所、郵便番号</li>
					<li>電話番号、メールアドレス</li>
					<li>ご家族の構成、ご検討時期、土地の状況、ご予算などの家づくりに関する情報</li>
					<li>ご要望・ご質問の内容</li>
				</ul>
				<p>また、ウェブサイトの利用状況を把握するため、Cookie等を用いてアクセス情報を取得する場合があります。これらの情報から個人を特定することはありません。</p>

				<h2>3. 利用目的</h2>
				<p>取得した個人情報は、以下の目的の範囲内で利用します。</p>
				<ul>
					<li>資料・カタログの送付、お問い合わせへの回答</li>
					<li>プラン提案、お見積り作成、打ち合わせなど、住宅の設計・施工に関する業務</li>
					<li>土地情報のご提案、資金計画のご相談への対応</li>
					<li>完成見学会・イベントのご案内</li>
					<li>お引き渡し後のアフターサービス、定期点検のご連絡</li>
					<li>サービス向上のための統計的な分析（個人を特定しない形で行います）</li>
				</ul>
				<p>上記以外の目的で利用する必要が生じた場合は、あらかじめご本人の同意をいただきます。</p>

				<h2>4. 第三者への提供</h2>
				<p>当社は、以下の場合を除き、ご本人の同意なく個人情報を第三者へ提供することはありません。</p>
				<ul>
					<li>法令にもとづく場合</li>
					<li>人の生命、身体または財産の保護のために必要があり、本人の同意を得ることが困難な場合</li>
					<li>設計・施工・登記・金融機関への手続きなど、業務の遂行に必要な範囲で、建築家・協力業者・金融機関等へ提供する場合（この場合、提供先に対しても適切な管理を求めます）</li>
				</ul>

				<h2>5. 委託先の管理</h2>
				<p>業務の一部を外部に委託する場合、委託先に対して個人情報の適切な取り扱いを契約等により義務づけ、必要かつ適切な監督を行います。</p>

				<h2>6. 安全管理措置</h2>
				<p>当社は、個人情報の漏えい、滅失またはき損の防止その他の安全管理のため、組織的・人的・物理的・技術的な安全管理措置を講じます。個人情報を取り扱う従業者に対しては、必要かつ適切な監督を行います。</p>

				<h2>7. 開示・訂正・削除等のご請求</h2>
				<p>ご本人からご自身の個人情報の開示、訂正、追加、削除、利用停止のお申し出があった場合は、ご本人であることを確認したうえで、法令にもとづき速やかに対応いたします。下記の窓口までご連絡ください。</p>

				<h2>8. Cookie（クッキー）およびアクセス解析</h2>
				<p>当社ウェブサイトでは、サイトの利用状況を把握し改善するために、Cookie を利用したアクセス解析ツールを使用する場合があります。取得される情報は匿名で収集されており、個人を特定するものではありません。ブラウザの設定により Cookie を無効にすることも可能です。</p>

				<h2>9. 本ポリシーの変更</h2>
				<p>法令の改正や事業内容の変更等に応じて、本ポリシーを変更することがあります。変更後の内容は、当ウェブサイトに掲載した時点から効力を生じるものとします。</p>

				<h2>10. お問い合わせ窓口</h2>
				<p>
					<?php echo esc_html( dcs_company( 'name' ) ); ?><br>
					〒<?php echo esc_html( dcs_company( 'zip' ) ); ?> <?php echo esc_html( dcs_company( 'address' ) ); ?><br>
					TEL <a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'tel' ) ) ); ?>"><?php echo esc_html( dcs_company( 'tel' ) ); ?></a>
					／ フリーダイヤル <a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ); ?>"><?php echo esc_html( dcs_company( 'freedial' ) ); ?></a><br>
					受付時間 <?php echo esc_html( dcs_company( 'hours' ) ); ?>（定休日：火曜・水曜）
				</p>

				<h2>11. 掲載写真について</h2>
				<p>当ウェブサイトに掲載している施工例の写真は、design casa（カーサプロジェクト株式会社）およびその加盟工務店による施工実例です。写真の著作権は各権利者に帰属します。無断転載を禁じます。</p>

				<p class="prose__date">制定日：<?php echo esc_html( gmdate( 'Y年n月j日' ) ); ?></p>

			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();
