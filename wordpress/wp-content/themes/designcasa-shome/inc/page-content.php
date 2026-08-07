<?php
/**
 * 各ページの本文（ブロック）を組み立てる
 *
 * ここで作った内容が、そのまま管理画面のブロックエディタで編集できます。
 * 自動で増える部分（施工例一覧・建築家一覧など）はショートコードブロックにしています。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * セクションの見出しブロックをまとめて作る。
 *
 * @param string $eyebrow 英字ラベル。
 * @param string $title   見出し。
 * @param string $note    補足文。
 * @return string
 */
function dcs_b_sechead( $eyebrow, $title, $note = '' ) {
	$out = array();
	if ( $eyebrow ) {
		$out[] = dcs_b_paragraph( '<span class="tick"></span>' . esc_html( $eyebrow ), 'sec-head__eyebrow' );
	}
	$out[] = dcs_b_heading( 2, $title, 'sec-head__title' );
	if ( $note ) {
		$out[] = dcs_b_paragraph( $note, 'sec-head__note' );
	}

	return dcs_b_join( $out );
}

/* =========================================================
   トップページ
   ========================================================= */

/**
 * トップページの本文。
 *
 * @return string
 */
function dcs_page_home_blocks() {
	$hero = dcs_import_common( 'hero.jpg', '宇都宮市の工務店エスホームが建てる、建築家が設計した注文住宅の夜景' );
	$url  = $hero ? wp_get_attachment_image_url( $hero, 'full' ) : '';

	$b = array();

	/* ---- ヒーロー（カバーブロック） ---- */
	$cover_attr = wp_json_encode(
		array(
			'url'        => $url,
			'id'         => $hero,
			'dimRatio'   => 50,
			'overlayColor' => 'ink',
			'minHeight'  => 88,
			'minHeightUnit' => 'vh',
			'className'  => 'hero-block',
			'layout'     => array( 'type' => 'constrained' ),
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	$cover_inner = dcs_b_join(
		array(
			dcs_b_paragraph( '<span class="tick"></span>注文住宅 宇都宮｜建築家とつくる design casa', 'hero__eyebrow' ),
			dcs_b_heading( 1, 'デザインも、性能も。どちらも諦めない宇都宮の注文住宅。', 'hero__title' ),
			dcs_b_paragraph(
				'全国の建築家とつくる注文住宅ブランド <strong>design casa</strong>。その加盟工務店として、株式会社エスホームが宇都宮市・栃木県で design casa の家をお届けします。耐震等級3・断熱等級6を標準仕様に、エアコン1台で家じゅうの温度がそろう高気密高断熱の住まいを。',
				'hero__lead'
			),
			dcs_b_button( '資料請求（無料）', home_url( '/contact/' ) ),
		)
	);
	$b[] = "<!-- wp:cover {$cover_attr} -->\n"
		. '<div class="wp-block-cover hero-block" style="min-height:88vh">'
		. '<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim"></span>'
		. ( $url ? '<img class="wp-block-cover__image-background wp-image-' . (int) $hero . '" alt="" src="' . esc_url( $url ) . '" data-object-fit="cover"/>' : '' )
		. '<div class="wp-block-cover__inner-container">' . "\n" . $cover_inner . "\n" . '</div></div>'
		. "\n<!-- /wp:cover -->";

	/* ---- お知らせ帯 ---- */
	$b[] = dcs_b_group(
		dcs_b_paragraph(
			'<strong>宇都宮市・栃木県で注文住宅をご検討の方へ。</strong>土地探しからのご相談、資金計画のご相談も無料で承っています。打ち合わせ回数・間取り変更に制限は設けていません。お電話は <a href="' . esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ) . '">' . esc_html( dcs_company( 'freedial' ) ) . '</a>（' . esc_html( dcs_company( 'hours' ) ) . '／火・水定休）へ。',
			'band__text'
		),
		'band-block'
	);

	/* ---- design casa とは ---- */
	$concept = dcs_import_common( 'concept.jpg', '建築家が設計した中庭のある家。夜の照明が中庭を照らす' );
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead(
					'ABOUT design casa',
					'建築家・工務店・あなた。3者でつくる、宇都宮の注文住宅。',
					'design casa は「建築家と建てる注文住宅」を、もっと多くの人に届けるための仕組みです。全国のネットワークで建材を共同購入し、設計から施工までの流れを標準化することで、建築家の設計料を<strong>建築費の約4%</strong>に抑えています。'
				),
				dcs_b_paragraph( '「デザイン性の高い注文住宅は高い」「建築家は敷居が高い」。宇都宮市でも、そう思って諦めてしまう方が少なくありません。' ),
				dcs_b_paragraph( 'けれど、規格プランから選ぶ家と、建築家が敷地を見て設計する家とでは、10年後20年後の暮らしやすさが変わってきます。土地の形、日の入り方、隣家の窓の位置。それらを読んだうえで窓の高さひとつを決められるかどうかが、住み心地を分けます。' ),
				dcs_b_image( $concept, 'design casa 施工実例／中庭のある平屋', 'large' ),
				dcs_b_button( 'デザインカーサとは', home_url( '/concept/' ) ),
			)
		),
		'sec sec--about'
	);

	/* ---- 標準仕様の数値 ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'STANDARD', '注文住宅の性能は、削らない。', '宇都宮市の冬の冷え込みと、夏の蒸し暑さ。栃木県の気候を前提に決めた標準仕様です。オプションではありません。' ),
				dcs_b_shortcode( '[dcs_figures]' ),
				dcs_b_button( '家の仕様をすべて見る', home_url( '/spec/' ) ),
			)
		),
		'sec sec--figures'
	);

	/* ---- 選ばれる理由 ---- */
	$reasons = array( dcs_b_sechead( 'WHY S HOME', '宇都宮で注文住宅の工務店を選ぶとき、見ていただきたい4つのこと。' ) );
	foreach ( dcs_reasons() as $i => $r ) {
		$img = dcs_import_common( $r['img'], $r['title'] );
		$reasons[] = dcs_b_join(
			array(
				dcs_b_paragraph( sprintf( '%02d', $i + 1 ), 'reason__num' ),
				dcs_b_heading( 3, $r['title'], 'reason__title' ),
				dcs_b_image( $img, '', 'large' ),
				dcs_b_paragraph( esc_html( $r['body'] ), 'reason__desc' ),
			)
		);
	}
	$b[] = dcs_b_group( dcs_b_join( $reasons ), 'sec sec--reason' );

	/* ---- 施工例 ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'CASE STUDY', '注文住宅の施工例', 'design casa の建築家が設計した注文住宅の実例です。平屋、中庭のある家、ガレージハウス、2階リビング。宇都宮市で建てる家のイメージづくりにお役立てください。' ),
				dcs_b_shortcode( '[dcs_works count="6"]' ),
				dcs_b_button( '施工例をすべて見る', home_url( '/works/' ) ),
			)
		),
		'sec sec--works'
	);

	/* ---- 建築家 ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'ARCHITECT', '注文住宅を設計するのは、実績のある建築家です。', 'design casa に登録し、宇都宮の注文住宅を手がける建築家をご紹介します。作品集をご覧いただきながら、あなたの感覚に近い建築家を一緒に選んでいきましょう。' ),
				dcs_b_shortcode( '[dcs_architects]' ),
				dcs_b_button( '建築家の紹介を見る', home_url( '/architect/' ) ),
			)
		),
		'sec sec--architect'
	);

	/* ---- 家づくりの流れ ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'FLOW', '注文住宅ができるまでの流れ', '宇都宮での注文住宅は、ご相談からお引き渡しまで14ステップ。ここでは最初の4つをご紹介します。' ),
				dcs_b_shortcode( '[dcs_flow from="1" to="4"]' ),
				dcs_b_button( '14ステップすべてを見る', home_url( '/flow/' ) ),
			)
		),
		'sec sec--flow'
	);

	/* ---- 対応エリア ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'AREA', '注文住宅の対応エリア', '宇都宮市を中心に、栃木県内で注文住宅の設計・施工を承っています。' ),
				dcs_b_shortcode( '[dcs_area map="1"]' ),
			)
		),
		'sec sec--area'
	);

	/* ---- FAQ ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'FAQ', '注文住宅のよくあるご質問' ),
				dcs_b_shortcode( '[dcs_faq]' ),
			)
		),
		'sec sec--faq'
	);

	/* ---- 会社 ---- */
	$b[] = dcs_b_group(
		dcs_b_join(
			array(
				dcs_b_sechead( 'COMPANY', '注文住宅を建てる施工会社について' ),
				dcs_b_paragraph( '宇都宮市平出町の工務店です。「大手ほど規格に縛られず、ローコスト系ほど性能を削らない」。宇都宮の注文住宅で、その中間にある選択肢でありたいと考えています。設計から施工、引き渡し後の点検まで、地元の会社が一貫して責任を持ちます。' ),
				dcs_b_shortcode( '[dcs_company_table]' ),
				dcs_b_button( '施工会社を紹介する', home_url( '/company/' ) ),
			)
		),
		'sec sec--company'
	);

	return dcs_b_join( $b );
}

