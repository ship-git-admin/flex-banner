<?php

/**
 * Plugin Name: フレキシブルバナーグループ
 * Description: 柔軟なバナー管理プラグイン。グリッド/スライダー表示・テキストオーバーレイ・表示スケジュール・複製機能搭載。ショートコード [flex_banner id="POST_ID"] で表示。
 * Version: 2.4.1
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

// Add Custom Capabilities to Administrator Role
function flex_banner_add_admin_caps() {
  $role = get_role('administrator');
  if ($role) {
    $caps = array(
      'edit_flex_banner',
      'read_flex_banner',
      'delete_flex_banner',
      'edit_flex_banners',
      'edit_others_flex_banners',
      'publish_flex_banners',
      'read_private_flex_banners',
      'delete_flex_banners',
      'delete_private_flex_banners',
      'delete_published_flex_banners',
      'delete_others_flex_banners',
      'edit_private_flex_banners',
      'edit_published_flex_banners',
      'create_flex_banners',
    );
    foreach ($caps as $cap) {
      $role->add_cap($cap);
    }
  }
}
register_activation_hook(__FILE__, 'flex_banner_add_admin_caps');

// Upgrade Routine to Apply Caps on Updates without Reactivation
add_action('admin_init', function() {
  $version = get_option('flex_banner_version', '0.0.0');
  if (version_compare($version, '2.4.0', '<')) {
    flex_banner_add_admin_caps();
    update_option('flex_banner_version', '2.4.0');
  }
});

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
