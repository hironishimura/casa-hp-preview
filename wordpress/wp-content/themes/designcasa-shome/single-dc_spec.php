<?php
/**
 * 仕様 詳細
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

while ( have_posts() ) :
	the_post();

	$grade   = dcs_meta( 'dcs_spec_grade' );
	$maker   = dcs_meta( 'dcs_spec_maker' );
	$lead    = dcs_meta( 'dcs_spec_lead' );
	$cat     = get_the_terms( get_the_ID(), 'dc_spec_cat' );
	$gallery = dcs_gallery_ids( 0, 'dcs_spec_gallery' );
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
				<div class="specdetail__main prose">
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

	<?php if ( $gallery ) : ?>
		<section class="sec sec--specgallery">
			<div class="wrap">
				<div class="sec-head">
					<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>MATERIAL &amp; DETAIL</p>
					<h2 class="sec-head__title">写真で見る、この仕様</h2>
					<p class="sec-head__note">実際の建材・製品と、それが使われている場面です。<?php echo esc_html( count( $gallery ) ); ?>枚。</p>
				</div>
				<div class="matgrid">
					<?php foreach ( $gallery as $i => $att_id ) : ?>
						<figure class="matgrid__item reveal">
							<?php
							echo wp_get_attachment_image(
								$att_id,
								'dcs-card',
								false,
								array(
									'loading' => $i < 2 ? 'eager' : 'lazy',
									'alt'     => esc_attr( get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ),
								)
							);
							?>
							<figcaption class="matgrid__cap">
								<span class="matgrid__no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<?php echo esc_html( wp_get_attachment_caption( $att_id ) ); ?>
							</figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
				<p class="matgrid__credit">写真：design casa 施工実例／各メーカー提供素材。仕様は予告なく変更になる場合があります。</p>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

dcs_cta();
get_footer();
