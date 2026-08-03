<?php
/**
 * 施工例 詳細
 *
 * 写真1枚ごとに設計意図のコメントを添えたギャラリーを表示します。
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
	$gallery = dcs_gallery_ids();
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
				<?php if ( $catch ) : ?>
					<p class="work__catch"><?php echo esc_html( $catch ); ?></p>
				<?php endif; ?>

				<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
					<ul class="chips">
						<?php foreach ( $terms as $t ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<dl class="deflist deflist--spec">
					<?php if ( $area ) : ?><div><dt>所在地</dt><dd><?php echo esc_html( $area ); ?></dd></div><?php endif; ?>
					<?php if ( $struct ) : ?><div><dt>構造・規模</dt><dd><?php echo esc_html( $struct ); ?></dd></div><?php endif; ?>
					<?php if ( $floor ) : ?><div><dt>延床面積</dt><dd><?php echo esc_html( $floor ); ?></dd></div><?php endif; ?>
					<?php if ( $land ) : ?><div><dt>敷地面積</dt><dd><?php echo esc_html( $land ); ?></dd></div><?php endif; ?>
					<?php if ( $family ) : ?><div><dt>家族構成</dt><dd><?php echo esc_html( $family ); ?></dd></div><?php endif; ?>
					<?php if ( $price ) : ?><div><dt>参考本体価格</dt><dd><?php echo esc_html( $price ); ?></dd></div><?php endif; ?>
					<?php if ( $done ) : ?><div><dt>竣工</dt><dd><?php echo esc_html( $done ); ?></dd></div><?php endif; ?>
					<?php if ( $arch_id ) : ?>
						<div><dt>設計</dt><dd><a href="<?php echo esc_url( get_permalink( $arch_id ) ); ?>"><?php echo esc_html( get_the_title( $arch_id ) ); ?></a></dd></div>
					<?php endif; ?>
				</dl>

				<div class="work__body">
					<?php the_content(); ?>
				</div>
			</div>
		</section>

		<?php if ( $gallery ) : ?>
			<section class="sec sec--gallery">
				<div class="wrap">
					<div class="sec-head">
						<p class="sec-head__eyebrow"><span class="tick" aria-hidden="true"></span>PHOTO &amp; DETAIL</p>
						<h2 class="sec-head__title">写真で見る、設計の意図</h2>
						<p class="sec-head__note">1枚ごとに、なぜそう設計したのかを添えています。<?php echo esc_html( count( $gallery ) ); ?>枚。</p>
					</div>

					<div class="gallery">
						<?php foreach ( $gallery as $i => $att_id ) : ?>
							<figure class="gallery__item<?php echo ( 0 === $i % 3 ) ? ' gallery__item--wide' : ''; ?> reveal">
								<?php
								echo wp_get_attachment_image(
									$att_id,
									( 0 === $i % 3 ) ? 'dcs-wide' : 'dcs-card',
									false,
									array(
										'loading' => $i < 2 ? 'eager' : 'lazy',
										'alt'     => esc_attr( get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ),
									)
								);
								?>
								<figcaption class="gallery__cap">
									<span class="gallery__no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
									<?php echo esc_html( wp_get_attachment_caption( $att_id ) ); ?>
								</figcaption>
							</figure>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="sec sec--worknav">
			<div class="wrap">
				<div class="notebox">
					<h2 class="notebox__title">この家のような住まいを、宇都宮市で。</h2>
					<p>
						こちらは design casa 加盟工務店による施工実例です。同じ建築家ネットワークを使い、
						株式会社エスホームが宇都宮市・栃木県で設計・施工いたします。
						耐震等級3・断熱等級6を標準仕様としているため、デザインを損なわずに冬あたたかく夏すずしい家になります。
					</p>
					<p>
						<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">この家について相談する（無料）</a>
					</p>
				</div>

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
