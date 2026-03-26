<?php
if (! defined('ABSPATH')) {
  exit;
}

class Flex_Banner_Meta_Box
{

  public function __construct()
  {
    add_action('add_meta_boxes', array($this, 'add_meta_box'));
    add_action('save_post', array($this, 'save_data'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
  }

  public function add_meta_box()
  {
    add_meta_box(
      'fb_flexible_content',
      'バナー構成（セクション編集）',
      array($this, 'render_meta_box'),
      'flex_banner_group',
      'normal',
      'high'
    );
    add_meta_box(
      'fb_shortcode_display',
      'ショートコード',
      array($this, 'render_shortcode_meta_box'),
      'flex_banner_group',
      'side',
      'default'
    );
    add_meta_box(
      'fb_container_settings',
      'コンテナ設定',
      array($this, 'render_container_meta_box'),
      'flex_banner_group',
      'side',
      'default'
    );
  }

  public function enqueue_admin_scripts($hook)
  {
    global $post;

    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
      return;
    }
    if ('flex_banner_group' !== $post->post_type) {
      return;
    }

    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('fb-admin-js', FB_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), '2.2.5', true);
    wp_enqueue_style('fb-admin-css', FB_PLUGIN_URL . 'assets/css/admin.css', array(), '2.2.5');

    // コンテナ幅をJSに渡す
    $container_width = intval(get_post_meta($post->ID, '_flex_banner_container_width', true));
    if ($container_width <= 0) {
      $container_width = 944;
    }
    wp_localize_script('fb-admin-js', 'fbAdminData', array(
      'containerWidth' => $container_width,
    ));
  }

  /* ------------------------------------------------------------------ */
  /*  メタボックス描画                                                     */
  /* ------------------------------------------------------------------ */

  public function render_meta_box($post)
  {
    wp_nonce_field('fb_save_data', 'fb_nonce');
    $data = get_post_meta($post->ID, '_flex_banner_data', true);
    if (! is_array($data)) {
      $data = array();
    }
?>
    <div id="fb-editor-wrapper">
      <div class="fb-editor-intro">
        <p>「セクション」は1つのバナーグループです。グリッド表示またはスライダー表示を選択できます。各バナーにはテキストオーバーレイや表示スケジュールを個別に設定できます。</p>
      </div>
      <div id="fb-rows-container">
        <?php
        if (! empty($data)) {
          foreach ($data as $row_index => $row) {
            $this->render_row($row_index, $row);
          }
        }
        ?>
      </div>
      <div class="fb-actions">
        <button type="button" class="button button-primary fb-add-row">＋ 新しいセクションを追加</button>
      </div>
    </div>

    <script type="text/html" id="tmpl-fb-row">
      <?php $this->render_row('{{row_index}}', array()); ?>
    </script>
    <script type="text/html" id="tmpl-fb-item">
      <?php $this->render_item('{{row_index}}', '{{item_index}}', array()); ?>
    </script>
  <?php
  }

  public function render_shortcode_meta_box($post)
  {
  ?>
    <div class="fb-shortcode-box">
      <p>このバナーセットを記事に表示するには、以下のコードをコピーして貼り付けてください。</p>
      <div class="fb-shortcode-input-wrap">
        <input type="text" id="fb-shortcode-value" value='[flex_banner id="<?php echo $post->ID; ?>"]' readonly class="widefat">
        <button type="button" class="button fb-copy-shortcode" data-target="fb-shortcode-value">コピー</button>
      </div>
      <p class="description">固定ページや投稿の編集画面に貼り付けて使用します。</p>
    </div>
  <?php
  }

