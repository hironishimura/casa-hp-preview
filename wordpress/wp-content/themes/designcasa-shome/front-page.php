<?php
/**
 * トップページ
 *
 * 主要キーワード：宇都宮市 工務店／宇都宮 注文住宅／栃木県 工務店／栃木県 注文住宅
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="hero">
	<div class="hero__media">
		<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/common/hero.jpg' ) ); ?>"
			alt="宇都宮市の工務店エスホームが建てる、建築家設計の注文住宅の夜景"
			width="1600" height="1067" fetchpriority="high" decoding="async">
	</div>
	<div class="hero__body">
		<p class="hero__eyebrow"><span class="tick" aria-hidden="true"></span>宇都宮市の工務店｜建築家とつくる注文住宅</p>
		<h1 class="hero__title">
			デザインも、性能も。<br>
			どちらも諦めない<br class="br-sp">宇都宮の家。
		</h1>
		<p class="hero__lead">
			全国の建築家とつくる注文住宅ブランド <strong>design casa</strong>。その加盟工務店として、
			株式会社エスホームが宇都宮市・栃木県で design casa の家をお届けします。
			耐震等級3・断熱等級6を標準仕様に、エアコン1台で家じゅうの温度がそろう高気密高断熱の住まいを。
		</p>
		<div class="hero__actions">
			<a class="btn btn--fill" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				資料請求（無料）
				<span class="btn__note">施工例集をお送りします</span>
			</a>
			<a class="btn btn--line" href="<?php echo esc_url( get_post_type_archive_link( 'dc_work' ) ); ?>">
				施工例を見る
				<span class="btn__note">67邸を掲載中</span>
			</a>
		</div>
	</div>
	<p class="hero__scroll">SCROLL<span aria-hidden="true"></span></p>
</section>

<div class="band">
	<div class="band__inner">
		<p class="band__label">INFORMATION</p>
		<p class="band__text">
			<strong>宇都宮市・栃木県で注文住宅をご検討の方へ。</strong>
			土地探しからのご相談、資金計画のご相談も無料で承っています。
			打ち合わせ回数・間取り変更に制限は設けていません。
			お電話は <a href="<?php echo esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ); ?>"><?php echo esc_html( dcs_company( 'freedial' ) ); ?></a>（<?php echo esc_html( dcs_company( 'hours' ) ); ?>／火・水定休）へ。
		</p>
	</div>
</div>

<!-- ============ design casa とは ============ -->
<section class="sec sec--about">
	<div class="wrap">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>ABOUT design casa</p>
			<h2 class="sec-head__title">建築家・工務店・あなた。<br>3者でつくる、こだわりの家。</h2>
			<p class="sec-head__note">
				design casa は「建築家と建てる家」を、もっと多くの人に届けるための仕組みです。
				全国のネットワークで建材を共同購入し、設計から施工までの流れを標準化することで、
				建築家の設計料を<strong>建築費の約4%</strong>に抑えています。
				一般に建築家へ直接依頼した場合の10〜15%と比べて、大きな差になります。
			</p>
		</div>

		<div class="about">
			<div class="about__text">
				<p>「デザイン性の高い家は高い」「建築家は敷居が高い」。宇都宮市でも、そう思って諦めてしまう方が少なくありません。</p>
				<p>けれど、規格プランから選ぶ家と、建築家が敷地を見て設計する家とでは、10年後20年後の暮らしやすさが変わってきます。土地の形、日の入り方、隣家の窓の位置。それらを読んだうえで窓の高さひとつを決められるかどうかが、住み心地を分けます。</p>
				<p>design casa なら、実績のある建築家と直接3回打ち合わせをしながら、あなたの敷地のためだけのプランをつくれます。そして施工と保証は、宇都宮市の地元工務店である株式会社エスホームが担当します。</p>
				<p><a href="<?php echo esc_url( home_url( '/concept/' ) ); ?>">デザインカーサとは →</a></p>
			</div>
			<figure class="about__fig">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/common/concept.jpg' ) ); ?>"
					alt="建築家が設計した中庭のある家。夜の照明が中庭を照らす" loading="lazy" decoding="async" width="1600" height="1067">
				<figcaption>design casa 施工実例／中庭のある平屋</figcaption>
			</figure>
		</div>

		<div class="trio">
			<div class="trio__item">
				<h3 class="trio__role">建築家</h3>
				<p class="trio__desc">敷地と暮らし方を読み、デザインと機能を両立させたプランを設計します。打ち合わせは直接3回。</p>
			</div>
			<div class="trio__item trio__item--center">
				<h3 class="trio__role">エスホーム</h3>
				<p class="trio__desc">宇都宮市の工務店として、資金計画・土地探しから施工、引き渡し後の点検までを一貫して担当します。</p>
			</div>
			<div class="trio__item">
				<h3 class="trio__role">あなた</h3>
				<p class="trio__desc">「休日をどう過ごしたいか」を話してください。専門知識は必要ありません。</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ 数値でみる標準仕様 ============ -->
<section class="sec sec--figures">
	<div class="wrap">
		<div class="sec-head sec-head--light">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>STANDARD</p>
			<h2 class="sec-head__title">性能は、削らない。</h2>
			<p class="sec-head__note">宇都宮市の冬の冷え込みと、夏の蒸し暑さ。栃木県の気候を前提に決めた標準仕様です。オプションではありません。</p>
		</div>
		<ul class="figures">
			<?php foreach ( dcs_spec_figures() as $f ) : ?>
				<li class="figures__item">
					<p class="figures__num"><?php echo esc_html( $f['num'] ); ?><span><?php echo esc_html( $f['unit'] ); ?></span></p>
					<h3 class="figures__label"><?php echo esc_html( $f['label'] ); ?></h3>
					<p class="figures__note"><?php echo esc_html( $f['note'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="figures__more">
			<a class="btn btn--dark btn--dark-on-ink" href="<?php echo esc_url( get_post_type_archive_link( 'dc_spec' ) ); ?>">家の仕様をすべて見る</a>
		</p>
	</div>
</section>

<!-- ============ 選ばれる理由 ============ -->
<section class="sec sec--reason">
	<div class="wrap">
		<div class="sec-head sec-head--light">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>WHY S HOME</p>
			<h2 class="sec-head__title">宇都宮で工務店を選ぶとき、<br>見ていただきたい4つのこと。</h2>
		</div>
		<div class="reason">
			<?php foreach ( dcs_reasons() as $i => $r ) : ?>
				<div class="reason__item<?php echo ( $i % 2 ) ? ' reason__item--rev' : ''; ?> reveal">
					<figure class="reason__fig">
						<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/common/' . $r['img'] ) ); ?>"
							alt="<?php echo esc_attr( $r['title'] ); ?>" loading="lazy" decoding="async" width="1600" height="1067">
					</figure>
					<div class="reason__body">
						<p class="reason__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
						<h3 class="reason__title"><?php echo esc_html( $r['title'] ); ?></h3>
						<p class="reason__desc"><?php echo esc_html( $r['body'] ); ?></p>
						<p class="reason__link"><a href="<?php echo esc_url( home_url( $r['link'] ) ); ?>">くわしく見る →</a></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ 施工例 ============ -->
<section class="sec sec--works">
	<div class="wrap">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>CASE STUDY</p>
			<h2 class="sec-head__title">施工例</h2>
			<p class="sec-head__note">
				design casa の建築家が設計した実例です。平屋、中庭のある家、ガレージハウス、2階リビング。
				宇都宮市で建てる家のイメージづくりにお役立てください。
			</p>
		</div>

		<?php
		$works = new WP_Query(
			array(
				'post_type'      => 'dc_work',
				'posts_per_page' => 6,
				'orderby'        => array(
					'menu_order' => 'DESC',
					'date'       => 'DESC',
				),
			)
		);
		if ( $works->have_posts() ) :
			?>
			<div class="works">
				<?php
				while ( $works->have_posts() ) :
					$works->the_post();
					dcs_work_card();
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<p class="works__foot">
				<a class="btn btn--dark" href="<?php echo esc_url( get_post_type_archive_link( 'dc_work' ) ); ?>">施工例をすべて見る</a>
			</p>
		<?php else : ?>
			<p class="works__foot">施工例はまもなく公開します。</p>
		<?php endif; ?>

		<?php
		$tags = get_terms(
			array(
				'taxonomy'   => 'dc_work_tag',
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => 14,
			)
		);
		if ( $tags && ! is_wp_error( $tags ) ) :
			?>
			<div class="tagcloud">
				<p class="tagcloud__label">特徴から探す</p>
				<ul class="tagcloud__list">
					<?php foreach ( $tags as $t ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?><span><?php echo esc_html( $t->count ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- ============ 建築家 ============ -->
<section class="sec sec--architect">
	<div class="wrap">
		<div class="sec-head sec-head--light">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>ARCHITECT</p>
			<h2 class="sec-head__title">設計するのは、<br>実績のある建築家です。</h2>
			<p class="sec-head__note">
				design casa に登録する建築家をご紹介します。作品集をご覧いただきながら、
				あなたの感覚に近い建築家を一緒に選んでいきましょう。
			</p>
		</div>

		<?php
		$arch = new WP_Query(
			array(
				'post_type'      => 'dc_architect',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
			)
		);
		if ( $arch->have_posts() ) :
			?>
			<ul class="names">
				<?php
				while ( $arch->have_posts() ) :
					$arch->the_post();
					?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
			<p class="works__foot works__foot--light">
				<a class="btn btn--dark btn--dark-on-ink" href="<?php echo esc_url( get_post_type_archive_link( 'dc_architect' ) ); ?>">建築家の紹介を見る</a>
			</p>
		<?php endif; ?>
	</div>
</section>

<!-- ============ 家づくりの流れ ============ -->
<section class="sec sec--flow">
	<div class="wrap">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>FLOW</p>
			<h2 class="sec-head__title">家づくりの流れ</h2>
			<p class="sec-head__note">ご相談からお引き渡しまで14ステップ。ここでは最初の4つをご紹介します。</p>
		</div>
		<div class="flow">
			<?php foreach ( array_slice( dcs_flow_steps(), 0, 4 ) as $i => $s ) : ?>
				<div class="flow__step">
					<p class="flow__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
					<div class="flow__body">
						<h3 class="flow__title"><?php echo esc_html( $s['title'] ); ?><span class="flow__who"><?php echo esc_html( $s['who'] ); ?></span></h3>
						<p><?php echo esc_html( $s['body'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="works__foot">
			<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/flow/' ) ); ?>">14ステップすべてを見る</a>
		</p>
	</div>
</section>

<!-- ============ 対応エリア ============ -->
<section class="sec sec--area">
	<div class="wrap">
		<div class="sec-head">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>AREA</p>
			<h2 class="sec-head__title">対応エリア</h2>
			<p class="sec-head__note">宇都宮市を中心に、栃木県内で注文住宅の設計・施工を承っています。</p>
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
				<p class="area__note">上記以外の地域も、まずはご相談ください。可否をお答えします。</p>
			</div>
			<div class="map">
				<iframe src="<?php echo esc_url( dcs_map_src() ); ?>" title="株式会社エスホームの所在地（栃木県宇都宮市平出町）" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		</div>
	</div>
</section>

<!-- ============ FAQ ============ -->
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

<!-- ============ 会社 ============ -->
<section class="sec sec--company">
	<div class="wrap">
		<div class="sec-head sec-head--light">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>COMPANY</p>
			<h2 class="sec-head__title">施工会社について</h2>
		</div>
		<div class="company">
			<div class="company__lead">
				<h3 class="company__name"><?php echo esc_html( dcs_company( 'name' ) ); ?></h3>
				<p class="company__text">
					宇都宮市平出町の工務店です。
					「大手ほど規格に縛られず、ローコスト系ほど性能を削らない」。
					その中間にある選択肢でありたいと考えています。
					設計から施工、引き渡し後の点検まで、地元の会社が一貫して責任を持ちます。
				</p>
				<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/company/' ) ); ?>">施工会社を紹介する</a>
			</div>
			<dl class="company__dl">
				<?php
				dcs_dl_row( '商号', esc_html( dcs_company( 'name' ) ) );
				dcs_dl_row( '所在地', '〒' . esc_html( dcs_company( 'zip' ) ) . ' ' . esc_html( dcs_company( 'address' ) ) );
				dcs_dl_row( '設立', esc_html( dcs_company( 'founded' ) ) );
				dcs_dl_row( '代表者', esc_html( dcs_company( 'ceo' ) ) );
				dcs_dl_row( '建設業許可', esc_html( dcs_company( 'license_kensetsu' ) ) );
				dcs_dl_row( '宅建業免許', esc_html( dcs_company( 'license_takken' ) ) );
				dcs_dl_row( '建築士事務所', esc_html( dcs_company( 'license_kenchikushi' ) ) );
				dcs_dl_row( '営業時間', esc_html( dcs_company( 'hours' ) ) . '／定休日 火曜・水曜' );
				dcs_dl_row(
					'TEL',
					sprintf(
						'<a href="%1$s">%2$s</a>／FAX %3$s',
						esc_attr( dcs_tel_href( dcs_company( 'tel' ) ) ),
						esc_html( dcs_company( 'tel' ) ),
						esc_html( dcs_company( 'fax' ) )
					)
				);
				?>
			</dl>
		</div>
	</div>
</section>

<?php
dcs_cta();
get_footer();
