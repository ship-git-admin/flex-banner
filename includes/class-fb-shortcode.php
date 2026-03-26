<?php
if (! defined('ABSPATH')) {
  exit;
}

class Flex_Banner_Shortcode_Display
{

  public function __construct()
  {
    add_shortcode('flex_banner', array($this, 'render_shortcode'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
  }

  public function enqueue_assets()
  {
    wp_enqueue_style('fb-frontend-style', FB_PLUGIN_URL . 'assets/css/style.css', array(), '2.2.4');
    wp_register_script('fb-frontend-js', FB_PLUGIN_URL . 'assets/js/frontend.js', array(), '2.2.4', true);
  }

  public function render_shortcode($atts)
  {
    $atts = shortcode_atts(
      array('id' => 0),
      $atts,
      'flex_banner'
    );

    $post_id = intval($atts['id']);
    if (! $post_id) {
      return '';
    }

    $data = get_post_meta($post_id, '_flex_banner_data', true);
    if (! is_array($data) || empty($data)) {
      return '';
    }

    // コンテナ設定
    $container_width = intval(get_post_meta($post_id, '_flex_banner_container_width', true));
    if ($container_width <= 0) {
      $container_width = 944;
    }
    $container_padding_pc = intval(get_post_meta($post_id, '_flex_banner_container_padding_pc', true));
    $container_padding_tab = intval(get_post_meta($post_id, '_flex_banner_container_padding_tab', true));
    $container_padding_sp = get_post_meta($post_id, '_flex_banner_container_padding_sp', true);
    if ($container_padding_sp === '') {
      $container_padding_sp = 20; // 未設定時のみ20
    } else {
      $container_padding_sp = intval($container_padding_sp);
    }

    // スライダーが含まれる場合はJSをエンキュー
    $has_slider = false;
    foreach ($data as $row) {
      if (isset($row['mode']) && $row['mode'] === 'slider') {
        $has_slider = true;
        break;
      }
    }
    if ($has_slider) {
      wp_enqueue_script('fb-frontend-js');
    }

    $container_id = 'fb-container-' . $post_id;

    ob_start();
?>
    <style>
      /* PC (1025px ~) */
      #<?php echo esc_attr($container_id); ?> {
        max-width: <?php echo intval($container_width); ?>px;
        padding-left: <?php echo intval($container_padding_pc); ?>px;
        padding-right: <?php echo intval($container_padding_pc); ?>px;
      }

      /* Tablet (768px ~ 1024px) */
      @media screen and (max-width: 1024px) {
        #<?php echo esc_attr($container_id); ?> {
          padding-left: <?php echo intval($container_padding_tab); ?>px;
          padding-right: <?php echo intval($container_padding_tab); ?>px;
        }
      }

      /* Smartphone (~ 767px) */
      @media screen and (max-width: 767px) {
        #<?php echo esc_attr($container_id); ?> {
          padding-left: <?php echo intval($container_padding_sp); ?>px;
          padding-right: <?php echo intval($container_padding_sp); ?>px;
        }
      }
    </style>
    <div class="fb-container" id="<?php echo esc_attr($container_id); ?>">
      <?php foreach ($data as $row_index => $row) : ?>
        <?php
        $mode    = isset($row['mode']) ? $row['mode'] : 'grid';
        $items   = isset($row['items']) ? $row['items'] : array();

        // スケジュールフィルタリング
        $visible_items = array();
        $now = current_time('timestamp');
        foreach ($items as $item) {
          $start = isset($item['schedule_start']) ? trim($item['schedule_start']) : '';
          $end   = isset($item['schedule_end'])   ? trim($item['schedule_end'])   : '';
          if ($start && strtotime($start) > $now) {
            continue;
          }
          if ($end && strtotime($end) < $now) {
            continue;
          }
          $visible_items[] = $item;
        }

        if (empty($visible_items)) {
          continue;
        }

