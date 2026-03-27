<?php
if (! defined('ABSPATH')) {
  exit;
}

class Flex_Banner_Post_Type
{

  public function __construct()
  {
    add_action('init', array($this, 'register_post_type'));
    add_filter('manage_flex_banner_group_posts_columns', array($this, 'add_custom_columns'));
    add_action('manage_flex_banner_group_posts_custom_column', array($this, 'render_custom_columns'), 10, 2);
    add_action('admin_notices', array($this, 'render_admin_notices'));
  }

  public function register_post_type()
  {
    $args = array(
      'label'               => 'フレキシブルバナー',
      'public'              => false, // Not publicly queryable on frontend as a single page
      'publicly_queryable'  => false,
      'show_ui'             => true, // Show in admin menu
      'show_in_menu'        => true,
      'capability_type'     => array('flex_banner', 'flex_banners'),
      'map_meta_cap'        => true,
      'hierarchical'        => false,
      'supports'            => array('title'), // Only title, content managed via meta box
      'menu_icon'           => 'dashicons-format-gallery',
      'has_archive'         => false,
      'rewrite'            => false,
    );

    register_post_type('flex_banner_group', $args);
  }

  public function add_custom_columns($columns)
  {
    $new_columns = array();
    foreach ($columns as $key => $value) {
      $new_columns[$key] = $value;
      if ($key === 'title') {
        $new_columns['shortcode'] = 'ショートコード';
      }
    }
    return $new_columns;
  }

  public function render_custom_columns($column, $post_id)
  {
    if ($column === 'shortcode') {
      echo '<code>[flex_banner id="' . $post_id . '"]</code>';
    }
  }

  public function render_admin_notices()
  {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'edit-flex_banner_group') {
?>
      <div class="notice notice-info is-dismissible fb-admin-notice" style="border-left-color: #00a0d2; padding: 12px 20px;">
        <h3 style="margin: 0 0 10px 0; font-size: 16px;">💡 フレキシブルバナーの使い方ガイド</h3>
        <ol style="margin: 0 0 10px 20px; list-style-type: decimal;">
          <li style="margin-bottom: 5px;">一番上の<b>「新規追加」</b>ボタンを押して、バナーセット（画像やURLの集まり）を作ります。</li>
          <li style="margin-bottom: 5px;">保存すると、この一覧表に<b>「ショートコード」(例: [flex_banner id="123"])</b> が出ます。</li>
          <li>そのショートコードをコピーして、表示したい固定ページの本文に貼り付けるだけ！</li>
        </ol>
        <p style="margin: 0; color: #666; font-size: 12px;">※高度な設定（スライダー表示、スケジュール設定など）は、各バナーの編集画面で行えます。</p>
      </div>
<?php
    }
  }
}

new Flex_Banner_Post_Type();