/* =========================================================
   デザインカーサとは
   ========================================================= */

/**
 * コンセプトページの本文。
 *
 * @return string
 */
function dcs_page_concept_blocks() {
	$img = dcs_import_common( 'concept.jpg', '建築家が設計した中庭のある平屋' );

	$feat = array(
		array( '01', '建築家によるデザイン', '実力のある建築家が、これまでの経験を生かして美しさと機能性を兼ね備えた設計を行います。規格プランの当てはめではありません。' ),
		array( '02', '建築家と直接、3回の打ち合わせ', '顔を合わせてデザインを話し合えます。図面の裏にある考え方まで、その場で聞くことができます。' ),
		array( '03', '敷地・予算まで含めた総合設計', 'ご要望・ライフスタイル・土地の特性・ご予算のすべてを踏まえて設計します。「良い案だが予算が合わない」を避けるため、見積もりを並走させます。' ),
		array( '04', '徹底したヒアリング', '建築家が詳細に情報を集め、暮らしの中の不満をひとつずつ潰していきます。「住んでみたら不便だった」を減らすための時間です。' ),
	);
	$feat_blocks = array( dcs_b_sechead( '4 FEATURES', 'design casa の4つの特徴' ) );
	foreach ( $feat as $f ) {
		$feat_blocks[] = dcs_b_join(
			array(
				dcs_b_paragraph( $f[0], 'featgrid__num' ),
				dcs_b_heading( 3, $f[1], 'featgrid__title' ),
				dcs_b_paragraph( esc_html( $f[2] ) ),
			)
		);
	}

	return dcs_b_join(
		array(
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_heading( 2, 'デザイン住宅は、本当に高いのか。', 'intro__title' ),
						dcs_b_paragraph( '宇都宮市でデザイン住宅を考えはじめると、多くの方が同じところで止まります。「建築家に頼むと高い」。' ),
						dcs_b_paragraph( 'たしかに、建築家へ直接依頼した場合の設計料は、一般に建築費の10〜15%です。3,000万円の家なら300〜450万円。この金額を聞いて諦めてしまうのは、無理のないことだと思います。' ),
						dcs_b_paragraph( 'design casa は、この構造そのものを組み替えました。全国のネットワークで建材を共同購入し、設計から施工までの進め方を標準化する。そうすることで、<strong>建築家の設計料を建築費の約4%</strong>に抑えています。' ),
						dcs_b_image( $img, 'design casa 施工実例／中庭のある平屋', 'large' ),
					)
				),
				'sec sec--intro'
			),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( '3 PLAYERS', '建築家・工務店・あなた。チームのような距離感で。', 'まず加盟工務店であるエスホームが、あなたの理想の暮らしをヒアリングします。そのうえで建築家・工務店・あなたの3者がミーティングを重ね、設計プランを導き出していきます。' ),
						dcs_b_heading( 3, '建築家' ),
						dcs_b_paragraph( '建築の国家資格を持ち、数多くの住宅を手がけてきた建築家が、ご要望・ライフスタイル・敷地特性・ご予算を総合的に判断して設計します。敷地に足を運び、周辺環境まで見たうえでプランを起こします。' ),
						dcs_b_heading( 3, 'エスホーム' ),
						dcs_b_paragraph( '宇都宮市の工務店として、資金計画・土地探しから施工、引き渡し後の点検まで一貫して担当します。建築家とあなたのあいだに立ち、専門用語を日常の言葉に置き換えるのも役割のひとつです。' ),
						dcs_b_heading( 3, 'あなた' ),
						dcs_b_paragraph( '専門知識は必要ありません。「休日をどう過ごしたいか」「何にいちばん時間をかけたいか」。暮らしの話をしてください。そこから間取りが立ち上がっていきます。' ),
					)
				),
				'sec sec--about'
			),
			dcs_b_group( dcs_b_join( $feat_blocks ), 'sec sec--reason' ),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'COST', 'なぜ、この価格でできるのか' ),
						dcs_b_heading( 3, '設計料は建築費の約4%' ),
						dcs_b_paragraph( '建築家に直接依頼した場合の一般的な設計料は建築費の10〜15%です。design casa では、設計から施工までのプロセスを標準化することで、これを約4%に抑えています。' ),
						dcs_b_heading( 3, '建材を全国でまとめて仕入れる' ),
						dcs_b_paragraph( '加盟工務店のネットワークで建材・設備を共同購入しています。1社では実現できない条件で仕入れられるため、同じ品質でもコストを抑えられます。' ),
						dcs_b_heading( 3, '設計にルールを設ける' ),
						dcs_b_paragraph( '設計と現場の進め方に一定のルールを設けることで、工期を短縮し現場を効率化しています。無理と無駄を省いた分が、デザインと性能に回ります。' ),
						dcs_b_heading( 3, 'それでも、性能は削らない' ),
						dcs_b_paragraph( '宇都宮市・栃木県では、株式会社エスホームが<strong>耐震等級3・断熱等級6</strong>を標準仕様として上乗せしています。デザインのために住み心地を犠牲にすることはありません。' ),
						dcs_b_button( '家の仕様をくわしく見る', home_url( '/spec/' ) ),
					)
				),
				'sec sec--cost'
			),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'CASE STUDY', 'design casa の家を見る' ),
						dcs_b_shortcode( '[dcs_works count="3"]' ),
						dcs_b_button( '施工例をすべて見る', home_url( '/works/' ) ),
					)
				),
				'sec sec--works'
			),
		)
	);
}

