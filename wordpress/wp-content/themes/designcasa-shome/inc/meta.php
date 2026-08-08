<?php
/**
 * カスタムフィールド（メタボックス）
 *
 * プラグインを入れずに、施工例のスペックや建築家プロフィールを編集できます。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * 投稿タイプごとのフィールド定義。
 *
 * @return array
 */
function dcs_meta_fields() {
	return array(
		'dc_work'      => array(
			'title' => '施工例の詳細データ',
			'fields' => array(
				'dcs_work_no'         => array( 'label' => '整理番号', 'type' => 'text', 'help' => '数字が大きいほど新しい施工例として先頭に表示されます（並び順にも自動反映）。' ),
				'dcs_work_catch'      => array( 'label' => 'キャッチコピー', 'type' => 'text', 'help' => '一覧・詳細の見出し下に出る一文。' ),
				'dcs_work_area'       => array( 'label' => '所在地', 'type' => 'text', 'help' => '例：栃木県宇都宮市' ),
				'dcs_work_structure'  => array( 'label' => '構造・規模', 'type' => 'text', 'help' => '例：木造平屋建て／木造2階建て' ),
				'dcs_work_floor'      => array( 'label' => '延床面積', 'type' => 'text', 'help' => '例：112.61㎡（34.06坪）' ),
				'dcs_work_land'       => array( 'label' => '敷地面積', 'type' => 'text' ),
				'dcs_work_family'     => array( 'label' => '家族構成', 'type' => 'text', 'help' => '例：ご夫婦＋お子さま2人' ),
				'dcs_work_price'      => array( 'label' => '参考本体価格', 'type' => 'text', 'help' => '例：2,000万円台' ),
				'dcs_work_completion' => array( 'label' => '竣工', 'type' => 'text', 'help' => '例：2024年' ),
				'dcs_work_architect'  => array( 'label' => '担当建築家', 'type' => 'post', 'post_type' => 'dc_architect' ),
				'dcs_work_gallery'    => array( 'label' => '写真ギャラリー', 'type' => 'gallery', 'help' => '目を引く写真を10枚前後。各写真のコメントはメディアの「キャプション」に入力してください。' ),
			),
		),
		'dc_architect' => array(
			'title' => '建築家プロフィール',
			'fields' => array(
				'dcs_arch_kana'   => array( 'label' => 'よみがな', 'type' => 'text' ),
				'dcs_arch_office' => array( 'label' => '所属事務所', 'type' => 'text' ),
				'dcs_arch_base'   => array( 'label' => '拠点', 'type' => 'text' ),
				'dcs_arch_policy' => array( 'label' => '設計の考え方（1〜2行）', 'type' => 'textarea' ),
				'dcs_arch_career' => array( 'label' => '経歴・受賞', 'type' => 'textarea', 'help' => '1行につき1項目。' ),
				'dcs_arch_url'    => array( 'label' => '事務所サイトURL', 'type' => 'text' ),
			),
		),
		'dc_spec'      => array(
			'title' => '仕様データ',
			'fields' => array(
				'dcs_spec_grade'   => array( 'label' => '等級・数値', 'type' => 'text', 'help' => '例：耐震等級3／断熱等級6以上' ),
				'dcs_spec_maker'   => array( 'label' => '主な採用製品・メーカー', 'type' => 'textarea', 'help' => '1行につき1項目。' ),
				'dcs_spec_lead'    => array( 'label' => 'リード文', 'type' => 'textarea' ),
				'dcs_spec_gallery' => array( 'label' => '建材・製品の写真', 'type' => 'gallery', 'help' => '各写真の説明はメディアの「キャプション」に入力してください。' ),
			),
		),
	);
}

/**
 * メタボックスを追加する。
 */