  public function render_container_meta_box($post)
  {
    $container_width = intval(get_post_meta($post->ID, '_flex_banner_container_width', true));
    if ($container_width <= 0) {
      $container_width = 944;
    }
    $container_padding_pc = intval(get_post_meta($post->ID, '_flex_banner_container_padding_pc', true));
    $container_padding_tab = intval(get_post_meta($post->ID, '_flex_banner_container_padding_tab', true));
    $container_padding_sp = get_post_meta($post->ID, '_flex_banner_container_padding_sp', true);
    if ($container_padding_sp === '') {
      $container_padding_sp = 20; // 未設定時のみ20
    } else {
      $container_padding_sp = intval($container_padding_sp);
    }
  ?>
    <div class="fb-container-settings-box">
      <p class="description" style="margin-bottom:12px;">フロントエンドのバナー全体の横幅を設定します。推奨サイズ計算にも反映されます。</p>
      <table class="form-table" style="margin:0;">
        <tr>
          <th style="padding:6px 0;font-size:12px;">PC最大幅</th>
          <td style="padding:6px 0;">
            <input type="number" id="fb-container-width-input" name="fb_container_width" value="<?php echo esc_attr($container_width); ?>" min="320" max="2560" style="width:80px;"> px
            <p class="description" style="margin-top:4px;">デフォルト: 944px</p>
          </td>
        </tr>
        <tr>
          <th style="padding:6px 0;font-size:12px;">PC側余白 (1025px~)</th>
          <td style="padding:6px 0;">
            <input type="number" id="fb-container-padding-pc-input" name="fb_container_padding_pc" value="<?php echo esc_attr($container_padding_pc); ?>" min="0" max="300" style="width:80px;"> px
            <p class="description" style="margin-top:4px;">デフォルト: 0px</p>
          </td>
        </tr>
        <tr>
          <th style="padding:6px 0;font-size:12px;">TB側余白 (768~1024px)</th>
          <td style="padding:6px 0;">
            <input type="number" id="fb-container-padding-tab-input" name="fb_container_padding_tab" value="<?php echo esc_attr($container_padding_tab); ?>" min="0" max="200" style="width:80px;"> px
            <p class="description" style="margin-top:4px;">デフォルト: 0px</p>
          </td>
        </tr>
        <tr>
          <th style="padding:6px 0;font-size:12px;">SP側余白 (~767px)</th>
          <td style="padding:6px 0;">
            <input type="number" id="fb-container-padding-sp-input" name="fb_container_padding_sp" value="<?php echo esc_attr($container_padding_sp); ?>" min="0" max="150" style="width:80px;"> px
            <p class="description" style="margin-top:4px;">デフォルト: 20px</p>
          </td>
        </tr>
      </table>
    </div>
  <?php
  }

  /* ------------------------------------------------------------------ */
  /*  セクション（行）描画                                                  */
  /* ------------------------------------------------------------------ */

