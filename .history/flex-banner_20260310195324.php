<?php

/**
 * Plugin Name: フレキシブルバナーグループ
 * Description: 柔軟なバナー管理プラグイン。グリッド/スライダー表示・テキストオーバーレイ・表示スケジュール・複製機能搭載。ショートコード [flex_banner id="POST_ID"] で表示。
 * Version: 2.0.7
 * Author: Custom
 */

if (! defined('ABSPATH')) {
  exit;
}

// Define Constants
define('FB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once FB_PLUGIN_DIR . 'includes/class-fb-post-type.php';
require_once FB_PLUGIN_DIR . 'includes/class-fb-meta-box.php';
require_once FB_PLUGIN_DIR . 'includes/class-fb-shortcode.php';

// Plugin Update Checker Integration
if (file_exists(FB_PLUGIN_DIR . 'lib/plugin-update-checker/plugin-update-checker.php')) {
  require FB_PLUGIN_DIR . 'lib/plugin-update-checker/plugin-update-checker.php';
  $myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/aurora-ship-sato/flex-banner',
    __FILE__,
    'flex-banner'
  );
  $myUpdateChecker->setAuthentication('REDACTED_GITHUB_TOKEN');
  $myUpdateChecker->setBranch('main');
}
