jQuery(document).ready(function ($) {

  // fbAdminData.containerWidth をJS側で使う（wp_localize_scriptで渡されてくる）
  var globalContainerWidth = (typeof fbAdminData !== 'undefined') ? parseInt(fbAdminData.containerWidth) || 944 : 944;

  var $container = $('#fb-rows-container');

  /* ================================================================== */
  /*  セクション（行）ソータブル                                              */
  /* ================================================================== */
  $container.sortable({
    handle: '.fb-drag-handle',
    placeholder: 'ui-state-highlight',
    axis: 'y',
    update: function () {
      /* 必要に応じてインデックス更新 */
    }
  });

  /* ================================================================== */
  /*  アイテムソータブル                                                     */
  /* ================================================================== */
  function initItemSortable($row) {
    if (!$row.length) return;
    $row.find('.fb-items-wrapper').sortable({
      handle: '.fb-item-drag-handle',
      placeholder: 'ui-state-highlight',
      connectWith: false,
      update: function () {
        updateItemNumbers($row);
      }
    });
  }

  /* ================================================================== */
  /*  アイテム番号更新                                                       */
  /* ================================================================== */
  function updateItemNumbers($row) {
    $row.find('.fb-item').each(function (index) {
      $(this).find('.fb-item-num').text(index + 1);
    });
  }

  /* ================================================================== */
  /*  推奨サイズ計算（コンテナ幅を考慮）                                       */
  /* ================================================================== */
  function getContainerWidth() {
    // コンテナ幅入力フォームから動的取得（変更している場合は最新値を使う）
    var inputVal = parseInt($('#fb-container-width-input').val());
    return (!isNaN(inputVal) && inputVal >= 320) ? inputVal : globalContainerWidth;
  }

  function updatePreviewStyles($row) {
    if (!$row || !$row.length) return;

    var mode = $row.data('mode') || 'grid';
    var totalWidth = getContainerWidth();
    var paddingPC = parseInt($('#fb-container-padding-pc-input').val()) || 0;
    var paddingSP = parseInt($('#fb-container-padding-sp-input').val()) || 0;

    if (mode === 'slider') {
      // スライダーモードは推奨サイズ = コンテナ内側の有効幅
      var effectiveCW = totalWidth - (paddingPC * 2);
      $row.find('.fb-guide-pc').html('PC: 100% (推奨: <strong>' + effectiveCW + 'px</strong>)');
      $row.find('.fb-guide-sp').html('スマホ: <strong>100%</strong>');
      return;
    }

    var cols = parseInt($row.find('.fb-cols-pc').val()) || 1;
    var colsSP = parseInt($row.find('.fb-cols-sp').val()) || 1;
    var gapPC = parseInt($row.find('.fb-gap-pc').val());
    var gapSP = parseInt($row.find('.fb-gap-sp').val());
    if (isNaN(gapPC)) gapPC = 12;
    if (isNaN(gapSP)) gapSP = 10;

    // PC推奨サイズ
    var effectiveWidthPC = totalWidth - (paddingPC * 2);
    var itemWidthPC = (effectiveWidthPC - (gapPC * (cols - 1))) / cols;
    var pcText;
    if (cols === 1) {
      pcText = '100% (推奨: <strong>' + Math.floor(effectiveWidthPC) + 'px</strong>)';
    } else {
      pcText = Math.floor(100 / cols) + '% (推奨幅: <strong>' + Math.floor(itemWidthPC) + 'px</strong>)';
    }
    $row.find('.fb-guide-pc').html('PC: ' + pcText);

    // SP推奨サイズ
    var effectiveWidthSP = 375 - (paddingSP * 2); // 標準的なモバイル幅375pxを基準に算出
    var itemWidthSP = (effectiveWidthSP - (gapSP * (colsSP - 1))) / colsSP;
    var spText = (colsSP === 1) ? '100%' : '50%';
    $row.find('.fb-guide-sp').html('スマホ: ' + spText + ' (推奨幅: <strong>' + Math.floor(itemWidthSP) + 'px</strong>)');

    // 管理画面プレビュー用のCSS変数
    $row.css('--fb-preview-cols', cols);
    $row.css('--fb-preview-gap', gapPC + 'px');
  }

  /* ================================================================== */
  /*  既存の行を初期化                                                       */
  /* ================================================================== */
  $('.fb-row').each(function () {
    var $row = $(this);
    initItemSortable($row);
    updatePreviewStyles($row);
    updateItemNumbers($row);
  });

  /* ================================================================== */
  /*  コンテナ設定変更時：全セクションの推奨サイズを再計算                          */
  /* ================================================================== */
  $(document).on('input change', '#fb-container-width-input, #fb-container-padding-pc-input, #fb-container-padding-sp-input', function () {
    $container.find('.fb-row').each(function () {
      updatePreviewStyles($(this));
    });
  });

  /* ================================================================== */
  /*  表示モード切り替え（グリッド / スライダー）                               */
  /* ================================================================== */
  $container.on('click', '.fb-mode-btn', function () {
    var $btn = $(this);
    var $row = $btn.closest('.fb-row');
    var mode = $btn.data('mode');

    $btn.siblings('.fb-mode-btn').removeClass('is-active');
    $btn.addClass('is-active');
    $row.find('.fb-row-mode').val(mode);
    $row.data('mode', mode);

    if (mode === 'slider') {
      $row.find('.fb-grid-settings').addClass('fb-hidden');
      $row.find('.fb-slider-settings').removeClass('fb-hidden');
    } else {
      $row.find('.fb-slider-settings').addClass('fb-hidden');
      $row.find('.fb-grid-settings').removeClass('fb-hidden');
    }
    updatePreviewStyles($row);
  });

  /* ================================================================== */
  /*  グリッド設定変更時に推奨サイズ更新                                        */
  /* ================================================================== */
  $container.on('change input', '.fb-cols-pc, .fb-cols-sp, .fb-gap-pc, .fb-gap-sp', function () {
    updatePreviewStyles($(this).closest('.fb-row'));
  });

  /* ================================================================== */
  /*  コラプシブル（テキストオーバーレイ / スケジュール）トグル                   */
  /* ================================================================== */
  $container.on('click', '.fb-collapsible__toggle', function () {
    var $toggle = $(this);
    var $coll = $toggle.closest('.fb-collapsible');
    var $body = $coll.find('.fb-collapsible__body');
    var $arrow = $toggle.find('.fb-collapsible__arrow');
    var isOpen = $coll.hasClass('is-open');

    if (isOpen) {
      $body.slideUp(180);
      $coll.removeClass('is-open');
      $arrow.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
    } else {
      $body.slideDown(180);
      $coll.addClass('is-open');
      $arrow.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
    }
  });

  /* ================================================================== */
  /*  セクション追加                                                         */
  /* ================================================================== */
  $('.fb-add-row').on('click', function () {
    var template = $('#tmpl-fb-row').html();
    var rowId = 'row_' + Date.now();
    var html = template.replace(/\{\{row_index\}\}/g, rowId);
    var $row = $(html);

    $container.append($row);
    initItemSortable($row);
    updatePreviewStyles($row);
    updateItemNumbers($row);
    $row[0].scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  });

  /* ================================================================== */
  /*  セクション削除                                                         */
  /* ================================================================== */
  $container.on('click', '.fb-remove-row__btn', function () {
    if (confirm('このセクションを削除してもよろしいですか？')) {
      $(this).closest('.fb-row').remove();
    }
  });

  /* ================================================================== */
  /*  セクション複製                                                         */
  /* ================================================================== */
  $container.on('click', '.fb-duplicate-row__btn', function () {
    var $origRow = $(this).closest('.fb-row');
    var $clone = $origRow.clone(false);
    var newRowId = 'row_' + Date.now();

    // name属性のインデックスを新IDに差し替え
    var origIndex = $origRow.data('index');
    $clone.attr('data-index', newRowId);
    $clone.find('[name]').each(function () {
      var name = $(this).attr('name');
      $(this).attr('name', name.replace(
        new RegExp('\\[' + escapeRegex(origIndex) + '\\]', 'g'),
        '[' + newRowId + ']'
      ));
    });

    // アイテムのインデックスも更新
    $clone.find('.fb-item').each(function (idx) {
      var newItemId = 'item_' + Date.now() + '_' + idx;
      var $item = $(this);
      $item.attr('data-index', newItemId);
      $item.find('[name]').each(function () {
        var name = $(this).attr('name');
        // itemsの部分を差し替え
        $(this).attr('name', name.replace(/\[items\]\[[^\]]+\]/, '[items][' + newItemId + ']'));
      });
    });

    $origRow.after($clone);
    initItemSortable($clone);
    updatePreviewStyles($clone);
    updateItemNumbers($clone);
  });

  /* ================================================================== */
  /*  バナー追加                                                            */
  /* ================================================================== */
  $container.on('click', '.fb-add-item', function () {
    var $row = $(this).closest('.fb-row');
    var rowId = $row.data('index');
    var itemId = 'item_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
    var template = $('#tmpl-fb-item').html();
    var html = template
      .replace(/\{\{row_index\}\}/g, rowId)
      .replace(/\{\{item_index\}\}/g, itemId);

    var $newItem = $(html);
    $row.find('.fb-items-wrapper').append($newItem);
    updatePreviewStyles($row);
    updateItemNumbers($row);
  });

  /* ================================================================== */
  /*  バナー削除                                                            */
  /* ================================================================== */
  $container.on('click', '.fb-remove-item__btn', function () {
    if (confirm('この項目を削除しますか？')) {
      var $row = $(this).closest('.fb-row');
      $(this).closest('.fb-item').remove();
      updatePreviewStyles($row);
      updateItemNumbers($row);
    }
  });

  /* ================================================================== */
  /*  バナー複製                                                            */
  /* ================================================================== */
  $container.on('click', '.fb-duplicate-item__btn', function () {
    var $origItem = $(this).closest('.fb-item');
    var $row = $origItem.closest('.fb-row');
    var origIndex = $origItem.data('index');
    var newItemId = 'item_' + Date.now() + '_' + Math.floor(Math.random() * 10000);

    var $clone = $origItem.clone(false);
    $clone.attr('data-index', newItemId);
    $clone.find('[name]').each(function () {
      var name = $(this).attr('name');
      $(this).attr('name', name.replace(
        new RegExp('\\[items\\]\\[' + escapeRegex(origIndex) + '\\]', 'g'),
        '[items][' + newItemId + ']'
      ));
    });
    // コラプシブルを閉じた状態で複製
    $clone.find('.fb-collapsible').removeClass('is-open');
    $clone.find('.fb-collapsible__body').hide();
    $clone.find('.fb-collapsible__arrow')
      .removeClass('dashicons-arrow-up-alt2')
      .addClass('dashicons-arrow-down-alt2');

    $origItem.after($clone);
    updateItemNumbers($row);
  });

  /* ================================================================== */
  /*  URLプレビュー更新                                                      */
  /* ================================================================== */
  $container.on('input', '.fb-url-input', function () {
    var val = $(this).val();
    var $preview = $(this).closest('.fb-item').find('.fb-item-url-preview');
    if (val) {
      $preview.text(val).removeClass('is-empty');
    } else {
      $preview.text('(リンク未設定)').addClass('is-empty');
    }
  });

  /* ================================================================== */
  /*  テキストオーバーレイ入力時：バッジ表示切り替え                              */
  /* ================================================================== */
  $container.on('input change', '.fb-collapsible__body input[type="text"], .fb-collapsible__body textarea', function () {
    var $item = $(this).closest('.fb-item');
    var $captionArea = $item.find('.fb-collapsible').first().find('.fb-collapsible__body');
    var hasCap = false;
    $captionArea.find('input[type="text"], textarea').each(function () {
      if ($(this).val().trim()) {
        hasCap = true;
        return false;
      }
    });
    var $badge = $item.find('.fb-item-badge--text');
    if (hasCap) {
      if (!$badge.length) {
        $item.find('.fb-item-title-wrap').append('<span class="fb-item-badge fb-item-badge--text" title="テキストオーバーレイあり">T</span>');
      }
    } else {
      $badge.remove();
    }
  });

  /* ================================================================== */
  /*  メディアアップローダー                                                   */
  /* ================================================================== */
  $container.on('click', '.fb-upload-btn', function (e) {
    e.preventDefault();
    var $uploader = $(this).closest('.fb-image-uploader');

    var frame = wp.media({
      title: '画像を選択',
      button: {
        text: 'この画像を使用する'
      },
      multiple: false
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      $uploader.find('.fb-img-id').val(attachment.id);
      var thumbUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
      $uploader.find('.fb-image-preview').html('<img src="' + thumbUrl + '">');
      $uploader.find('.fb-upload-btn').text('変更');
      $uploader.find('.fb-remove-img-btn').show();
    });

    frame.open();
  });

  /* ================================================================== */
  /*  画像削除                                                              */
  /* ================================================================== */
  $container.on('click', '.fb-remove-img-btn', function (e) {
    e.preventDefault();
    var $wrap = $(this).closest('.fb-image-uploader');
    $wrap.find('.fb-img-id').val('');
    $wrap.find('.fb-image-preview').html('<span class="dashicons dashicons-format-image"></span>');
    $wrap.find('.fb-upload-btn').text('選択');
    $(this).hide();
  });

  /* ================================================================== */
  /*  ショートコードコピー                                                     */
  /* ================================================================== */
  $(document).on('click', '.fb-copy-shortcode', function () {
    var targetId = $(this).data('target');
    var $text = $('#' + targetId);
    $text.select();
    try {
      document.execCommand('copy');
    } catch (e) {}
    var $btn = $(this);
    var orig = $btn.text();
    $btn.text('コピー完了！').addClass('button-primary');
    setTimeout(function () {
      $btn.text(orig).removeClass('button-primary');
    }, 2000);
  });

  /* ================================================================== */
  /*  ユーティリティ                                                          */
  /* ================================================================== */
  function escapeRegex(str) {
    return String(str).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

});