function dcs_add_meta_boxes() {
	foreach ( dcs_meta_fields() as $post_type => $box ) {
		add_meta_box(
			'dcs_meta_' . $post_type,
			$box['title'],
			'dcs_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'dcs_add_meta_boxes' );

/**
 * メタボックスを描画する。
 *
 * @param WP_Post $post 投稿。
 */
function dcs_render_meta_box( $post ) {
	$all = dcs_meta_fields();
	if ( ! isset( $all[ $post->post_type ] ) ) {
		return;
	}
	$fields = $all[ $post->post_type ]['fields'];

	wp_nonce_field( 'dcs_save_meta', 'dcs_meta_nonce' );

	echo '<table class="form-table dcs-meta"><tbody>';
	foreach ( $fields as $key => $f ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th><label for="' . esc_attr( $key ) . '">' . esc_html( $f['label'] ) . '</label></th><td>';

		switch ( $f['type'] ) {
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%1$s" rows="4" class="large-text">%2$s</textarea>',
					esc_attr( $key ),
					esc_textarea( $value )
				);
				break;

			case 'post':
				$items = get_posts(
					array(
						'post_type'      => $f['post_type'],
						'posts_per_page' => -1,
						'orderby'        => 'menu_order title',
						'order'          => 'ASC',
					)
				);
				printf( '<select id="%1$s" name="%1$s"><option value="">— 未設定 —</option>', esc_attr( $key ) );
				foreach ( $items as $item ) {
					printf(
						'<option value="%1$d"%2$s>%3$s</option>',
						(int) $item->ID,
						selected( (int) $value, (int) $item->ID, false ),
						esc_html( $item->post_title )
					);
				}
				echo '</select>';
				break;

			case 'gallery':
				$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
				echo '<div class="dcs-gallery" data-target="' . esc_attr( $key ) . '">';
				echo '<div class="dcs-gallery__preview">';
				foreach ( $ids as $id ) {
					$thumb = wp_get_attachment_image( $id, 'thumbnail' );
					if ( $thumb ) {
						echo '<span class="dcs-gallery__item">' . $thumb . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
				}
				echo '</div>';
				printf(
					'<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />',
					esc_attr( $key ),
					esc_attr( implode( ',', $ids ) )
				);
				echo '<p><button type="button" class="button dcs-gallery__pick">画像を選ぶ／並べ替える</button> ';
				echo '<button type="button" class="button-link dcs-gallery__clear">クリア</button></p>';
				echo '</div>';
				break;

			default:
				printf(
					'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
					esc_attr( $key ),
					esc_attr( $value )
				);
		}

		if ( ! empty( $f['help'] ) ) {
			echo '<p class="description">' . esc_html( $f['help'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * メタボックスを保存する。
 *
 * @param int $post_id 投稿ID。
 */
function dcs_save_meta( $post_id ) {
	if ( ! isset( $_POST['dcs_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['dcs_meta_nonce'] ) ), 'dcs_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$all       = dcs_meta_fields();
	$post_type = get_post_type( $post_id );
	if ( ! isset( $all[ $post_type ] ) ) {
		return;
	}

	foreach ( $all[ $post_type ]['fields'] as $key => $f ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if ( 'textarea' === $f['type'] ) {
			$clean = sanitize_textarea_field( $raw );
		} elseif ( 'gallery' === $f['type'] ) {
			$clean = implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) ) );
		} else {
			$clean = sanitize_text_field( $raw );
		}

		update_post_meta( $post_id, $key, $clean );
	}

	/* 整理番号を menu_order に同期して、大きい番号が先頭に来るようにする */
	if ( 'dc_work' === $post_type ) {
		$no = (int) get_post_meta( $post_id, 'dcs_work_no', true );
		if ( $no && (int) get_post_field( 'menu_order', $post_id ) !== $no ) {
			remove_action( 'save_post', 'dcs_save_meta' );
			wp_update_post(
				array(
					'ID'         => $post_id,
					'menu_order' => $no,
				)
			);
			add_action( 'save_post', 'dcs_save_meta' );
		}
	}
}
add_action( 'save_post', 'dcs_save_meta' );

/**
 * 管理画面用のスクリプト・スタイル。
 *
 * @param string $hook 現在の画面。
 */
function dcs_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'dcs-admin',
		get_theme_file_uri( 'assets/js/admin.js' ),
		array( 'jquery' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
	wp_add_inline_style(
		'wp-admin',
		'.dcs-gallery__preview{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px}
		 .dcs-gallery__item img{width:88px;height:66px;object-fit:cover;display:block;border:1px solid #ccd0d4}'
	);
}
add_action( 'admin_enqueue_scripts', 'dcs_admin_assets' );

/**
 * メタ値を取得する短縮関数。
 *
 * @param string $key     キー。
 * @param int    $post_id 投稿ID。
 * @return string
 */
function dcs_meta( $key, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return (string) get_post_meta( $post_id, $key, true );
}

/**
 * ギャラリーの添付IDを配列で取得する。
 *
 * @param int    $post_id 投稿ID。
 * @param string $key     メタキー。
 * @return int[]
 */
function dcs_gallery_ids( $post_id = 0, $key = 'dcs_work_gallery' ) {
	$raw = dcs_meta( $key, $post_id );

	return array_filter( array_map( 'absint', explode( ',', $raw ) ) );
}
