<?php
/**
 * 初期データのセットアップ
 *
 * テーマを有効化すると固定ページ・メニュー・パーマリンクが自動で整い、
 * 管理画面の「design casa 初期データ」から施工例・建築家・仕様を一括取り込みできます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * テーマ有効化時の初期設定。
 */
function dcs_after_switch_theme() {
	dcs_setup_permalinks();
	dcs_setup_pages();
	dcs_setup_menus();
	dcs_sync_term_slugs();

	if ( ! get_option( 'dcs_imported' ) ) {
		update_option( 'dcs_needs_import', 1 );
	}

	update_option( 'dcs_migrated_version', DCS_VERSION );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dcs_after_switch_theme' );

/**
 * ターム名の配列を、ローマ字スラッグ付きのターム群にして term_id の配列で返す。
 *
 * ターム名だけを wp_set_object_terms() に渡すと、スラッグが日本語の
 * URLエンコードになり、環境によって施工例の絞り込みタブが404になります。
 *
 * @param array  $names    ターム名の配列。
 * @param string $taxonomy タクソノミー名。
 * @return array<int,int> term_id の配列。
 */
function dcs_ensure_terms( $names, $taxonomy ) {
	$map = dcs_term_slugs( $taxonomy );
	$ids = array();

	foreach ( (array) $names as $name ) {
		$name = trim( (string) $name );
		if ( '' === $name ) {
			continue;
		}

		$term = get_term_by( 'name', $name, $taxonomy );

		if ( ! $term ) {
			$args = isset( $map[ $name ] ) ? array( 'slug' => $map[ $name ] ) : array();
			$new  = wp_insert_term( $name, $taxonomy, $args );
			if ( is_wp_error( $new ) ) {
				continue;
			}
			$ids[] = (int) $new['term_id'];
			continue;
		}

		/* 既にあるタームのスラッグが日本語のままなら、ローマ字に直す */
		if ( isset( $map[ $name ] ) && $term->slug !== $map[ $name ] ) {
			wp_update_term( $term->term_id, $taxonomy, array( 'slug' => $map[ $name ] ) );
		}
		$ids[] = (int) $term->term_id;
	}

	return $ids;
}

/**
 * 既存タームのスラッグをローマ字にそろえる。
 *
 * すでに公開済みのサイトでは、タームが日本語スラッグで作られています。
 * 施工例を取り込み直さなくても直せるよう、単独で実行できるようにしています。
 *
 * @return int 直した件数。
 */
function dcs_sync_term_slugs() {
	$fixed = 0;

	foreach ( dcs_term_slugs() as $taxonomy => $map ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		foreach ( $map as $name => $slug ) {
			$term = get_term_by( 'name', $name, $taxonomy );
			if ( ! $term || $term->slug === $slug ) {
				continue;
			}
			/* 同じスラッグの別タームがある場合は触らない（重複を作らない） */
			if ( get_term_by( 'slug', $slug, $taxonomy ) ) {
				continue;
			}
			$res = wp_update_term( $term->term_id, $taxonomy, array( 'slug' => $slug ) );
			if ( ! is_wp_error( $res ) ) {
				$fixed++;
			}
		}
	}

	return $fixed;
}

/**
 * テーマ更新時の移行処理。
 *
 * 保存してあるバージョンが今のテーマより古ければ一度だけ実行します。
 * ZIPを入れ直すだけで直るようにするための仕組みです。
 */
function dcs_maybe_migrate() {
	if ( get_option( 'dcs_migrated_version' ) === DCS_VERSION ) {
		return;
	}

	dcs_sync_term_slugs();
	flush_rewrite_rules();

	update_option( 'dcs_migrated_version', DCS_VERSION );
}
add_action( 'admin_init', 'dcs_maybe_migrate' );

/**
 * パーマリンクを「投稿名」にする（SEOの基本）。
 */
function dcs_setup_permalinks() {
	if ( '' === get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
}

/**
 * 作成する固定ページの定義。
 *
 * @return array<string,array{title:string,template:string,content:string}>
 */
function dcs_page_defs() {
	return array(
		'home'    => array(
			'title'    => 'ホーム',
			'template' => '',
			'blocks'   => 'dcs_page_home_blocks',
			'front'    => true,
		),
		'concept' => array(
			'title'    => 'デザインカーサとは',
			'template' => '',
			'blocks'   => 'dcs_page_concept_blocks',
		),
		'flow'    => array(
			'title'    => '家づくりの流れ',
			'template' => '',
			'blocks'   => 'dcs_page_flow_blocks',
		),
		'company' => array(
			'title'    => '施工会社紹介',
			'template' => '',
			'blocks'   => 'dcs_page_company_blocks',
		),
		'contact' => array(
			'title'    => '資料請求・お問い合わせ',
			'template' => '',
			'blocks'   => 'dcs_page_contact_blocks',
		),
		'privacy' => array(
			'title'    => 'プライバシーポリシー',
			'template' => '',
			'blocks'   => 'dcs_page_privacy_blocks',
		),
	);
}

/**
 * 各ページのリード文（見出し下の説明）。
 *
 * @return array
 */
function dcs_page_leads() {
	return array(
		'concept' => '一生暮らしたいデザインの家を、建築家・工務店・あなたの3者でつくる。まったく新しい「建築家とつくる家」のかたちです。宇都宮市・栃木県では、株式会社エスホームが design casa 加盟工務店としてお手伝いします。',
		'flow'    => '最初のご相談から、お引き渡しまで全14ステップ。宇都宮市で注文住宅を建てるとき、どの順番で何が決まっていくのかを、あらかじめ知っておいてください。',
		'company' => 'design casa 宇都宮の家をつくるのは、宇都宮市平出町の工務店・株式会社エスホームです。設計から施工、引き渡し後の点検まで、地元の会社が一貫して責任を持ちます。',
		'contact' => 'まだ具体的でなくてかまいません。「宇都宮市で工務店を探しはじめたところ」「予算がいくら必要なのか知りたい」。その段階のご相談がいちばん多く、いちばんお役に立てる段階です。しつこい営業は一切いたしません。',
		'privacy' => '株式会社エスホームは、お客さまの個人情報の保護を重要な責務と考え、以下の方針にもとづき適切に取り扱います。',
	);
}

/**
 * 固定ページを作成する。
 */
function dcs_setup_pages() {
	$leads = dcs_page_leads();

	foreach ( dcs_page_defs() as $slug => $def ) {
		$page = get_page_by_path( $slug );

		if ( ! $page ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $def['title'],
					'post_name'    => $slug,
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_content' => '',
					'post_excerpt' => isset( $leads[ $slug ] ) ? $leads[ $slug ] : '',
				)
			);
			if ( ! $id || is_wp_error( $id ) ) {
				continue;
			}
		} else {
			$id = $page->ID;
		}

		if ( ! empty( $def['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', $def['template'] );
		}
		if ( 'privacy' === $slug ) {
			update_option( 'wp_page_for_privacy_policy', $id );
		}
		if ( ! empty( $def['front'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $id );
		}
	}

	/* 初期状態の「サンプルページ」「Hello world!」はゴミ箱へ */
	foreach ( array( 'sample-page', 'hello-world' ) as $s ) {
		$p = get_page_by_path( $s, OBJECT, array( 'page', 'post' ) );
		if ( $p && 'trash' !== $p->post_status ) {
			wp_trash_post( $p->ID );
		}
	}
}

/**
 * ナビゲーションメニューを作成して割り当てる。
 */
function dcs_setup_menus() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	/* ---------- ヘッダー ---------- */
	if ( ! is_nav_menu( 'ヘッダーメニュー' ) ) {
		$menu_id = wp_create_nav_menu( 'ヘッダーメニュー' );
		if ( ! is_wp_error( $menu_id ) ) {
			$items = array(
				array( 'page', 'concept', 'デザインカーサとは' ),
				array( 'archive', 'dc_work', '施工例' ),
				array( 'archive', 'dc_architect', '建築家紹介' ),
				array( 'page', 'flow', '家づくりの流れ' ),
				array( 'archive', 'dc_spec', '家の仕様' ),
				array( 'page', 'company', '施工会社紹介' ),
			);
			dcs_add_menu_items( $menu_id, $items );
			$locations['primary'] = $menu_id;
		}
	}

	/* ---------- フッター ---------- */
	if ( ! is_nav_menu( 'フッターメニュー' ) ) {
		$menu_id = wp_create_nav_menu( 'フッターメニュー' );
		if ( ! is_wp_error( $menu_id ) ) {
			$items = array(
				array( 'page', 'concept', 'デザインカーサとは' ),
				array( 'archive', 'dc_work', '施工例' ),
				array( 'archive', 'dc_architect', '建築家紹介' ),
				array( 'page', 'flow', '家づくりの流れ' ),
				array( 'archive', 'dc_spec', '家の仕様' ),
				array( 'page', 'company', '施工会社紹介' ),
				array( 'page', 'contact', '資料請求・お問い合わせ' ),
				array( 'page', 'privacy', 'プライバシーポリシー' ),
			);
			dcs_add_menu_items( $menu_id, $items );
			$locations['footer'] = $menu_id;
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * メニュー項目をまとめて追加する。
 *
 * @param int   $menu_id メニューID。
 * @param array $items   項目定義。
 */
function dcs_add_menu_items( $menu_id, $items ) {
	foreach ( $items as $item ) {
		list( $kind, $key, $label ) = $item;

		if ( 'page' === $kind ) {
			$page = get_page_by_path( $key );
			if ( ! $page ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $label,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		} else {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $label,
					'menu-item-object' => $key,
					'menu-item-type'   => 'post_type_archive',
					'menu-item-status' => 'publish',
				)
			);
		}
	}
}

/* =========================================================
   初期データの取り込み（管理画面 + Ajax）
   ========================================================= */

/**
 * 取り込み管理ページを追加する。
 */
function dcs_import_menu() {
	add_management_page(
		'design casa 初期データ',
		'design casa 初期データ',
		'manage_options',
		'dcs-import',
		'dcs_import_page'
	);
}
add_action( 'admin_menu', 'dcs_import_menu' );

/**
 * 未取り込みのときに管理画面へお知らせを出す。
 */
function dcs_import_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'dcs_needs_import' ) ) {
		return;
	}
	$url = admin_url( 'tools.php?page=dcs-import' );
	printf(
		'<div class="notice notice-info"><p><strong>design casa テーマの初期データがまだ取り込まれていません。</strong><br>施工例67件・建築家13名・仕様8項目と写真を取り込みます。<a class="button button-primary" style="margin-left:8px" href="%s">取り込み画面をひらく</a></p></div>',
		esc_url( $url )
	);
}
add_action( 'admin_notices', 'dcs_import_notice' );

