<?php
/**
 * 仕様 詳細
 *
 * 本文（解説と建材写真）はブロックエディタで編集できます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

while ( have_posts() ) :
	the_post();

	$grade = dcs_meta( 'dcs_spec_grade' );
	$maker = dcs_meta( 'dcs_spec_maker' );
	$lead  = dcs_meta( 'dcs_spec_lead' );
	$cat   = get_the_terms( get_the_ID(), 'dc_spec_cat' );
	?>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="spec__hero">
			<?php the_post_thumbnail( 'dcs-wide', array( 'alt' => esc_attr( get_the_title() ), 'loading' => 'eager' ) ); ?>
		</div>
	<?php endif; ?>

	<section class="phero">
		<div class="wrap">
			<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span><?php echo ( $cat && ! is_wp_error( $cat ) ) ? esc_html( $cat[0]->name ) : 'SPECIFICATION'; ?></p>
			<h1 class="phero__title"><?php the_title(); ?></h1>
			<?php if ( $lead ) : ?><p class="phero__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
		</div>
	</section>

	<section class="sec sec--specdetail">
		<div class="wrap">
			<div class="specdetail">
				<div class="specdetail__main entry-content">
					<?php the_content(); ?>
				</div>

				<aside class="specdetail__side">
					<?php if ( $grade ) : ?>
						<div class="sidebox">
							<h2 class="sidebox__title">標準仕様</h2>
							<p class="sidebox__grade"><?php echo esc_html( $grade ); ?></p>
						</div>
					<?php endif; ?>

					<?php if ( $maker ) : ?>
						<div class="sidebox">
							<h2 class="sidebox__title">主な採用製品</h2>
							<ul class="sidebox__list">
								<?php foreach ( array_filter( array_map( 'trim', explode( "\n", $maker ) ) ) as $m ) : ?>
									<li><?php echo esc_html( $m ); ?></li>
								<?php endforeach; ?>
							</ul>
							<p class="sidebox__note">仕様は予告なく変更になる場合があります。最新の内容はお問い合わせください。</p>
						</div>
					<?php endif; ?>

					<div class="sidebox sidebox--cta">
						<h2 class="sidebox__title">仕様の資料をお送りします</h2>
						<p>標準仕様書と施工例集を無料でお届けします。宇都宮市・栃木県以外の方もお気軽にどうぞ。</p>
						<a class="btn btn--fill" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">資料請求（無料）</a>
					</div>

					<div class="sidebox">
						<h2 class="sidebox__title">ほかの仕様</h2>
						<ul class="sidebox__links">
							<?php
							$others = get_posts(
								array(
									'post_type'      => 'dc_spec',
									'posts_per_page' => -1,
									'post__not_in'   => array( get_the_ID() ),
									'orderby'        => 'menu_order date',
									'order'          => 'ASC',
								)
							);
							foreach ( $others as $o ) :
								?>
								<li><a href="<?php echo esc_url( get_permalink( $o ) ); ?>"><?php echo esc_html( get_the_title( $o ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<?php
endwhile;

dcs_cta();
get_footer();
