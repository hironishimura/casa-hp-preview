<?php
/**
 * 家の仕様一覧
 *
 * 主要キーワード：宇都宮市 高気密高断熱／宇都宮 地震に強い家／宇都宮市 全館空調／宇都宮 エコハウス／エアコン1台
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>SPECIFICATION</p>
		<h1 class="phero__title">家の仕様</h1>
		<p class="phero__lead">
			耐震等級3・断熱等級6が標準仕様です。オプションではありません。
			宇都宮市の冬の冷え込みと夏の蒸し暑さを前提に、性能から決めた仕様を公開します。
			高気密高断熱、エアコン1台の全館空調、地震に強い構造まで、栃木県で家を建てる方に知っておいていただきたい中身です。
		</p>
	</div>
</section>

<section class="sec sec--figures">
	<div class="wrap">
		<div class="sec-head sec-head--light">
			<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>STANDARD</p>
			<h2 class="sec-head__title">まずは、この2つ。</h2>
			<p class="sec-head__note">
				<strong>構造は耐震等級3、断熱は断熱等級6</strong>。この2つが標準です。
				性能は後から足せません。だから最初から削らないと決めています。
			</p>
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
	</div>
</section>

<section class="sec sec--speclist">
	<div class="wrap">
		<div class="sec-head">
			<h2 class="sec-head__title">仕様の一覧</h2>
			<p class="sec-head__note">構造・断熱・空調から、床材・キッチン・階段まで。採用しているメーカーと製品も公開しています。</p>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="speclist">
				<?php
				while ( have_posts() ) :
					the_post();
					$cat   = get_the_terms( get_the_ID(), 'dc_spec_cat' );
					$grade = dcs_meta( 'dcs_spec_grade' );
					$maker = dcs_meta( 'dcs_spec_maker' );
					?>
					<article class="speccard<?php echo has_post_thumbnail() ? ' speccard--img' : ''; ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="speccard__fig" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'dcs-card', array( 'alt' => '' ) ); ?>
							</a>
						<?php endif; ?>
						<div class="speccard__head">
							<?php if ( $cat && ! is_wp_error( $cat ) ) : ?>
								<p class="speccard__cat"><?php echo esc_html( $cat[0]->name ); ?></p>
							<?php endif; ?>
							<h3 class="speccard__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php if ( $grade ) : ?><p class="speccard__grade"><?php echo esc_html( $grade ); ?></p><?php endif; ?>
						</div>
						<div class="speccard__body">
							<p class="speccard__lead"><?php echo esc_html( dcs_meta( 'dcs_spec_lead' ) ); ?></p>
							<?php if ( $maker ) : ?>
								<ul class="speccard__maker">
									<?php foreach ( array_filter( array_map( 'trim', explode( "\n", $maker ) ) ) as $m ) : ?>
										<li><?php echo esc_html( $m ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<p class="speccard__more"><a href="<?php the_permalink(); ?>">くわしく見る →</a></p>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>
		<?php endif; ?>

		<div class="notebox">
			<h2 class="notebox__title">仕様は、見学して確かめてください</h2>
			<p>
				断熱や気密は、カタログの数字より実際の体感が分かりやすい部分です。
				宇都宮市内で完成見学会を開催している時期であれば、ぜひ足を運んでみてください。
				廊下や脱衣室の温度を、その場で確かめていただけます。
			</p>
			<p><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">見学会の情報を受け取る（無料）</a></p>
		</div>
	</div>
</section>

<?php
dcs_cta();
get_footer();