/**
 * 取り込み対象のキュー（作業単位の一覧）を組み立てる。
 *
 * @return array<int,array{type:string,index:int,label:string}>
 */
function dcs_import_queue() {
	$queue = array();

	foreach ( dcs_load_data( 'specs' ) as $i => $item ) {
		$queue[] = array(
			'type'  => 'spec',
			'index' => $i,
			'label' => '仕様：' . $item['title'],
		);
	}
	foreach ( dcs_load_data( 'architects' ) as $i => $item ) {
		$queue[] = array(
			'type'  => 'architect',
			'index' => $i,
			'label' => '建築家：' . $item['name'],
		);
	}
	foreach ( dcs_load_data( 'works' ) as $i => $item ) {
		$queue[] = array(
			'type'  => 'work',
			'index' => $i,
			'label' => '施工例：' . $item['title'],
		);
	}

	/* 固定ページの本文（ブロック）は、写真の取り込み後に入れる */
	foreach ( dcs_page_defs() as $slug => $def ) {
		if ( ! empty( $def['blocks'] ) ) {
			$queue[] = array(
				'type'  => 'page',
				'index' => $slug,
				'label' => 'ページ：' . $def['title'],
			);
		}
	}

	return $queue;
}

/**
 * テーマ共通画像（assets/img/common/）を取り込む。
 *
 * @param string $file ファイル名。
 * @param string $alt  代替テキスト。
 * @return int 添付ID。
 */
