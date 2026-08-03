<?php
/**
 * カスタム投稿タイプ / タクソノミー
 *
 * 施工例・建築家・家の仕様を、プラグインなしで管理できるようにします。
 *
 * @package DesignCasa_SHome
 */

defined( 'ABSPATH' ) || exit;

/**
 * カスタム投稿タイプを登録する。
 */
function dcs_register_post_types() {

	/* ---------- 施工例 ---------- */
	register_post_type(
		'dc_work',
		array(
			'label'         => '施工例',
			'labels'        => array(
				'name'          => '施工例',
				'singular_name' => '施工例',
				'add_new'       => '施工例を追加',
				'add_new_item'  => '施工例を追加',
				'edit_item'     => '施工例を編集',
				'all_items'     => '施工例一覧',
				'search_items'  => '施工例を検索',
			),
			'public'        => true,
			'has_archive'   => 'works',
			'rewrite'       => array(
				'slug'       => 'works',
				'with_front' => false,
			),
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-admin-home',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	/* ---------- 建築家 ---------- */
	register_post_type(
		'dc_architect',
		array(
			'label'         => '建築家',
			'labels'        => array(
				'name'          => '建築家',
				'singular_name' => '建築家',
				'add_new'       => '建築家を追加',
				'add_new_item'  => '建築家を追加',
				'edit_item'     => '建築家を編集',
				'all_items'     => '建築家一覧',
			),
			'public'        => true,
			'has_archive'   => 'architect',
			'rewrite'       => array(
				'slug'       => 'architect',
				'with_front' => false,
			),
			'menu_position' => 6,
			'menu_icon'     => 'dashicons-businessperson',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	/* ---------- 家の仕様 ---------- */
	register_post_type(
		'dc_spec',
		array(
			'label'         => '家の仕様',
			'labels'        => array(
				'name'          => '家の仕様',
				'singular_name' => '仕様',
				'add_new'       => '仕様を追加',
				'add_new_item'  => '仕様を追加',
				'edit_item'     => '仕様を編集',
				'all_items'     => '仕様一覧',
			),
			'public'        => true,
			'has_archive'   => 'spec',
			'rewrite'       => array(
				'slug'       => 'spec',
				'with_front' => false,
			),
			'menu_position' => 7,
			'menu_icon'     => 'dashicons-hammer',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'dcs_register_post_types' );

/**
 * タクソノミーを登録する。
 */
function dcs_register_taxonomies() {

	/* 施工例の特徴タグ（平屋・ペット・全館空調 などSEO受け皿になる） */
	register_taxonomy(
		'dc_work_tag',
		array( 'dc_work' ),
		array(
			'label'             => '施工例の特徴',
			'labels'            => array(
				'name'          => '施工例の特徴',
				'singular_name' => '特徴',
				'add_new_item'  => '特徴を追加',
				'all_items'     => '特徴一覧',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'works/feature',
				'with_front' => false,
			),
		)
	);

	/* 仕様のカテゴリ */
	register_taxonomy(
		'dc_spec_cat',
		array( 'dc_spec' ),
		array(
			'label'             => '仕様カテゴリ',
			'labels'            => array(
				'name'          => '仕様カテゴリ',
				'singular_name' => 'カテゴリ',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'spec/category',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'dcs_register_taxonomies' );

/**
 * アーカイブの表示件数と並び順。
 *
 * 施工例は「番号が大きいほど新しい」ため、menu_order の降順で並べる。
 *
 * @param WP_Query $query クエリ。
 */
function dcs_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'dc_work' ) || $query->is_tax( 'dc_work_tag' ) ) {
		$query->set( 'posts_per_page', 12 );
		$query->set( 'orderby', array( 'menu_order' => 'DESC', 'date' => 'DESC' ) );
	}

	if ( $query->is_post_type_archive( 'dc_architect' ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}

	if ( $query->is_post_type_archive( 'dc_spec' ) || $query->is_tax( 'dc_spec_cat' ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'dcs_pre_get_posts' );
