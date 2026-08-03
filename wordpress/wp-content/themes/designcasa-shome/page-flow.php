<?php
/**
 * Template Name: 家づくりの流れ
 *
 * 主要キーワード：宇都宮市 注文住宅／注文住宅 宇都宮／工務店 宇都宮市
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>FLOW</p>
		<h1 class="phero__title">家づくりの流れ</h1>
		<p class="phero__lead">
			最初のご相談から、お引き渡しまで全14ステップ。
			宇都宮市で注文住宅を建てるとき、どの順番で何が決まっていくのかを、あらかじめ知っておいてください。
			「いま何をしている段階なのか」が分かるだけで、家づくりの不安はかなり減ります。
		</p>
	</div>
</section>

<section class="sec sec--flow">
	<div class="wrap">
		<div class="flow">
			<?php foreach ( dcs_flow_steps() as $i => $s ) : ?>
				<div class="flow__step reveal">
					<p class="flow__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
					<div class="flow__body">
						<h2 class="flow__title"><?php echo esc_html( $s['title'] ); ?><span class="flow__who"><?php echo esc_html( $s['who'] ); ?></span></h2>
						<p><?php echo esc_html( $s['body'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="sec sec--period">
	<div class="wrap wrap--narrow">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>SCHEDULE</p>
			<h2 class="sec-head__title">どれくらいの期間がかかるか</h2>
		</div>
		<div class="prose">
			<p>目安はつぎのとおりです。土地探しから始める場合は、ここに土地決定までの期間が加わります。</p>
			<ul>
				<li><strong>ご相談〜仮契約</strong>：1〜2ヶ月（敷地調査・資金計画）</li>
				<li><strong>仮契約〜プラン完成</strong>：約1ヶ月（建築家との打ち合わせ3回）</li>
				<li><strong>プラン完成〜ご契約</strong>：2週間〜1ヶ月（最終見積り確認）</li>
				<li><strong>ご契約〜着工</strong>：1〜2ヶ月（確認申請・地鎮祭）</li>
				<li><strong>着工〜お引き渡し</strong>：4〜6ヶ月（規模による）</li>
			</ul>
			<p>全体では<strong>10ヶ月〜1年ほど</strong>を見込んでください。「来年の春に入居したい」といったご希望がある場合は、逆算してスケジュールを組みます。</p>
			<h3>打ち合わせの回数に制限はありません</h3>
			<p>当社は打ち合わせ期間や間取り変更の回数に上限を設けていません。納得いくまでご検討ください。ただし、着工後の仕様変更は原則としてお受けできませんので、着工前にしっかり詰めていきます。</p>
			<h3>引き渡してからが、本当のお付き合い</h3>
			<p>お引き渡し後も定期点検でお伺いします。宇都宮市の地元工務店ですから、何かあればすぐに駆けつけられます。<a href="<?php echo esc_url( home_url( '/company/' ) ); ?>">施工会社について →</a></p>
		</div>
	</div>
</section>

<section class="sec sec--faq">
	<div class="wrap wrap--narrow">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>FAQ</p>
			<h2 class="sec-head__title">よくあるご質問</h2>
		</div>
		<div class="faq">
			<?php foreach ( dcs_faq_items() as $f ) : ?>
				<details class="faq__item">
					<summary class="faq__q"><?php echo esc_html( $f['q'] ); ?></summary>
					<div class="faq__a"><p><?php echo esc_html( $f['a'] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
dcs_cta();
get_footer();