function dcs_import_common( $file, $alt = '' ) {
	if ( function_exists( 'dcs_pv_att' ) ) {
		/* 確認用プレビューでは、その場で疑似的な添付をつくる */
		return dcs_pv_att( 'common/' . $file, '', $alt );
	}

	return dcs_import_image( 'assets/img/common/' . $file, '', $alt, 0, 'common-' . $file );
}

/**
 * 固定ページの本文（ブロック）を入れる。
 *
 * @param string $slug スラッグ。
 */
function dcs_import_page_content( $slug ) {
	$defs = dcs_page_defs();
	if ( empty( $defs[ $slug ]['blocks'] ) ) {
		return;
	}
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		return;
	}

	$fn = $defs[ $slug ]['blocks'];
	if ( ! function_exists( $fn ) ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => call_user_func( $fn ),
		)
	);
}

/**
 * data/*.json を読み込む。
 *
 * @param string $name ファイル名（拡張子なし）。
 * @return array
 */
function dcs_load_data( $name ) {
	static $cache = array();
	if ( isset( $cache[ $name ] ) ) {
		return $cache[ $name ];
	}
	$path = get_theme_file_path( 'data/' . $name . '.json' );
	if ( ! file_exists( $path ) ) {
		$cache[ $name ] = array();

		return array();
	}
	$json           = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$cache[ $name ] = json_decode( $json, true );

	return is_array( $cache[ $name ] ) ? $cache[ $name ] : array();
}