/* =========================================================
   家づくりの流れ
   ========================================================= */

/**
 * 流れページの本文。
 *
 * @return string
 */
function dcs_page_flow_blocks() {
	return dcs_b_join(
		array(
			dcs_b_group( dcs_b_shortcode( '[dcs_flow from="1" to="14"]' ), 'sec sec--flow' ),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'SCHEDULE', 'どれくらいの期間がかかるか' ),
						dcs_b_paragraph( '目安はつぎのとおりです。土地探しから始める場合は、ここに土地決定までの期間が加わります。' ),
						dcs_b_list(
							array(
								'<strong>ご相談〜仮契約</strong>：1〜2ヶ月（敷地調査・資金計画）',
								'<strong>仮契約〜プラン完成</strong>：約1ヶ月（建築家との打ち合わせ3回）',
								'<strong>プラン完成〜ご契約</strong>：2週間〜1ヶ月（最終見積り確認）',
								'<strong>ご契約〜着工</strong>：1〜2ヶ月（確認申請・地鎮祭）',
								'<strong>着工〜お引き渡し</strong>：4〜6ヶ月（規模による）',
							)
						),
						dcs_b_paragraph( '全体では<strong>10ヶ月〜1年ほど</strong>を見込んでください。「来年の春に入居したい」といったご希望がある場合は、逆算してスケジュールを組みます。' ),
						dcs_b_heading( 3, '打ち合わせの回数に制限はありません' ),
						dcs_b_paragraph( '当社は打ち合わせ期間や間取り変更の回数に上限を設けていません。納得いくまでご検討ください。ただし、着工後の仕様変更は原則としてお受けできませんので、着工前にしっかり詰めていきます。' ),
						dcs_b_heading( 3, '引き渡してからが、本当のお付き合い' ),
						dcs_b_paragraph( 'お引き渡し後も定期点検でお伺いします。宇都宮市の地元工務店ですから、何かあればすぐに駆けつけられます。' ),
					)
				),
				'sec sec--period'
			),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'FAQ', 'よくあるご質問' ),
						dcs_b_shortcode( '[dcs_faq]' ),
					)
				),
				'sec sec--faq'
			),
		)
	);
}