        if ($mode === 'slider') {
          $this->render_slider_row($row, $visible_items);
        } else {
          $this->render_grid_row($row, $visible_items);
        }
        ?>
      <?php endforeach; ?>
    </div>
  <?php
    return ob_get_clean();
  }

  /* ------------------------------------------------------------------ */
  /*  グリッド表示                                                          */
  /* ------------------------------------------------------------------ */

  private function render_grid_row($row, $items)
  {
    $cols_pc = isset($row['cols_pc']) ? intval($row['cols_pc']) : 1;
    $cols_sp = isset($row['cols_sp']) ? intval($row['cols_sp']) : 1;
    $gap_pc  = isset($row['gap_pc'])  ? intval($row['gap_pc'])  : 12;
    $gap_sp  = isset($row['gap_sp'])  ? intval($row['gap_sp'])  : 10;

    $row_class = sprintf('fb-row fb-row--pc-%d fb-row--sp-%d', $cols_pc, $cols_sp);
    $row_style = sprintf('--fb-gap-pc:%dpx;--fb-gap-sp:%dpx;', $gap_pc, $gap_sp);
  ?>
    <div class="<?php echo esc_attr($row_class); ?>" style="<?php echo esc_attr($row_style); ?>">
      <?php foreach ($items as $item) : ?>
        <div class="fb-column">
          <?php $this->render_item_inner($item); ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php
  }

  /* ------------------------------------------------------------------ */
  /*  スライダー表示                                                        */
  /* ------------------------------------------------------------------ */

  private function render_slider_row($row, $items)
  {
    $animation = isset($row['slider_animation']) ? $row['slider_animation'] : 'slide';
    $autoplay  = isset($row['slider_autoplay'])  ? intval($row['slider_autoplay']) : 1;
    $interval  = isset($row['slider_interval'])  ? intval($row['slider_interval']) : 5;
    $arrows    = isset($row['slider_arrows'])    ? intval($row['slider_arrows']) : 1;
    $dots      = isset($row['slider_dots'])      ? intval($row['slider_dots']) : 1;
  ?>
    <div class="fb-slider fb-row"
      data-animation="<?php echo esc_attr($animation); ?>"
      data-autoplay="<?php echo esc_attr($autoplay); ?>"
      data-interval="<?php echo esc_attr($interval * 1000); ?>"
      data-arrows="<?php echo esc_attr($arrows); ?>"
      data-dots="<?php echo esc_attr($dots); ?>">
      <div class="fb-slider-track fb-slider-track--<?php echo esc_attr($animation); ?>">
        <?php foreach ($items as $item) : ?>
          <div class="fb-slide">
            <?php $this->render_item_inner($item); ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if ($arrows) : ?>
        <button type="button" class="fb-slider-prev" aria-label="前へ">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
        <button type="button" class="fb-slider-next" aria-label="次へ">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>
      <?php endif; ?>
      <?php if ($dots) : ?>
        <div class="fb-slider-dots"></div>
      <?php endif; ?>
    </div>
    <?php
  }

  /* ------------------------------------------------------------------ */
  /*  バナーアイテム内部（グリッド・スライダー共通）                           */
  /* ------------------------------------------------------------------ */

  private function render_item_inner($item)
  {
    $img_pc_id        = isset($item['img_pc'])           ? intval($item['img_pc'])           : 0;
    $img_sp_id        = isset($item['img_sp'])           ? intval($item['img_sp'])           : 0;
    $url              = isset($item['url'])              ? trim($item['url'])              : '';
    $target           = (isset($item['target_blank']) && $item['target_blank']) ? ' target="_blank" rel="noopener"' : '';
    $caption_heading  = isset($item['caption_heading'])  ? trim($item['caption_heading'])  : '';
    $caption_body     = isset($item['caption_body'])     ? trim($item['caption_body'])     : '';
    $caption_position = isset($item['caption_position']) ? $item['caption_position']       : 'center';
    $caption_btn_text = isset($item['caption_btn_text']) ? trim($item['caption_btn_text']) : '';
    $caption_btn_url  = isset($item['caption_btn_url'])  ? trim($item['caption_btn_url'])  : '';

    $img_pc_data = $img_pc_id ? wp_get_attachment_image_src($img_pc_id, 'full') : false;
    $img_sp_data = $img_sp_id ? wp_get_attachment_image_src($img_sp_id, 'full') : false;

    if (! $img_pc_data) {
      return;
    }

    $pc_url = $img_pc_data[0];
    $pc_w   = $img_pc_data[1];
    $pc_h   = $img_pc_data[2];
    $sp_url = $img_sp_data ? $img_sp_data[0] : $pc_url;

    $alt = get_post_meta($img_pc_id, '_wp_attachment_image_alt', true);

    $has_caption = $caption_heading || $caption_body || $caption_btn_text;
    // キャプションボタンがある場合は外側のaタグは使わない（入れ子回避）
    $use_outer_link = $url && ! $caption_btn_text;
    // ボタンのリンク先：専用URLが設定されていればそちら、なければバナーリンク
    $btn_href = $caption_btn_url ?: $url;

    if ($use_outer_link) :
    ?><a href="<?php echo esc_url($url); ?>" class="fb-link" <?php echo $target; ?>><?php
                                                                                  endif;
                                                                                    ?>
      <div class="fb-image<?php echo $has_caption ? ' fb-image--has-caption' : ''; ?>">
        <picture>
          <source media="(max-width: 767px)" srcset="<?php echo esc_url($sp_url); ?>">
          <img src="<?php echo esc_url($pc_url); ?>"
            alt="<?php echo esc_attr($alt); ?>"
            width="<?php echo esc_attr($pc_w); ?>"
            height="<?php echo esc_attr($pc_h); ?>"
            loading="lazy"
            decoding="async"
            class="fb-image__img">
        </picture>

        <?php if ($has_caption) : ?>
          <div class="fb-caption fb-caption--<?php echo esc_attr($caption_position); ?>">
            <?php if ($caption_heading) : ?>
              <p class="fb-caption__heading"><?php echo esc_html($caption_heading); ?></p>
            <?php endif; ?>
            <?php if ($caption_body) : ?>
              <p class="fb-caption__body"><?php echo nl2br(esc_html($caption_body)); ?></p>
            <?php endif; ?>
            <?php if ($caption_btn_text && $btn_href) : ?>
              <a href="<?php echo esc_url($btn_href); ?>" class="fb-caption__btn" <?php echo $target; ?>><?php echo esc_html($caption_btn_text); ?></a>
            <?php elseif ($caption_btn_text) : ?>
              <span class="fb-caption__btn fb-caption__btn--nolink"><?php echo esc_html($caption_btn_text); ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php
      if ($use_outer_link) :
      ?>
      </a><?php
        endif;
      }
    }

    new Flex_Banner_Shortcode_Display();