/**
 * 取り込み管理ページを描画する。
 */
function dcs_import_page() {
	$queue = dcs_import_queue();
	$done  = (int) get_option( 'dcs_import_pos', 0 );
	?>
	<div class="wrap">
		<h1>design casa 初期データの取り込み</h1>
		<p>施工例 <strong><?php echo count( dcs_load_data( 'works' ) ); ?>件</strong>（写真 約660点）、建築家 <strong><?php echo count( dcs_load_data( 'architects' ) ); ?>名</strong>、家の仕様 <strong><?php echo count( dcs_load_data( 'specs' ) ); ?>項目</strong>をデータベースに登録し、写真をメディアライブラリに取り込みます。</p>
		<p>写真の枚数が多いため、数分かかります。<strong>この画面を開いたままお待ちください。</strong>途中で閉じても、次に開いたときに続きから再開できます。</p>

		<div id="dcs-import-box" style="max-width:760px;border:1px solid #c3c4c7;background:#fff;padding:20px;margin-top:16px">
			<div style="background:#f0f0f1;height:22px;border-radius:11px;overflow:hidden">
				<div id="dcs-bar" style="background:#2271b1;height:100%;width:<?php echo esc_attr( $queue ? round( $done / count( $queue ) * 100 ) : 0 ); ?>%;transition:width .3s"></div>
			</div>
			<p id="dcs-status" style="margin:12px 0 0"><?php echo esc_html( sprintf( '%d / %d 件', $done, count( $queue ) ) ); ?></p>
			<p id="dcs-log" style="margin:4px 0 16px;color:#646970;font-size:12px"></p>
			<p>
				<button type="button" class="button button-primary" id="dcs-start">取り込みを開始する</button>
				<button type="button" class="button" id="dcs-reset">最初からやり直す</button>
			</p>
		</div>

		<h2 style="margin-top:32px">テーマを新しいZIPに入れ替えたとき</h2>
		<div style="max-width:760px;border:1px solid #c3c4c7;background:#fff;padding:20px">
			<p style="margin-top:0">
				ページの文章はデータベースに保存されているため、テーマを入れ替えただけでは変わりません。
				<strong>文章の修正を反映させたいときだけ</strong>、下のボタンを押してください。写真の取り込みはやり直しません（数秒で終わります）。
			</p>
			<p style="color:#b32d2e">
				※ ブロックエディタでページ本文をご自分で編集された場合、その内容は上書きされます。
			</p>
			<p>
				<button type="button" class="button" id="dcs-rebuild">固定ページの文章を作り直す</button>
				<span id="dcs-rebuild-log" style="margin-left:10px;color:#646970"></span>
			</p>
		</div>

		<h2 style="margin-top:32px">取り込み後にご確認ください</h2>
		<ol>
			<li><a href="<?php echo esc_url( admin_url( 'options-permalink.php' ) ); ?>">設定 &gt; パーマリンク設定</a> を開いて「変更を保存」を1回押してください（URLの再構築）。</li>
			<li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">外観 &gt; カスタマイズ</a> で電話番号・住所・問い合わせ先メールをご確認ください。</li>
			<li><a href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">設定 &gt; 一般</a> でサイトのタイトルとキャッチフレーズをご設定ください。</li>
		</ol>
	</div>

	<script>
	( function () {
		const bar = document.getElementById( 'dcs-bar' );
		const status = document.getElementById( 'dcs-status' );
		const log = document.getElementById( 'dcs-log' );
		const start = document.getElementById( 'dcs-start' );
		const reset = document.getElementById( 'dcs-reset' );
		const nonce = '<?php echo esc_js( wp_create_nonce( 'dcs_import' ) ); ?>';
		let running = false;

		function step() {
			if ( ! running ) { return; }
			const body = new FormData();
			body.append( 'action', 'dcs_import_step' );
			body.append( 'nonce', nonce );
			fetch( ajaxurl, { method: 'POST', body: body, credentials: 'same-origin' } )
				.then( r => r.json() )
				.then( d => {
					if ( ! d.success ) { throw new Error( d.data || 'エラー' ); }
					bar.style.width = d.data.percent + '%';
					status.textContent = d.data.done + ' / ' + d.data.total + ' 件';
					log.textContent = d.data.label || '';
					if ( d.data.finished ) {
						running = false;
						start.disabled = false;
						start.textContent = '取り込みを開始する';
						status.textContent = '完了しました（' + d.data.total + ' 件）';
						log.textContent = 'パーマリンク設定を開いて「変更を保存」を押してください。';
					} else {
						step();
					}
				} )
				.catch( e => {
					running = false;
					start.disabled = false;
					start.textContent = '再開する';
					log.textContent = 'エラー: ' + e.message + '（もう一度「再開する」を押してください）';
				} );
		}

		start.addEventListener( 'click', function () {
			running = true;
			start.disabled = true;
			start.textContent = '取り込み中…';
			step();
		} );

		const rebuild = document.getElementById( 'dcs-rebuild' );
		const rebuildLog = document.getElementById( 'dcs-rebuild-log' );

		rebuild.addEventListener( 'click', function () {
			if ( ! confirm( '固定ページの本文をテーマの内容で作り直します。ブロックエディタでの編集内容は失われます。よろしいですか？' ) ) { return; }
			rebuild.disabled = true;
			rebuildLog.textContent = '作り直しています…';
			const body = new FormData();
			body.append( 'action', 'dcs_rebuild_pages' );
			body.append( 'nonce', nonce );
			fetch( ajaxurl, { method: 'POST', body: body, credentials: 'same-origin' } )
				.then( r => r.json() )
				.then( d => {
					rebuild.disabled = false;
					if ( ! d.success ) { throw new Error( d.data || 'エラー' ); }
					rebuildLog.textContent = '完了しました（' + d.data.pages.join( '／' ) + '）';
				} )
				.catch( e => {
					rebuild.disabled = false;
					rebuildLog.textContent = 'エラー: ' + e.message;
				} );
		} );

		reset.addEventListener( 'click', function () {
			if ( ! confirm( '取り込み位置を先頭に戻します。すでに登録済みの記事は上書きされません。よろしいですか？' ) ) { return; }
			const body = new FormData();
			body.append( 'action', 'dcs_import_reset' );
			body.append( 'nonce', nonce );
			fetch( ajaxurl, { method: 'POST', body: body, credentials: 'same-origin' } )
				.then( () => location.reload() );
		} );
	} )();
	</script>
	<?php
}

