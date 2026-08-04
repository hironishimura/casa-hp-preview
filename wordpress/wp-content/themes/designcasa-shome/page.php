<?php
/**
 * 固定ページ（すべて共通）
 *
 * 本文はブロックエディタで編集できます。
 * 見出し下のリード文は「抜粋」欄に入力してください。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();

$dcs_is_front = is_front_page();

if ( ! $dcs_is_front ) {
	dcs_breadcrumb();
}

while ( have_posts() ) :
	the_post();

	if ( ! $dcs_is_front ) :
		?>
		<section class="phero">
			<div class="wrap">
				<h1 class="phero__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="phero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	endif;
	?>

	<div class="entry-content<?php echo $dcs_is_front ? ' entry-content--front' : ''; ?>">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="pager">',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php
endwhile;

if ( ! is_page( 'contact' ) ) {
	dcs_cta();
}

get_footer();
