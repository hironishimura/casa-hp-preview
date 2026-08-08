<?php
/**
 * 建築家 詳細
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

while ( have_posts() ) :
	the_post();

	$office = dcs_meta( 'dcs_arch_office' );
	$kana   = dcs_meta( 'dcs_arch_kana' );
	$base   = dcs_meta( 'dcs_arch_base' );
	$policy = dcs_meta( 'dcs_arch_policy' );
	$url    = dcs_meta( 'dcs_arch_url' );
	?>

	<section class="phero">
		<div class="wrap">
			<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>ARCHITECT</p>
			<h1 class="phero__title"><?php the_title(); ?>
				<?php if ( $kana ) : ?><span class="phero__kana"><?php echo esc_html( $kana ); ?></span><?php endif; ?>
			</h1>
			<?php if ( $office ) : ?><p class="phero__lead"><?php echo esc_html( $office ); ?><?php echo $base ? '（' . esc_html( $base ) . '）' : ''; ?></p><?php endif; ?>
		</div>
	</section>

	<section class="sec sec--archdetail">
		<div class="wrap">
			<div class="archdetail">
				<div class="archdetail__fig">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'dcs-card', array( 'alt' => esc_attr( get_the_title() . '｜' . $office ) ) ); ?>
					<?php endif; ?>
					<?php if ( $url ) : ?>
						<p class="archdetail__url"><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">事務所サイトを見る</a></p>
					<?php endif; ?>
				</div>

				<div class="archdetail__body">
					<?php if ( $policy ) : ?>
						<p class="archdetail__policy"><?php echo esc_html( $policy ); ?></p>
					<?php endif; ?>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<div class="notebox notebox--inline">
						<h2 class="notebox__title">この建築家と、宇都宮で家を建てる</h2>
						<p>
							design casa では、建築家と直接3回打ち合わせをしながらプランをつくります。
							施工と保証は宇都宮市の工務店・株式会社エスホームが担当し、
							耐震等級3・断熱等級6以上の標準仕様で仕上げます。
						</p>
						<p><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">相談する（無料）</a></p>
					</div>
				</div>
			</div>

			<?php
			$works = new WP_Query(
				array(
					'post_type'      => 'dc_work',
					'posts_per_page' => 3,
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
						array(
							'key'   => 'dcs_work_architect',
							'value' => get_the_ID(),
						),
					),
				)
			);
			if ( $works->have_posts() ) :
				?>
				<h2 class="sec-head__title" style="margin:64px 0 28px;font-size:clamp(20px,2.6vw,28px)">この建築家の施工例</h2>
				<div class="works">
					<?php
					while ( $works->have_posts() ) :
						$works->the_post();
						dcs_work_card();
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>

			<nav class="postnav" aria-label="建築家の移動">
				<div class="postnav__side"></div>
				<a class="postnav__index" href="<?php echo esc_url( get_post_type_archive_link( 'dc_architect' ) ); ?>">建築家一覧へ</a>
				<div class="postnav__side postnav__side--right"></div>
			</nav>
		</div>
	</section>

	<?php
endwhile;

dcs_cta();
get_footer();