/**
 * Ajax：1件ずつ取り込む。
 */
function dcs_ajax_import_step() {
	check_ajax_referer( 'dcs_import', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( '権限がありません' );
	}

	@set_time_limit( 120 ); // phpcs:ignore

	$queue = dcs_import_queue();
	$total = count( $queue );
	$pos   = (int) get_option( 'dcs_import_pos', 0 );

	if ( $pos >= $total ) {
		update_option( 'dcs_imported', 1 );
		delete_option( 'dcs_needs_import' );
		wp_send_json_success(
			array(
				'done'     => $total,
				'total'    => $total,
				'percent'  => 100,
				'finished' => true,
				'label'    => '',
			)
		);
	}

	$job = $queue[ $pos ];

	switch ( $job['type'] ) {
		case 'spec':
			dcs_import_spec( dcs_load_data( 'specs' )[ $job['index'] ] );
			break;
		case 'architect':
			dcs_import_architect( dcs_load_data( 'architects' )[ $job['index'] ] );
			break;
		case 'work':
			dcs_import_work( dcs_load_data( 'works' )[ $job['index'] ] );
			break;
		case 'page':
			dcs_import_page_content( $job['index'] );
			break;
	}

	$pos++;
	update_option( 'dcs_import_pos', $pos );

	wp_send_json_success(
		array(
			'done'     => $pos,
			'total'    => $total,
			'percent'  => round( $pos / $total * 100 ),
			'finished' => $pos >= $total,
			'label'    => $job['label'],
		)
	);
}
add_action( 'wp_ajax_dcs_import_step', 'dcs_ajax_import_step' );

/**
 * Ajax：取り込み位置をリセットする。
 */
function dcs_ajax_import_reset() {
	check_ajax_referer( 'dcs_import', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( '権限がありません' );
	}
	update_option( 'dcs_import_pos', 0 );
	wp_send_json_success();
}
add_action( 'wp_ajax_dcs_import_reset', 'dcs_ajax_import_reset' );

/**
 * Ajax：固定ページの本文だけを作り直す。
 *
 * 文章を直したテーマに入れ替えても、すでに登録済みのページ本文は
 * データベースに保存されたままなので自動では変わりません。
 * 写真を取り込み直さずに文章だけ反映させるための処理です。
 */
