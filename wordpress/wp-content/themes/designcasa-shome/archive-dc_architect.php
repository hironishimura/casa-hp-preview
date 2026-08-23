<?php
/**
 * 建築家一覧
 *
 * 主要キーワード：宇都宮 デザイン住宅／デザイン住宅 宇都宮市／宇都宮市 デザイン
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

get_header();
dcs_breadcrumb();
?>

<section class="phero">
	<div class="wrap">
		<p class="phero__eyebrow"><span class="tick" aria-hidden="true"></span>ARCHITECT</p>
		<h1 class="phero__title">建築家紹介</h1>
		<p class="phero__lead">
			design casa に登録する建築家をご紹介します。住宅を数多く手がけてきた実力のある建築家が、
			宇都宮市・栃木県のあなたの敷地のために、一邸ずつ設計します。
			「どうやって依頼すればいいのか」「何を、どこまで伝えていいのか」。
			その部分は、加盟工務店である株式会社エスホームがあいだに入ってお引き受けします。
		</p>
	</div>
</section>

<section class="sec sec--archlist">
	<div class="wrap">
		<div class="sec-head">
			<h2 class="sec-head__title">建築家の選び方</h2>
			<p class="sec-head__note">
				建築家には、それぞれ得意な方向性があります。光をどう扱うか、素材をどう選ぶか、暮らしをどう整理するか。
				まずは作品集をご覧いただき、「これが好き」と感じる方向性を教えてください。
				そのうえで、宇都宮市の敷地条件と予算に合う建築家を、エスホームがご提案します。
			</p>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="archgrid">
				<?php
				while ( have_posts() ) :
					the_post();
					$office = dcs_meta( 'dcs_arch_office' );
					$policy = dcs_meta( 'dcs_arch_policy' );
					$base   = dcs_meta( 'dcs_arch_base' );
					?>
					<a class="archcard" href="<?php the_permalink(); ?>">
						<figure class="archcard__fig">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'dcs-card', array( 'alt' => esc_attr( get_the_title() . '｜design casa 登録建築家' ) ) ); ?>
							<?php else : ?>
								<span class="archcard__initial" aria-hidden="true"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
							<?php endif; ?>
						</figure>
						<div class="archcard__body">
							<h3 class="archcard__name"><?php the_title(); ?></h3>
							<?php if ( $office ) : ?><p class="archcard__office"><?php echo esc_html( $office ); ?></p><?php endif; ?>
							<?php if ( $policy ) : ?><p class="archcard__policy"><?php echo esc_html( $policy ); ?></p><?php endif; ?>
							<?php if ( $base ) : ?><p class="archcard__base"><?php echo esc_html( $base ); ?></p><?php endif; ?>
						</div>
					</a>
					<?php
				endwhile;
				?>
			</div>
		<?php endif; ?>

		<div class="notebox">
			<h2 class="notebox__title">建築家との家づくりに、専門知識は要りません</h2>
			<p>
				打ち合わせは建築家と直接3回。「LDKは20帖ほしい」ではなく、「休日は家族でどう過ごしたいか」を話してください。
				そこから間取りを組み立てるのが建築家の仕事です。
				難しい言葉は使いませんし、間に宇都宮市のエスホームが入って通訳します。
			</p>
			<p>
				<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">建築家との家づくりを相談する（無料）</a>
			</p>
		</div>
	</div>
</section>

<?php
dcs_cta();
get_footer();