/* =========================================================
   施工会社紹介
   ========================================================= */

/**
 * 会社紹介ページの本文。
 *
 * @return string
 */
function dcs_page_company_blocks() {
	$img = dcs_import_common( 'hiraya.jpg', '宇都宮市の工務店エスホームが手がける、大屋根の平屋' );

	$stance = array(
		array( '01', '見積書の内訳を、すべて開示します', '「一式」でまとめた見積書はお出ししません。どこにいくらかかっているのかが分かる形でご提示します。比較していただいて構いません。' ),
		array( '02', '性能を、数値でお伝えします', '耐震等級は構造計算にもとづく等級3。気密は全棟で測定し、結果をお渡しします。「相当」という言葉は使いません。' ),
		array( '03', '急かしません', '打ち合わせ回数にも間取り変更にも制限を設けていません。しつこい営業もいたしません。ご検討の時間はお客さまのものです。' ),
		array( '04', 'できないことは、できないと言います', 'ご予算内で実現できない場合や、その間取りではエアコン1台での全館空調が成立しない場合は、正直にお伝えします。' ),
	);
	$stance_blocks = array( dcs_b_sechead( 'OUR STANCE', '私たちが約束すること' ) );
	foreach ( $stance as $s ) {
		$stance_blocks[] = dcs_b_join(
			array(
				dcs_b_paragraph( $s[0], 'featgrid__num' ),
				dcs_b_heading( 3, $s[1], 'featgrid__title' ),
				dcs_b_paragraph( esc_html( $s[2] ) ),
			)
		);
	}

	return dcs_b_join(
		array(
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_heading( 2, 'ハワイのように明るく心地よい暮らしを、安心できる価格で。', 'intro__title' ),
						dcs_b_paragraph( '大手ハウスメーカーほど規格に縛られず、ローコスト系ほど性能を削らない。私たちが立っているのは、その中間です。' ),
						dcs_b_paragraph( '打ち合わせ期間にも、間取り変更の回数にも制限を設けていません。家は一度建てたら数十年つき合うものですから、納得いくまで考える時間があっていいはずだと思っています。' ),
						dcs_b_paragraph( '採用しているのは<strong>松尾式のエコハウス</strong>の考え方です。高断熱・高気密、日射をコントロールするパッシブ設計、そして計画的な空気の流れ。この組み合わせによって、<strong>エアコン1台で家じゅうの温度がそろう家</strong>を実現します。' ),
						dcs_b_paragraph( '宇都宮市の冬は冷え込み、夏は蒸し暑い。この土地で暮らすからこそ、性能にこだわる意味があります。' ),
						dcs_b_image( $img, '', 'large' ),
					)
				),
				'sec sec--intro'
			),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'OUTLINE', '会社概要' ),
						dcs_b_shortcode( '[dcs_company_table]' ),
					)
				),
				'sec sec--outline'
			),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'AREA &amp; ACCESS', '対応エリアとアクセス' ),
						dcs_b_shortcode( '[dcs_area map="1"]' ),
						dcs_b_paragraph( 'ご来社の際は、事前にお電話いただけると確実です（火曜・水曜が定休日ですが、事前にご連絡いただければ対応できます）。' ),
					)
				),
				'sec sec--area'
			),
			dcs_b_group( dcs_b_join( $stance_blocks ), 'sec sec--reason' ),
		)
	);
}

