<?php
/**
 * 汎用の一覧（お知らせ・検索結果など）
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();
?>

<section class="phero">
	<div class="wrap">
		<h1 class="phero__title">
			<?php
			if ( is_search() ) {
				printf( '「%s」の検索結果', esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				echo 'お知らせ';
			}
			?>
		</h1>
	</div>
</section>

<section class="sec">
	<div class="wrap wrap--narrow">
		<?php if ( have_posts() ) : ?>
			<ul class="postlist">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li class="postlist__item">
						<a href="<?php the_permalink(); ?>">
							<time class="postlist__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
							<span class="postlist__title"><?php the_title(); ?></span>
						</a>
					</li>
					<?php
				endwhile;
				?>
			</ul>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => '前へ',
					'next_text' => '次へ',
				)
			);
			?>
		<?php else : ?>
			<p>該当する記事が見つかりませんでした。</p>
			<p><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>">トップページへ</a></p>
		<?php endif; ?>
	</div>
</section>

<?php
dcs_cta();
get_footer();