  private function render_row($index, $data)
  {
    $mode         = isset($data['mode'])             ? $data['mode']             : 'grid';
    $cols_pc      = isset($data['cols_pc'])          ? $data['cols_pc']          : 1;
    $cols_sp      = isset($data['cols_sp'])          ? $data['cols_sp']          : 1;
    $gap_pc       = isset($data['gap_pc'])           ? $data['gap_pc']           : 12;
    $gap_sp       = isset($data['gap_sp'])           ? $data['gap_sp']           : 10;
    $sl_animation = isset($data['slider_animation']) ? $data['slider_animation'] : 'slide';
    $sl_autoplay  = isset($data['slider_autoplay'])  ? $data['slider_autoplay']  : 1;
    $sl_interval  = isset($data['slider_interval'])  ? $data['slider_interval']  : 5;
    $sl_arrows    = isset($data['slider_arrows'])    ? $data['slider_arrows']    : 1;
    $sl_dots      = isset($data['slider_dots'])      ? $data['slider_dots']      : 1;
    $items        = isset($data['items'])            ? $data['items']            : array();
    $is_slider    = ($mode === 'slider');
  ?>
    <div class="fb-row" data-index="<?php echo esc_attr($index); ?>" data-mode="<?php echo esc_attr($mode); ?>">
      <div class="fb-row-header">
        <div class="fb-row-title-grab">
          <span class="dashicons dashicons-menu fb-drag-handle"></span>
          <span class="fb-row-label">セクション</span>
        </div>

        <div class="fb-mode-toggle">
          <button type="button" class="fb-mode-btn <?php echo !$is_slider ? 'is-active' : ''; ?>" data-mode="grid">
            <span class="dashicons dashicons-grid-view"></span> グリッド
          </button>
          <button type="button" class="fb-mode-btn <?php echo $is_slider ? 'is-active' : ''; ?>" data-mode="slider">
            <span class="dashicons dashicons-slides"></span> スライダー
          </button>
          <input type="hidden" name="fb_data[<?php echo esc_attr($index); ?>][mode]" class="fb-row-mode" value="<?php echo esc_attr($mode); ?>">
        </div>

        <div class="fb-row-header-actions">
          <button type="button" class="button-link fb-duplicate-row__btn">複製</button>
          <button type="button" class="button-link fb-remove-row__btn text-error">削除</button>
        </div>
      </div>

      <div class="fb-row-settings-panel">
        <div class="fb-grid-settings <?php echo $is_slider ? 'fb-hidden' : ''; ?>">
          <label>PC：1行の枚数
            <select name="fb_data[<?php echo esc_attr($index); ?>][cols_pc]" class="fb-cols-pc">
              <option value="1" <?php selected($cols_pc, 1); ?>>1枚（大きく表示）</option>
              <option value="2" <?php selected($cols_pc, 2); ?>>2枚並べる</option>
              <option value="3" <?php selected($cols_pc, 3); ?>>3枚並べる</option>
              <option value="4" <?php selected($cols_pc, 4); ?>>4枚並べる</option>
            </select>
          </label>
          <label>スマホ：1行の枚数
            <select name="fb_data[<?php echo esc_attr($index); ?>][cols_sp]" class="fb-cols-sp">
              <option value="1" <?php selected($cols_sp, 1); ?>>1枚（縦に並べる）</option>
              <option value="2" <?php selected($cols_sp, 2); ?>>2枚並べる</option>
            </select>
          </label>
          <label>PC余白:
            <input type="number" name="fb_data[<?php echo esc_attr($index); ?>][gap_pc]" value="<?php echo intval($gap_pc); ?>" class="fb-gap-pc" style="width:55px;"> px
          </label>
          <label>スマホ余白:
            <input type="number" name="fb_data[<?php echo esc_attr($index); ?>][gap_sp]" value="<?php echo intval($gap_sp); ?>" class="fb-gap-sp" style="width:55px;"> px
          </label>
          <span class="fb-size-guide">推奨サイズ: <span class="fb-guide-pc"></span> / <span class="fb-guide-sp"></span></span>
        </div>

        <div class="fb-slider-settings <?php echo !$is_slider ? 'fb-hidden' : ''; ?>">
          <label>アニメーション:
            <select name="fb_data[<?php echo esc_attr($index); ?>][slider_animation]">
              <option value="slide" <?php selected($sl_animation, 'slide'); ?>>スライド</option>
              <option value="fade" <?php selected($sl_animation, 'fade');  ?>>フェード</option>
            </select>
          </label>
          <label class="fb-inline-check">
            <input type="checkbox" name="fb_data[<?php echo esc_attr($index); ?>][slider_autoplay]" value="1" <?php checked($sl_autoplay, 1); ?>>
            自動再生
          </label>
          <label>切替間隔:
            <input type="number" name="fb_data[<?php echo esc_attr($index); ?>][slider_interval]" value="<?php echo intval($sl_interval); ?>" min="1" max="60" style="width:55px;"> 秒
          </label>
          <label class="fb-inline-check">
            <input type="checkbox" name="fb_data[<?php echo esc_attr($index); ?>][slider_arrows]" value="1" <?php checked($sl_arrows, 1); ?>>
            矢印ナビ
          </label>
          <label class="fb-inline-check">
            <input type="checkbox" name="fb_data[<?php echo esc_attr($index); ?>][slider_dots]" value="1" <?php checked($sl_dots, 1); ?>>
            ドットナビ
          </label>
        </div>
      </div>

      <div class="fb-items-wrapper">
        <?php
        if (! empty($items)) {
          foreach ($items as $item_index => $item) {
            $this->render_item($index, $item_index, $item);
          }
        }
        ?>
      </div>
      <div class="fb-row-footer">
        <button type="button" class="button fb-add-item">
          <span class="dashicons dashicons-plus-alt2" style="font-size:16px;margin-top:4px;"></span> バナーを追加
        </button>
      </div>
    </div>
  <?php
  }

  /* ------------------------------------------------------------------ */
  /*  バナーアイテム描画                                                    */
  /* ------------------------------------------------------------------ */

