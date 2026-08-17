<?php
/**
 * 家の仕様（カテゴリ別）
 *
 * これが無いと index.php にフォールバックし、日付つきの素っ気ない一覧になってしまう。
 *
 * 主要キーワード：宇都宮市 高気密高断熱／宇都宮 地震に強い家／宇都宮市 全館空調
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

$term = get_queried_object();
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>SPECIFICATION</p>
		<h1 class="phero__title"><?php echo esc_html( $term->name ); ?>の仕様</h1>
		<p class="phero__lead">
			<?php if ( $term->description ) : ?>
				<?php echo esc_html( $term->description ); ?>
			<?php else : ?>
				宇都宮市の工務店エスホームの標準仕様のうち、「<?php echo esc_html( $term->name ); ?>」に関する内容です。
				耐震等級3・断熱等級6以上は、オプションではなく標準としています。
				栃木県の冬の冷え込みと夏の蒸し暑さを前提に、性能から決めた中身を公開します。
			<?php endif; ?>
		</p>
	</div>
</section>

<?php
$cats = get_terms(
	array(
		'taxonomy'   => 'dc_spec_cat',
		'hide_empty' => true,
	)
);
if ( $cats && ! is_wp_error( $cats ) ) :
	?>
	<nav class="filter" aria-label="仕様の絞り込み">
		<div class="wrap">
			<ul class="filter__list">
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'dc_spec' ) ); ?>">すべて</a></li>
				<?php foreach ( $cats as $c ) : ?>
					<li>
						<a class="<?php echo ( (int) $term->term_id === (int) $c->term_id ) ? 'is-current' : ''; ?>"
							href="<?php echo esc_url( get_term_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?><span><?php echo esc_html( $c->count ); ?></span></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>
<?php endif; ?>

<section class="sec sec--speclist">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="speclist">
				<?php
				while ( have_posts() ) :
					the_post();
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
							<p class="speccard__cat"><?php echo esc_html( $term->name ); ?></p>
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

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => '前へ',
					'next_text' => '次へ',
					'class'     => 'pager',
				)
			);
			?>
		<?php else : ?>
			<p>該当する仕様が見つかりませんでした。</p>
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