function dcs_ajax_rebuild_pages() {
	check_ajax_referer( 'dcs_import', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( '権限がありません' );
	}

	@set_time_limit( 120 ); // phpcs:ignore

	$titles = array();
	foreach ( dcs_page_defs() as $slug => $def ) {
		if ( empty( $def['blocks'] ) ) {
			continue;
		}
		dcs_import_page_content( $slug );
		$titles[] = $def['title'];
	}

	dcs_sync_term_slugs();
	flush_rewrite_rules();

	wp_send_json_success( array( 'pages' => $titles ) );
}
add_action( 'wp_ajax_dcs_rebuild_pages', 'dcs_ajax_rebuild_pages' );

/* =========================================================
   個別の取り込み処理
   ========================================================= */

/**
 * スラッグから既存の投稿IDを取得する。
 *
 * @param string $slug      スラッグ。
 * @param string $post_type 投稿タイプ。
 * @return int
 */
function dcs_find_post( $slug, $post_type ) {
	$found = get_posts(
		array(
			'name'        => $slug,
			'post_type'   => $post_type,
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);

	return $found ? (int) $found[0] : 0;
}

/**
 * テーマ内の画像をメディアライブラリへ取り込む。
 *
 * @param string $rel     テーマ内の相対パス。
 * @param string $caption キャプション（写真コメント）。
 * @param string $alt     代替テキスト。
 * @param int    $parent  親投稿ID。
 * @param string $key     重複判定に使うキー。同じ写真を別のキャプションで使う場合に指定する。
 * @return int 添付ID（失敗時0）。
 */
function dcs_import_image( $rel, $caption, $alt, $parent = 0, $key = '' ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$path     = get_theme_file_path( $rel );
	$filename = basename( $rel );
	$source   = $key ? $key : $filename;
	$bytes    = null;

	if ( ! file_exists( $path ) ) {
		/* テーマに同梱されていない場合は、配布元から取得する */
		if ( ! DCS_REMOTE_ASSETS ) {
			return 0;
		}
		$res = wp_remote_get(
			rtrim( DCS_REMOTE_ASSETS, '/' ) . '/' . ltrim( $rel, '/' ),
			array( 'timeout' => 30 )
		);
		if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
			return 0;
		}
		$bytes = wp_remote_retrieve_body( $res );
		if ( ! $bytes ) {
			return 0;
		}
	}

	/* すでに取り込み済みなら再利用する */
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => '_dcs_source',
					'value'   => $source,
					'compare' => '=',
				),
			),
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	if ( null === $bytes ) {
		$bytes = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	$bits = wp_upload_bits( $filename, null, $bytes );
	if ( ! empty( $bits['error'] ) ) {
		return 0;
	}

	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $alt ? $alt : pathinfo( $filename, PATHINFO_FILENAME ),
			'post_excerpt'   => $caption,
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$bits['file'],
		$parent
	);

	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return 0;
	}

	update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $attach_id, '_dcs_source', $source );

	$meta = wp_generate_attachment_metadata( $attach_id, $bits['file'] );
	wp_update_attachment_metadata( $attach_id, $meta );

	return (int) $attach_id;
}

/**
 * 施工例を1件取り込む。
 *
 * @param array $w データ。
 */
function dcs_import_work( $w ) {
	$post_id = dcs_find_post( $w['slug'], 'dc_work' );

	$excerpt = sprintf(
		'%sで design casa の建築家が設計した%s。%s',
		$w['pref'],
		$w['type'],
		$w['catch']
	);

	$args = array(
		'post_type'    => 'dc_work',
		'post_status'  => 'publish',
		'post_title'   => $w['title'],
		'post_name'    => $w['slug'],
		'post_excerpt' => $excerpt,
		'menu_order'   => (int) $w['no'],
	);

	if ( $post_id ) {
		$args['ID'] = $post_id;
		wp_update_post( $args );
	} else {
		$post_id = wp_insert_post( $args );
	}

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return;
	}

	/* メタ情報 */
	update_post_meta( $post_id, 'dcs_work_no', (int) $w['no'] );
	update_post_meta( $post_id, 'dcs_work_catch', $w['catch'] );
	update_post_meta( $post_id, 'dcs_work_area', $w['pref'] );
	update_post_meta( $post_id, 'dcs_work_structure', '木造' . $w['type'] );

	/* 特徴タグ（スラッグをローマ字で固定するため、ID指定で割り当てる） */
	wp_set_object_terms( $post_id, dcs_ensure_terms( $w['tags'], 'dc_work_tag' ), 'dc_work_tag' );

	/* ギャラリー画像 */
	$ids = array();
	foreach ( $w['gallery'] as $i => $g ) {
		$alt = sprintf( '%s｜%sの注文住宅 施工例（%s）', $w['title'], $w['pref'], $w['type'] );
		$id  = dcs_import_image( 'assets/img/works/' . $g['file'], $g['caption'], $alt, $post_id );
		if ( $id ) {
			$ids[ $i ] = $id;
			if ( 0 === $i ) {
				set_post_thumbnail( $post_id, $id );
			}
		}
	}
	update_post_meta( $post_id, 'dcs_work_gallery', implode( ',', $ids ) );

	/* 本文をブロックで組み立てる（写真の取り込み後に行う） */
	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => dcs_work_blocks( $w, $ids ),
		)
	);
}

