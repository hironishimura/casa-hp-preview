<?php
/**
 * PHPビルトインサーバー用のルーター
 *
 * 実ファイル（画像・CSS・JS）はそのまま返し、それ以外は index.php に渡します。
 */

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$file = rtrim( $_SERVER['DOCUMENT_ROOT'], '/' ) . rawurldecode( $path );

if ( '/' !== $path && is_file( $file ) ) {
	return false; // 静的ファイルとして配信.
}

require __DIR__ . '/index.php';
