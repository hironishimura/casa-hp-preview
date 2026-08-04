<?php
/**
 * 施工例 詳細
 *
 * 本文（写真とコメント）はブロックエディタで編集できます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

while ( have_posts() ) :
	the_post();

	$id      = get_the_ID();
	$catch   = dcs_meta( 'dcs_work_catch' );
	$area    = dcs_meta( 'dcs_work_area' );
	$struct  = dcs_meta( 'dcs_work_structure' );
	$floor   = dcs_meta( 'dcs_work_floor' );
	$land    = dcs_meta( 'dcs_work_land' );
	$family  = dcs_meta( 'dcs_work_family' );
	$price   = dcs_meta( 'dcs_work_price' );
	$done    = dcs_meta( 'dcs_work_completion' );
	$arch_id = (int) dcs_meta( 'dcs_work_architect' );
	$terms   = get_the_terms( $id, 'dc_work_tag' );
	?>

	<article class="work">

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="work__hero">
				<?php the_post_thumbnail( 'full', array( 'alt' => esc_attr( get_the_title() . '｜' . $area . 'の注文住宅 施工例' ) ) ); ?>
			</div>
		<?php endif; ?>

		<section class="sec sec--workhead">
			<div class="wrap">
				<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>CASE STUDY<?php echo dcs_meta( 'dcs_work_no' ) ? ' / No.' . esc_html( dcs_meta( 'dcs_work_no' ) ) : ''; ?></p>
				<h1 class="phero__title"><?php the_title(); ?></h1>

				<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
					<ul class="chips">
						<?php foreach ( $terms as $t ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<dl class="deflist deflist--spec">
					<?php
					dcs_dl_row( '所在地', esc_html( $area ) );
					dcs_dl_row( '構造・規模', esc_html( $struct ) );
					dcs_dl_row( '延床面積', esc_html( $floor ) );
					dcs_dl_row( '敷地面積', esc_html( $land ) );
					dcs_dl_row( '家族構成', esc_html( $family ) );
					dcs_dl_row( '参考本体価格', esc_html( $price ) );
					dcs_dl_row( '竣工', esc_html( $done ) );
					if ( $arch_id ) {
						dcs_dl_row( '設計', sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $arch_id ) ), esc_html( get_the_title( $arch_id ) ) ) );
					}
					?>
				</dl>
			</div>
		</section>

		<div class="entry-content entry-content--work">
			<?php the_content(); ?>
		</div>

		<section class="sec sec--worknav">
			<div class="wrap">
				<nav class="postnav" aria-label="施工例の移動">
					<?php
					$prev = get_previous_post();
					$next = get_next_post();
					?>
					<div class="postnav__side">
						<?php if ( $next ) : ?>
							<a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><span>前の施工例</span><?php echo esc_html( get_the_title( $next ) ); ?></a>
						<?php endif; ?>
					</div>
					<a class="postnav__index" href="<?php echo esc_url( get_post_type_archive_link( 'dc_work' ) ); ?>">一覧へ</a>
					<div class="postnav__side postnav__side--right">
						<?php if ( $prev ) : ?>
							<a href="<?php echo esc_url( get_permalink( $prev ) ); ?>"><span>次の施工例</span><?php echo esc_html( get_the_title( $prev ) ); ?></a>
						<?php endif; ?>
					</div>
				</nav>

				<?php
				$related = new WP_Query(
					array(
						'post_type'      => 'dc_work',
						'posts_per_page' => 3,
						'post__not_in'   => array( $id ),
						'orderby'        => 'rand',
						'tax_query'      => ( $terms && ! is_wp_error( $terms ) ) ? array( // phpcs:ignore WordPress.DB.SlowDBQuery
							array(
								'taxonomy' => 'dc_work_tag',
								'field'    => 'term_id',
								'terms'    => wp_list_pluck( $terms, 'term_id' ),
							),
						) : array(),
					)
				);
				if ( $related->have_posts() ) :
					?>
					<h2 class="sec-head__title" style="margin:64px 0 28px;font-size:clamp(20px,2.6vw,28px)">似た雰囲気の施工例</h2>
					<div class="works">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							dcs_work_card();
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	</article>

	<?php
endwhile;

dcs_cta();
get_footer();