/**
 * 施工例の本文（ブロック）をつくる。
 *
 * @param array $w   データ。
 * @param array $ids 添付ID（ギャラリー順）。
 * @return string
 */
function dcs_work_blocks( $w, $ids ) {
	$b = array();

	$b[] = dcs_b_paragraph( esc_html( $w['catch'] ), 'work__catch' );
	$b[] = dcs_b_paragraph(
		sprintf(
			'%s に建つ、%s の住まいです。%s といった要素を、敷地の条件と暮らし方に合わせて組み立てました。design casa に登録する建築家が、規格プランの当てはめではなく一邸ずつ設計しています。',
			esc_html( $w['pref'] ),
			esc_html( $w['type'] ),
			esc_html( implode( '・', $w['tags'] ) )
		)
	);

	if ( $ids ) {
		$b[] = dcs_b_heading( 2, '写真で見る、設計の意図' );
		$b[] = dcs_b_paragraph( '1枚ごとに、なぜそう設計したのかを添えています。' );
		foreach ( $w['gallery'] as $i => $g ) {
			if ( isset( $ids[ $i ] ) ) {
				$b[] = dcs_b_image( $ids[ $i ], $g['caption'], 'large' );
			}
		}
	}

	$b[] = dcs_b_heading( 2, 'この家のような住まいを、宇都宮市で。' );
	$b[] = dcs_b_paragraph( 'こちらは design casa 加盟工務店による施工実例です。同じ建築家ネットワークを使い、株式会社エスホームが宇都宮市・栃木県で設計・施工いたします。耐震等級3・断熱等級6を標準仕様としているため、デザインを損なわずに冬あたたかく夏すずしい家になります。' );
	$b[] = dcs_b_button( 'この家について相談する（無料）', home_url( '/contact/' ) );

	return dcs_b_join( $b );
}

/**
 * 建築家を1件取り込む。
 *
 * @param array $a データ。
 */
function dcs_import_architect( $a ) {
	$post_id = dcs_find_post( $a['slug'], 'dc_architect' );

	$blocks = array();
	foreach ( explode( "\n\n", $a['body'] ) as $para ) {
		$para = trim( $para );
		if ( $para ) {
			$blocks[] = dcs_b_paragraph( esc_html( $para ) );
		}
	}
	if ( ! empty( $a['career'] ) ) {
		$blocks[] = dcs_b_heading( 2, '経歴' );
		$blocks[] = dcs_b_paragraph( nl2br( esc_html( $a['career'] ) ) );
	}

	$args = array(
		'post_type'    => 'dc_architect',
		'post_status'  => 'publish',
		'post_title'   => $a['name'],
		'post_name'    => $a['slug'],
		'post_excerpt' => $a['policy'] ? $a['policy'] : $a['office'],
		'post_content' => dcs_b_join( $blocks ),
	);

	if ( $post_id ) {
		$args['ID'] = $post_id;
		wp_update_post( $args );
	} else {
		$post_id = wp_insert_post( $args );
	}

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'dcs_arch_kana', $a['kana'] );
	update_post_meta( $post_id, 'dcs_arch_office', $a['office'] );
	update_post_meta( $post_id, 'dcs_arch_base', $a['base'] );
	update_post_meta( $post_id, 'dcs_arch_policy', $a['policy'] );
	update_post_meta( $post_id, 'dcs_arch_career', $a['career'] );

	if ( ! empty( $a['image'] ) ) {
		$id = dcs_import_image(
			'assets/img/architect/' . $a['image'],
			'',
			sprintf( '%s（%s）｜design casa 登録建築家', $a['name'], $a['office'] ),
			$post_id
		);
		if ( $id ) {
			set_post_thumbnail( $post_id, $id );
		}
	}
}