  private function render_item($row_index, $item_index, $data)
  {
    $img_pc           = isset($data['img_pc'])           ? $data['img_pc']           : '';
    $img_sp           = isset($data['img_sp'])           ? $data['img_sp']           : '';
    $url              = isset($data['url'])              ? $data['url']              : '';
    $target_blank     = isset($data['target_blank'])     ? $data['target_blank']     : 0;
    $schedule_start   = isset($data['schedule_start'])   ? $data['schedule_start']   : '';
    $schedule_end     = isset($data['schedule_end'])     ? $data['schedule_end']     : '';
    $caption_heading  = isset($data['caption_heading'])  ? $data['caption_heading']  : '';
    $caption_body     = isset($data['caption_body'])     ? $data['caption_body']     : '';
    $caption_position = isset($data['caption_position']) ? $data['caption_position'] : 'center';
    $caption_btn_text = isset($data['caption_btn_text']) ? $data['caption_btn_text'] : '';
    $caption_btn_url  = isset($data['caption_btn_url'])  ? $data['caption_btn_url']  : '';

    $img_pc_src   = $img_pc ? wp_get_attachment_image_url($img_pc, 'thumbnail') : '';
    $img_sp_src   = $img_sp ? wp_get_attachment_image_url($img_sp, 'thumbnail') : '';
    $has_caption  = $caption_heading || $caption_body || $caption_btn_text;
    $has_schedule = $schedule_start || $schedule_end;

    $n = esc_attr($row_index);
    $i = esc_attr($item_index);
  ?>
    <div class="fb-item" data-index="<?php echo esc_attr($item_index); ?>">
      <div class="fb-item-header">
        <div class="fb-item-title-wrap">
          <span class="dashicons dashicons-move fb-item-drag-handle"></span>
          <span class="fb-item-label">バナー #<span class="fb-item-num"></span></span>
          <span class="fb-item-url-preview <?php echo !$url ? 'is-empty' : ''; ?>"><?php echo $url ? esc_html($url) : '(リンク未設定)'; ?></span>
          <?php if ($has_schedule) : ?><span class="fb-item-badge" title="スケジュール設定あり">📅</span><?php endif; ?>
          <?php if ($has_caption) : ?><span class="fb-item-badge fb-item-badge--text" title="テキストオーバーレイあり">T</span><?php endif; ?>
        </div>
        <div class="fb-item-actions">
          <button type="button" class="button-link fb-duplicate-item__btn">複製</button>
          <button type="button" class="button-link fb-remove-item__btn text-error">削除</button>
        </div>
      </div>

      <div class="fb-item-body">
        <div class="fb-item-images">
          <div class="fb-field-group fb-field-pc">
            <label><span class="dashicons dashicons-desktop"></span> PC用画像</label>
            <div class="fb-image-uploader" data-target="img_pc">
              <input type="hidden" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][img_pc]" value="<?php echo esc_attr($img_pc); ?>" class="fb-img-id">
              <div class="fb-image-preview">
                <?php if ($img_pc_src) : ?><img src="<?php echo esc_url($img_pc_src); ?>" alt=""><?php else : ?><span class="dashicons dashicons-format-image"></span><?php endif; ?>
              </div>
              <div class="fb-image-actions">
                <button type="button" class="button fb-upload-btn"><?php echo $img_pc ? '変更' : '選択'; ?></button>
                <button type="button" class="button-link fb-remove-img-btn" <?php echo $img_pc ? '' : 'style="display:none;"'; ?>>削除</button>
              </div>
            </div>
          </div>
          <div class="fb-field-group fb-field-sp">
            <label><span class="dashicons dashicons-smartphone"></span> SP用画像 <small>(任意)</small></label>
            <div class="fb-image-uploader" data-target="img_sp">
              <input type="hidden" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][img_sp]" value="<?php echo esc_attr($img_sp); ?>" class="fb-img-id">
              <div class="fb-image-preview">
                <?php if ($img_sp_src) : ?><img src="<?php echo esc_url($img_sp_src); ?>" alt=""><?php else : ?><span class="dashicons dashicons-format-image"></span><?php endif; ?>
              </div>
              <div class="fb-image-actions">
                <button type="button" class="button fb-upload-btn"><?php echo $img_sp ? '変更' : '選択'; ?></button>
                <button type="button" class="button-link fb-remove-img-btn" <?php echo $img_sp ? '' : 'style="display:none;"'; ?>>削除</button>
              </div>
            </div>
          </div>
        </div>

        <div class="fb-field-group">
          <label><span class="dashicons dashicons-admin-links"></span> リンク先</label>
          <input type="text" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][url]" value="<?php echo esc_attr($url); ?>" class="widefat fb-url-input" placeholder="例：/contact/ や https://google.com">
          <label class="fb-checkbox-label">
            <input type="checkbox" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][target_blank]" value="1" <?php checked($target_blank); ?>>
            別タブで開く
          </label>
        </div>

        <div class="fb-collapsible <?php echo $has_caption ? 'is-open' : ''; ?>">
          <button type="button" class="fb-collapsible__toggle">
            <span class="dashicons dashicons-editor-textcolor"></span>
            テキストオーバーレイ
            <span class="fb-collapsible__arrow dashicons <?php echo $has_caption ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2'; ?>"></span>
          </button>
          <div class="fb-collapsible__body" <?php echo $has_caption ? '' : 'style="display:none;"'; ?>>
            <div class="fb-field-row">
              <div class="fb-field-group">
                <label>見出しテキスト</label>
                <input type="text" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][caption_heading]" value="<?php echo esc_attr($caption_heading); ?>" class="widefat" placeholder="バナー上に表示する見出し">
              </div>
              <div class="fb-field-group">
                <label>テキスト位置</label>
                <select name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][caption_position]">
                  <option value="center" <?php selected($caption_position, 'center');        ?>>中央</option>
                  <option value="top-left" <?php selected($caption_position, 'top-left');      ?>>左上</option>
                  <option value="top-center" <?php selected($caption_position, 'top-center');    ?>>中央上</option>
                  <option value="top-right" <?php selected($caption_position, 'top-right');     ?>>右上</option>
                  <option value="bottom-left" <?php selected($caption_position, 'bottom-left');   ?>>左下</option>
                  <option value="bottom-center" <?php selected($caption_position, 'bottom-center'); ?>>中央下</option>
                  <option value="bottom-right" <?php selected($caption_position, 'bottom-right');  ?>>右下</option>
                </select>
              </div>
            </div>
            <div class="fb-field-group">
              <label>本文テキスト</label>
              <textarea name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][caption_body]" class="widefat" rows="2" placeholder="説明文（任意）"><?php echo esc_textarea($caption_body); ?></textarea>
            </div>
            <div class="fb-field-row">
              <div class="fb-field-group">
                <label>ボタンテキスト</label>
                <input type="text" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][caption_btn_text]" value="<?php echo esc_attr($caption_btn_text); ?>" class="widefat" placeholder="詳しくはこちら">
              </div>
              <div class="fb-field-group">
                <label>ボタンURL <small>（空欄ならバナーリンクを使用）</small></label>
                <input type="text" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][caption_btn_url]" value="<?php echo esc_attr($caption_btn_url); ?>" class="widefat" placeholder="別URLを指定する場合のみ入力">
              </div>
            </div>
          </div>
        </div>

        <div class="fb-collapsible <?php echo $has_schedule ? 'is-open' : ''; ?>">
          <button type="button" class="fb-collapsible__toggle">
            <span class="dashicons dashicons-calendar-alt"></span>
            表示スケジュール
            <span class="fb-collapsible__arrow dashicons <?php echo $has_schedule ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2'; ?>"></span>
          </button>
          <div class="fb-collapsible__body" <?php echo $has_schedule ? '' : 'style="display:none;"'; ?>>
            <div class="fb-field-row">
              <div class="fb-field-group">
                <label>表示開始日時 <small>（空欄なら即時表示）</small></label>
                <input type="datetime-local" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][schedule_start]" value="<?php echo esc_attr($schedule_start); ?>" class="widefat">
              </div>
              <div class="fb-field-group">
                <label>表示終了日時 <small>（空欄なら無期限）</small></label>
                <input type="datetime-local" name="fb_data[<?php echo $n; ?>][items][<?php echo $i; ?>][schedule_end]" value="<?php echo esc_attr($schedule_end); ?>" class="widefat">
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
<?php
  }

  /* ------------------------------------------------------------------ */
  /*  保存処理                                                             */
  /* ------------------------------------------------------------------ */

  public function save_data($post_id)
  {
    if (! isset($_POST['fb_nonce']) || ! wp_verify_nonce($_POST['fb_nonce'], 'fb_save_data')) {
      return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }
    if (! current_user_can('edit_post', $post_id)) {
      return;
    }

    // コンテナ幅を保存
    if (isset($_POST['fb_container_width'])) {
      $cw = min(2560, max(320, intval($_POST['fb_container_width'])));
      update_post_meta($post_id, '_flex_banner_container_width', $cw);
    }
    if (isset($_POST['fb_container_padding_pc'])) {
      $cpc = min(300, max(0, intval($_POST['fb_container_padding_pc'])));
      update_post_meta($post_id, '_flex_banner_container_padding_pc', $cpc);
    }
    if (isset($_POST['fb_container_padding_tab'])) {
      $cpt = min(200, max(0, intval($_POST['fb_container_padding_tab'])));
      update_post_meta($post_id, '_flex_banner_container_padding_tab', $cpt);
    }
    if (isset($_POST['fb_container_padding_sp'])) {
      $cps = min(150, max(0, intval($_POST['fb_container_padding_sp'])));
      update_post_meta($post_id, '_flex_banner_container_padding_sp', $cps);
    }

    $allowed_modes     = array('grid', 'slider');
    $allowed_anims     = array('slide', 'fade');
    $allowed_positions = array('center', 'top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right');

    if (isset($_POST['fb_data'])) {
      $raw   = $_POST['fb_data'];
      $clean = array();

      foreach ($raw as $row_key => $row) {
        $mode = (isset($row['mode']) && in_array($row['mode'], $allowed_modes, true)) ? $row['mode'] : 'grid';
        $anim = (isset($row['slider_animation']) && in_array($row['slider_animation'], $allowed_anims, true)) ? $row['slider_animation'] : 'slide';

        $clean_row = array(
          'mode'             => $mode,
          'cols_pc'          => min(4, max(1, intval(isset($row['cols_pc'])       ? $row['cols_pc']       : 1))),
          'cols_sp'          => min(2, max(1, intval(isset($row['cols_sp'])       ? $row['cols_sp']       : 1))),
          'gap_pc'           => intval(isset($row['gap_pc'])                      ? $row['gap_pc']        : 12),
          'gap_sp'           => intval(isset($row['gap_sp'])                      ? $row['gap_sp']        : 10),
          'slider_animation' => $anim,
          'slider_autoplay'  => isset($row['slider_autoplay'])                    ? 1 : 0,
          'slider_interval'  => min(60, max(1, intval(isset($row['slider_interval']) ? $row['slider_interval'] : 5))),
          'slider_arrows'    => isset($row['slider_arrows'])                      ? 1 : 0,
          'slider_dots'      => isset($row['slider_dots'])                        ? 1 : 0,
          'items'            => array(),
        );

        if (! empty($row['items']) && is_array($row['items'])) {
          foreach ($row['items'] as $item_key => $item) {
            $pos = (isset($item['caption_position']) && in_array($item['caption_position'], $allowed_positions, true)) ? $item['caption_position'] : 'center';
            $clean_item = array(
              'img_pc'           => intval(isset($item['img_pc'])           ? $item['img_pc']           : 0),
              'img_sp'           => intval(isset($item['img_sp'])           ? $item['img_sp']           : 0),
              'url'              => isset($item['url'])                     ? esc_url_raw(trim($item['url'])) : '',
              'target_blank'     => isset($item['target_blank'])            ? 1 : 0,
              'schedule_start'   => sanitize_text_field(isset($item['schedule_start'])   ? $item['schedule_start']   : ''),
              'schedule_end'     => sanitize_text_field(isset($item['schedule_end'])     ? $item['schedule_end']     : ''),
              'caption_heading'  => sanitize_text_field(isset($item['caption_heading'])  ? $item['caption_heading']  : ''),
              'caption_body'     => sanitize_textarea_field(isset($item['caption_body']) ? $item['caption_body']     : ''),
              'caption_position' => $pos,
              'caption_btn_text' => sanitize_text_field(isset($item['caption_btn_text']) ? $item['caption_btn_text'] : ''),
              'caption_btn_url'  => isset($item['caption_btn_url'])         ? esc_url_raw(trim($item['caption_btn_url'])) : '',
            );
            if ($clean_item['img_pc'] || $clean_item['img_sp']) {
              $clean_row['items'][$item_key] = $clean_item;
            }
          }
        }

        $clean[$row_key] = $clean_row;
      }

      update_post_meta($post_id, '_flex_banner_data', $clean);
    } else {
      delete_post_meta($post_id, '_flex_banner_data');
    }
  }
}

new Flex_Banner_Meta_Box();
