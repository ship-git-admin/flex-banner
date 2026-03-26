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
  }

  public function register_post_type()
  {
    $manage_cap = apply_filters('flex_banner_manage_capability', 'manage_options');
    $args = array(
      'label'               => 'フレキシブルバナー',
      'public'              => false, // Not publicly queryable on frontend as a single page
      'publicly_queryable'  => false,
      'show_ui'             => true, // Show in admin menu
      'show_in_menu'        => true,
      'capability_type'     => 'post',
      'capabilities' => array(
        'edit_post'              => $manage_cap,
        'read_post'              => $manage_cap,
        'delete_post'            => $manage_cap,
        'edit_posts'             => $manage_cap,
        'edit_others_posts'      => $manage_cap,
        'delete_posts'           => $manage_cap,
        'publish_posts'          => $manage_cap,
        'read_private_posts'     => $manage_cap,
        'create_posts'           => $manage_cap,
        'edit_private_posts'     => $manage_cap,
        'edit_published_posts'   => $manage_cap,
        'delete_private_posts'   => $manage_cap,
        'delete_published_posts' => $manage_cap,
        'delete_others_posts'    => $manage_cap,
      ),
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
}

new Flex_Banner_Post_Type();