/**
 * 仕様を1件取り込む。
 *
 * @param array $s データ。
 */
function dcs_import_spec( $s ) {
	$post_id = dcs_find_post( $s['slug'], 'dc_spec' );

	$args = array(
		'post_type'    => 'dc_spec',
		'post_status'  => 'publish',
		'post_title'   => $s['title'],
		'post_name'    => $s['slug'],
		'post_excerpt' => $s['lead'],
		'post_content' => dcs_markdown_to_blocks( $s['body'] ),
	);

	if ( $post_id ) {
		$args['ID'] = $post_id;
		wp_update_post( $args );
	} else {
		$post_id = wp_insert_post( $args );
	}

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'dcs_spec_grade', $s['grade'] );
	update_post_meta( $post_id, 'dcs_spec_maker', $s['maker'] );
	update_post_meta( $post_id, 'dcs_spec_lead', $s['lead'] );
	update_post_meta( $post_id, 'dcs_seo_title', $s['seo_title'] );
	update_post_meta( $post_id, 'dcs_seo_desc', $s['seo_desc'] );

	wp_set_object_terms( $post_id, dcs_ensure_terms( array( $s['cat'] ), 'dc_spec_cat' ), 'dc_spec_cat' );

	/* 建材・製品の写真 */
	$ids = array();
	foreach ( ( isset( $s['images'] ) ? $s['images'] : array() ) as $i => $img ) {
		$alt = sprintf( '%s｜宇都宮市の注文住宅の標準仕様', wp_strip_all_tags( $img['caption'] ) );
		$id  = dcs_import_image(
			'assets/img/' . $img['file'],
			$img['caption'],
			mb_substr( $alt, 0, 110 ),
			$post_id,
			'spec-' . $s['slug'] . '-' . $i . '-' . basename( $img['file'] )
		);
		if ( $id ) {
			$ids[] = $id;
			if ( 0 === $i ) {
				set_post_thumbnail( $post_id, $id );
			}
		}
	}
	update_post_meta( $post_id, 'dcs_spec_gallery', implode( ',', $ids ) );

	/* 本文＋建材写真をブロックで組み立て直す */
	$blocks = array( dcs_markdown_to_blocks( $s['body'] ) );
	if ( $ids ) {
		$blocks[] = dcs_b_heading( 2, '写真で見る、この仕様' );
		$blocks[] = dcs_b_paragraph( '実際の建材・製品と、それが使われている場面です。' );
		foreach ( $ids as $n => $id ) {
			$cap = isset( $s['images'][ $n ]['caption'] ) ? $s['images'][ $n ]['caption'] : '';
			$blocks[] = dcs_b_image( $id, $cap, 'large' );
		}
		$blocks[] = dcs_b_paragraph( '写真：design casa 施工実例／各メーカー提供素材。仕様は予告なく変更になる場合があります。', 'matgrid__credit' );
	}
	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => dcs_b_join( $blocks ),
		)
	);
}

/**
 * かんたんなMarkdown（見出しと強調）をHTMLへ変換する。
 *
 * @param string $text 本文。
 * @return string
 */
function dcs_markdownish( $text ) {
	$out = array();
	foreach ( preg_split( '/\n{2,}/', trim( $text ) ) as $block ) {
		$block = trim( $block );
		if ( '' === $block ) {
			continue;
		}
		if ( 0 === strpos( $block, '## ' ) ) {
			$out[] = '<h2>' . esc_html( substr( $block, 3 ) ) . '</h2>';
		} elseif ( 0 === strpos( $block, '1. ' ) || 0 === strpos( $block, '- ' ) ) {
			$lines = preg_split( '/\n/', $block );
			$tag   = ( '1' === $block[0] ) ? 'ol' : 'ul';
			$li    = '';
			foreach ( $lines as $line ) {
				$li .= '<li>' . dcs_inline( preg_replace( '/^(\d+\.|-)\s*/', '', trim( $line ) ) ) . '</li>';
			}
			$out[] = "<{$tag}>{$li}</{$tag}>";
		} else {
			$out[] = '<p>' . dcs_inline( $block ) . '</p>';
		}
	}

	return implode( "\n\n", $out );
}

/**
 * **強調** をHTMLに変換する。
 *
 * @param string $text テキスト。
 * @return string
 */
function dcs_inline( $text ) {
	$text = esc_html( $text );
	$text = preg_replace( '/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text );

	return nl2br( $text );
}
