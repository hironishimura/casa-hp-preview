<?php
/**
 * 汎用の固定ページ
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
		<div class="wrap">
			<h1 class="phero__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="phero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="sec sec--legal">
		<div class="wrap wrap--narrow">
			<div class="prose">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<nav class="pager">', 'after' => '</nav>' ) );
				?>
			</div>
		</div>
	</section>
	<?php
endwhile;

dcs_cta();
get_footer();
