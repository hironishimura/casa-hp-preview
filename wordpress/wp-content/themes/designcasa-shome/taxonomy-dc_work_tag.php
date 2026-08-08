<?php
/**
 * 施工例一覧
 *
 * 主要キーワード：宇都宮 注文住宅／平屋 宇都宮市／注文住宅 栃木県
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();

$is_tax = is_tax( 'dc_work_tag' );
$term   = $is_tax ? get_queried_object() : null;
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>CASE STUDY</p>
		<h1 class="phero__title">
			<?php echo $is_tax ? esc_html( $term->name ) . 'の施工例' : '施工例一覧'; ?>
		</h1>
		<p class="phero__lead">
			<?php if ( $is_tax ) : ?>
				「<?php echo esc_html( $term->name ); ?>」を取り入れた design casa の施工例です。
				宇都宮市・栃木県で同じような家を建てたい方は、株式会社エスホームまでご相談ください。
				耐震等級3・断熱等級6以上の標準仕様で、この雰囲気を栃木の気候に合わせた性能とともにお届けします。
			<?php else : ?>
				design casa の建築家が設計した注文住宅の施工例です。新しいものから順に掲載しています。
				平屋・2階リビング・中庭のある家・ガレージハウスなど、宇都宮市で家づくりを考える際のイメージづくりにお使いください。
				写真の1枚ごとに、設計上の意図をコメントで添えています。
			<?php endif; ?>
		</p>
	</div>
</section>

<?php
$tags = get_terms(
	array(
		'taxonomy'   => 'dc_work_tag',
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
if ( $tags && ! is_wp_error( $tags ) ) :
	?>
	<nav class="filter" aria-label="施工例の絞り込み">
		<div class="wrap">
			<ul class="filter__list">
				<li><a class="<?php echo $is_tax ? '' : 'is-current'; ?>" href="<?php echo esc_url( get_post_type_archive_link( 'dc_work' ) ); ?>">すべて</a></li>
				<?php foreach ( $tags as $t ) : ?>
					<li>
						<a class="<?php echo ( $is_tax && (int) $term->term_id === (int) $t->term_id ) ? 'is-current' : ''; ?>"
							href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?><span><?php echo esc_html( $t->count ); ?></span></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>
<?php endif; ?>

<section class="sec sec--works">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="works">
				<?php
				while ( have_posts() ) :
					the_post();
					dcs_work_card();
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
			<p>該当する施工例が見つかりませんでした。</p>
		<?php endif; ?>

		<div class="notebox">
			<h2 class="notebox__title">宇都宮市で注文住宅をご検討中の方へ</h2>
			<p>
				掲載している施工例は、全国の design casa 加盟工務店が手がけた実例です（掲載写真：design casa）。
				同じ建築家に設計を依頼し、株式会社エスホームが宇都宮市・栃木県で施工します。
				栃木県の気候に合わせ、耐震等級3・断熱等級6以上・エアコン1台の全館空調を標準仕様として組み込んだうえで、
				このデザインを実現します。
			</p>
			<p>
				<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">施工例集を無料で取り寄せる</a>
			</p>
		</div>
	</div>
</section>

<?php
dcs_cta();
get_footer();
