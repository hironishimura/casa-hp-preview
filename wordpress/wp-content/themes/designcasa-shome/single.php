<?php
/**
 * 汎用の投稿詳細（お知らせなど）
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

while ( have_posts() ) :
	the_post();
	?>
	<section class="phero">
		<div class="wrap wrap--narrow">
			<time class="phero__eyebrow" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><span class="tick" aria-hidden="true"></span><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
			<h1 class="phero__title"><?php the_title(); ?></h1>
		</div>
	</section>

	<section class="sec sec--legal">
		<div class="wrap wrap--narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="prose__hero"><?php the_post_thumbnail( 'dcs-wide' ); ?></figure>
			<?php endif; ?>
			<div class="prose">
				<?php the_content(); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

dcs_cta();
get_footer();
