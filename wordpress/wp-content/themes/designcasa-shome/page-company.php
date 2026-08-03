<?php
/**
 * Template Name: 施工会社紹介
 *
 * 主要キーワード：宇都宮市 工務店／工務店 宇都宮／栃木県 工務店／工務店 栃木県
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>COMPANY</p>
		<h1 class="phero__title">施工会社紹介<br><span class="phero__sub">株式会社エスホーム</span></h1>
		<p class="phero__lead">
			design casa 宇都宮の家をつくるのは、宇都宮市平出町の工務店・株式会社エスホームです。
			設計から施工、引き渡し後の点検まで、地元の会社が一貫して責任を持ちます。
		</p>
	</div>
</section>

<section class="sec sec--intro">
	<div class="wrap">
		<div class="intro">
			<div class="intro__text">
				<h2 class="intro__title">ハワイのように明るく<br>心地よい暮らしを、<br>安心できる価格で。</h2>
				<p>大手ハウスメーカーほど規格に縛られず、ローコスト系ほど性能を削らない。私たちが立っているのは、その中間です。</p>
				<p>打ち合わせ期間にも、間取り変更の回数にも制限を設けていません。家は一度建てたら数十年つき合うものですから、納得いくまで考える時間があっていいはずだと思っています。</p>
				<p>採用しているのは<strong>松尾式のエコハウス</strong>の考え方です。高断熱・高気密、日射をコントロールするパッシブ設計、そして計画的な空気の流れ。この組み合わせによって、<strong>エアコン1台で家じゅうの温度がそろう家</strong>を実現します。</p>
				<p>宇都宮市の冬は冷え込み、夏は蒸し暑い。この土地で暮らすからこそ、性能にこだわる意味があります。</p>
			</div>
			<figure class="intro__fig">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/common/hiraya.jpg' ) ); ?>"
					alt="宇都宮市の工務店エスホームが手がける、大屋根の平屋" loading="lazy" decoding="async" width="1600" height="1067">
			</figure>
		</div>
	</div>
</section>

<section class="sec sec--outline">
	<div class="wrap wrap--narrow">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>OUTLINE</p>
			<h2 class="sec-head__title">会社概要</h2>
		</div>
		<dl class="deflist">
			<?php
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
			?>
		</dl>
	</div>
</section>

<?php
$dcs_licenses = array_filter(
	array(
		'建設業許可'           => dcs_company( 'license_kensetsu' ),
		'宅地建物取引業免許'   => dcs_company( 'license_takken' ),
		'一級建築士事務所'     => dcs_company( 'license_kenchikushi' ),
	)
);
if ( $dcs_licenses ) :
	?>
	<section class="sec sec--license">
		<div class="wrap">
			<div class="sec-head sec-head--light">
				<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>LICENSE</p>
				<h2 class="sec-head__title">登録・許可</h2>
				<p class="sec-head__note">設計・施工・不動産仲介まで、必要な資格と許可をすべて自社で保有しています。土地探しからのご相談も一社で完結します。</p>
			</div>
			<ul class="license">
				<?php foreach ( $dcs_licenses as $label => $num ) : ?>
					<li>
						<p class="license__label"><?php echo esc_html( $label ); ?></p>
						<p class="license__num"><?php echo esc_html( $num ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<section class="sec sec--area">
	<div class="wrap">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>AREA &amp; ACCESS</p>
			<h2 class="sec-head__title">対応エリアとアクセス</h2>
		</div>
		<div class="area">
			<div>
				<p class="area__head">対応エリア</p>
				<ul class="area__list">
					<?php foreach ( dcs_areas_main() as $a ) : ?>
						<li><?php echo esc_html( $a ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p class="area__head" style="margin-top:32px">一部地域を除きご相談ください</p>
				<ul class="area__list area__list--sub">
					<?php foreach ( dcs_areas_sub() as $a ) : ?>
						<li><?php echo esc_html( $a ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p class="area__note">
					上記以外の地域も、まずはご相談ください。可否をお答えします。<br>
					ご来社の際は、事前にお電話いただけると確実です（火曜・水曜が定休日ですが、事前にご連絡いただければ対応できます）。
				</p>
			</div>
			<div class="map">
				<iframe src="<?php echo esc_url( dcs_map_src() ); ?>" title="株式会社エスホームの所在地（栃木県宇都宮市平出町3563-3）" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		</div>
	</div>
</section>

<section class="sec sec--reason">
	<div class="wrap">
		<div class="sec-head sec-head--light">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>OUR STANCE</p>
			<h2 class="sec-head__title">私たちが約束すること</h2>
		</div>
		<div class="featgrid featgrid--light">
			<div class="featgrid__item">
				<p class="featgrid__num">01</p>
				<h3 class="featgrid__title">見積書の内訳を、すべて開示します</h3>
				<p>「一式」でまとめた見積書はお出ししません。どこにいくらかかっているのかが分かる形でご提示します。比較していただいて構いません。</p>
			</div>
			<div class="featgrid__item">
				<p class="featgrid__num">02</p>
				<h3 class="featgrid__title">性能を、数値でお伝えします</h3>
				<p>耐震等級は構造計算にもとづく等級3。気密は全棟で測定し、結果をお渡しします。「相当」という言葉は使いません。</p>
			</div>
			<div class="featgrid__item">
				<p class="featgrid__num">03</p>
				<h3 class="featgrid__title">急かしません</h3>
				<p>打ち合わせ回数にも間取り変更にも制限を設けていません。しつこい営業もいたしません。ご検討の時間はお客さまのものです。</p>
			</div>
			<div class="featgrid__item">
				<p class="featgrid__num">04</p>
				<h3 class="featgrid__title">できないことは、できないと言います</h3>
				<p>ご予算内で実現できない場合や、その間取りではエアコン1台での全館空調が成立しない場合は、正直にお伝えします。</p>
			</div>
		</div>
	</div>
</section>

<?php
dcs_cta();
get_footer();