/* =========================================================
   資料請求・お問い合わせ
   ========================================================= */

/**
 * 問い合わせページの本文。
 *
 * @return string
 */
function dcs_page_contact_blocks() {
	return dcs_b_join(
		array(
			dcs_b_group( dcs_b_shortcode( '[dcs_contact_ways]' ), 'sec sec--ways' ),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead(
							'FORM',
							'資料請求・ご相談フォーム',
							'<strong>無料でお送りするもの</strong>：design casa の施工例集／エスホームの標準仕様書／家づくりの資金計画ガイド'
						),
						dcs_b_shortcode( '[dcs_contact_form]' ),
					)
				),
				'sec sec--form'
			),
			dcs_b_group(
				dcs_b_join(
					array(
						dcs_b_sechead( 'FAQ', 'お問い合わせの前に' ),
						dcs_b_shortcode( '[dcs_faq count="6"]' ),
					)
				),
				'sec sec--faq'
			),
		)
	);
}

/* =========================================================
   プライバシーポリシー
   ========================================================= */

/**
 * プライバシーポリシーの本文。
 *
 * @return string
 */
function dcs_page_privacy_blocks() {
	$b = array();

	$b[] = dcs_b_paragraph( esc_html( dcs_company( 'name' ) ) . '（以下「当社」）は、お客さまの個人情報の保護を重要な責務と考え、以下の方針にもとづき適切に取り扱います。' );

	$b[] = dcs_b_heading( 2, '1. 個人情報の定義' );
	$b[] = dcs_b_paragraph( '本ポリシーにおける「個人情報」とは、個人情報の保護に関する法律に定める個人情報、すなわち生存する個人に関する情報であって、氏名、住所、電話番号、メールアドレス等により特定の個人を識別できるものをいいます。' );

	$b[] = dcs_b_heading( 2, '2. 取得する情報' );
	$b[] = dcs_b_paragraph( '当社は、資料請求・お問い合わせ・見学会のお申し込み・ご契約手続きなどにおいて、以下の情報を取得することがあります。' );
	$b[] = dcs_b_list( array( '氏名、ふりがな', '住所、郵便番号', '電話番号、メールアドレス', 'ご家族の構成、ご検討時期、土地の状況、ご予算などの家づくりに関する情報', 'ご要望・ご質問の内容' ) );
	$b[] = dcs_b_paragraph( 'また、ウェブサイトの利用状況を把握するため、Cookie等を用いてアクセス情報を取得する場合があります。これらの情報から個人を特定することはありません。' );

	$b[] = dcs_b_heading( 2, '3. 利用目的' );
	$b[] = dcs_b_paragraph( '取得した個人情報は、以下の目的の範囲内で利用します。' );
	$b[] = dcs_b_list(
		array(
			'資料・カタログの送付、お問い合わせへの回答',
			'プラン提案、お見積り作成、打ち合わせなど、住宅の設計・施工に関する業務',
			'土地情報のご提案、資金計画のご相談への対応',
			'完成見学会・イベントのご案内',
			'お引き渡し後のアフターサービス、定期点検のご連絡',
			'サービス向上のための統計的な分析（個人を特定しない形で行います）',
		)
	);
	$b[] = dcs_b_paragraph( '上記以外の目的で利用する必要が生じた場合は、あらかじめご本人の同意をいただきます。' );

	$b[] = dcs_b_heading( 2, '4. 第三者への提供' );
	$b[] = dcs_b_paragraph( '当社は、以下の場合を除き、ご本人の同意なく個人情報を第三者へ提供することはありません。' );
	$b[] = dcs_b_list(
		array(
			'法令にもとづく場合',
			'人の生命、身体または財産の保護のために必要があり、本人の同意を得ることが困難な場合',
			'設計・施工・登記・金融機関への手続きなど、業務の遂行に必要な範囲で、建築家・協力業者・金融機関等へ提供する場合（この場合、提供先に対しても適切な管理を求めます）',
		)
	);

	$b[] = dcs_b_heading( 2, '5. 委託先の管理' );
	$b[] = dcs_b_paragraph( '業務の一部を外部に委託する場合、委託先に対して個人情報の適切な取り扱いを契約等により義務づけ、必要かつ適切な監督を行います。' );

	$b[] = dcs_b_heading( 2, '6. 安全管理措置' );
	$b[] = dcs_b_paragraph( '当社は、個人情報の漏えい、滅失またはき損の防止その他の安全管理のため、組織的・人的・物理的・技術的な安全管理措置を講じます。個人情報を取り扱う従業者に対しては、必要かつ適切な監督を行います。' );

	$b[] = dcs_b_heading( 2, '7. 開示・訂正・削除等のご請求' );
	$b[] = dcs_b_paragraph( 'ご本人からご自身の個人情報の開示、訂正、追加、削除、利用停止のお申し出があった場合は、ご本人であることを確認したうえで、法令にもとづき速やかに対応いたします。下記の窓口までご連絡ください。' );

	$b[] = dcs_b_heading( 2, '8. Cookie（クッキー）およびアクセス解析' );
	$b[] = dcs_b_paragraph( '当社ウェブサイトでは、サイトの利用状況を把握し改善するために、Cookie を利用したアクセス解析ツールを使用する場合があります。取得される情報は匿名で収集されており、個人を特定するものではありません。ブラウザの設定により Cookie を無効にすることも可能です。' );

	$b[] = dcs_b_heading( 2, '9. 本ポリシーの変更' );
	$b[] = dcs_b_paragraph( '法令の改正や事業内容の変更等に応じて、本ポリシーを変更することがあります。変更後の内容は、当ウェブサイトに掲載した時点から効力を生じるものとします。' );

	$b[] = dcs_b_heading( 2, '10. お問い合わせ窓口' );
	$b[] = dcs_b_paragraph(
		esc_html( dcs_company( 'name' ) ) . '<br>〒' . esc_html( dcs_company( 'zip' ) ) . ' ' . esc_html( dcs_company( 'address' ) ) . '<br>'
		. 'TEL <a href="' . esc_attr( dcs_tel_href( dcs_company( 'tel' ) ) ) . '">' . esc_html( dcs_company( 'tel' ) ) . '</a>'
		. ' ／ フリーダイヤル <a href="' . esc_attr( dcs_tel_href( dcs_company( 'freedial' ) ) ) . '">' . esc_html( dcs_company( 'freedial' ) ) . '</a><br>'
		. '受付時間 ' . esc_html( dcs_company( 'hours' ) ) . '（定休日：火曜・水曜）'
	);

	$b[] = dcs_b_heading( 2, '11. 掲載写真について' );
	$b[] = dcs_b_paragraph( '当ウェブサイトに掲載している施工例の写真は、design casa（カーサプロジェクト株式会社）およびその加盟工務店による施工実例です。写真の著作権は各権利者に帰属します。無断転載を禁じます。' );

	return dcs_b_group( dcs_b_join( $b ), 'sec sec--legal' );
}
