<?php
/**
 * 404ページ
 *
 * 行き止まりにせず、施工例と問い合わせへ導きます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>404 NOT FOUND</p>
		<h1 class="phero__title">ページが見つかりませんでした</h1>
		<p class="phero__lead">
			お探しのページは移動または削除された可能性があります。<br>
			下のリンクからお探しください。
		</p>
	</div>
</section>

<section class="sec">
	<div class="wrap wrap--narrow">
		<ul class="linklist">
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">トップページ</a></li>
			<li><a href="<?php echo esc_url( home_url( '/concept/' ) ); ?>">デザインカーサとは</a></li>
			<li><a href="<?php echo esc_url( get_post_type_archive_link( 'dc_work' ) ); ?>">施工例一覧</a></li>
			<li><a href="<?php echo esc_url( get_post_type_archive_link( 'dc_architect' ) ); ?>">建築家紹介</a></li>
			<li><a href="<?php echo esc_url( home_url( '/flow/' ) ); ?>">家づくりの流れ</a></li>
			<li><a href="<?php echo esc_url( get_post_type_archive_link( 'dc_spec' ) ); ?>">家の仕様</a></li>
			<li><a href="<?php echo esc_url( home_url( '/company/' ) ); ?>">施工会社紹介</a></li>
			<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">資料請求・お問い合わせ</a></li>
		</ul>
	</div>
</section>

<?php
dcs_cta();
get_footer();
