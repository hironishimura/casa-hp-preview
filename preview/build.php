<?php
/**
 * 静的サイトの書き出し（GitHub Pages 用）
 *
 *   php preview/build.php https://ユーザー名.github.io/リポジトリ名 docs
 *
 * テーマのテンプレートをそのまま描画し、全ページを HTML ファイルとして出力します。
 * 内容確認のためのプレビューなので、検索エンジンには登録させません（noindex + robots.txt）。
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( "CLIから実行してください\n" );
}

$site = rtrim( $argv[1] ?? '', '/' );
$out  = dirname( __DIR__ ) . '/' . ( $argv[2] ?? 'docs' );

define( 'DCS_SITE', $site );
define( 'DCS_ASSET_BASE', '/theme' );
define( 'DCS_BUILD', true );
define( 'DCS_NOINDEX', true );

require __DIR__ . '/bootstrap.php';

/* =========================================================
   出力先の準備
   ========================================================= */
if ( is_dir( $out ) ) {
	$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $out, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
	foreach ( $it as $f ) {
		$f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
	}
} else {
	mkdir( $out, 0755, true );
}

/**
 * ファイルを書き出す。
 */
function dcs_put( $out, $rel, $body ) {
	$path = rtrim( $out, '/' ) . '/' . ltrim( $rel, '/' );
	$dir  = dirname( $path );
	if ( ! is_dir( $dir ) ) { mkdir( $dir, 0755, true ); }
	file_put_contents( $path, $body );
}

/**
 * ディレクトリを再帰コピーする。
 */
function dcs_copy_dir( $src, $dst ) {
	if ( ! is_dir( $dst ) ) { mkdir( $dst, 0755, true ); }
	foreach ( scandir( $src ) as $f ) {
		if ( '.' === $f || '..' === $f || '.DS_Store' === $f ) { continue; }
		$s = $src . '/' . $f;
		$d = $dst . '/' . $f;
		is_dir( $s ) ? dcs_copy_dir( $s, $d ) : copy( $s, $d );
	}
}

/* =========================================================
   出力するURLの一覧を組み立てる
   ========================================================= */
$paths = array( '/' );

foreach ( array_keys( dcs_page_defs() ) as $slug ) {
	$paths[] = '/' . $slug . '/';
}

$paths[] = '/works/';
$works   = dcs_pv_query( array( 'post_type' => 'dc_work', 'posts_per_page' => -1 ) );
$pages   = (int) ceil( count( $works ) / 12 );
for ( $i = 2; $i <= $pages; $i++ ) {
	$paths[] = '/works/page/' . $i . '/';
}
foreach ( $works as $w ) {
	$paths[] = '/works/' . $w->post_name . '/';
}
foreach ( $GLOBALS['dcs_terms']['dc_work_tag'] as $t ) {
	$paths[] = '/works/feature/' . $t->slug . '/';
}

$paths[] = '/architect/';
foreach ( dcs_pv_query( array( 'post_type' => 'dc_architect', 'posts_per_page' => -1 ) ) as $a ) {
	$paths[] = '/architect/' . $a->post_name . '/';
}

$paths[] = '/spec/';
foreach ( dcs_pv_query( array( 'post_type' => 'dc_spec', 'posts_per_page' => -1 ) ) as $s ) {
	$paths[] = '/spec/' . $s->post_name . '/';
}
foreach ( $GLOBALS['dcs_terms']['dc_spec_cat'] as $t ) {
	$paths[] = '/spec/category/' . $t->slug . '/';
}

/* =========================================================
   書き出し
   ========================================================= */
$n = 0;
foreach ( $paths as $p ) {
	$r   = dcs_render_path( $p );
	$rel = ( '/' === $p ) ? 'index.html' : trim( $p, '/' ) . '/index.html';
	dcs_put( $out, $rel, $r['html'] );
	$n++;
}

/* 404 */
$r = dcs_render_path( '/__404__/' );
dcs_put( $out, '404.html', $r['html'] );

/* テーマのアセット（CSS・JS・画像） */
dcs_copy_dir( DCS_THEME_DIR . '/assets', $out . '/theme/assets' );
copy( DCS_THEME_DIR . '/style.css', $out . '/theme/style.css' );

/* GitHub Pages 用 */
dcs_put( $out, '.nojekyll', '' );
dcs_put(
	$out,
	'robots.txt',
	"# 内容確認用のプレビューサイトです。検索エンジンには登録しないでください。\nUser-agent: *\nDisallow: /\n"
);

printf( "%d ページ書き出しました → %s\n", $n + 1, $out );
printf( "サイトURL: %s/\n", $site );
