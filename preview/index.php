<?php
/**
 * ローカルプレビュー用のフロントコントローラ
 *
 * 実際のテーマテンプレートをそのまま読み込んで描画します。
 */

require __DIR__ . '/bootstrap.php';

$result = dcs_render_path( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

http_response_code( $result['status'] );
echo $result['html']; // phpcs:ignore WordPress.Security.EscapeOutput
