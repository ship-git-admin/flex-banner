# 修正内容の確認 (Walkthrough)

## 修正のポイント
### 1. 権限設定の修正 (`includes/class-fb-post-type.php`)
以下のように権限を修正しました。
```php
// 修正前
'edit_post' => 'edit_flex_banner',
'read_post' => 'read_flex_banner',
'delete_post' => 'delete_flex_banner',

// 修正後
'edit_post' => $manage_cap,   // manage_options
'read_post' => $manage_cap,   // manage_options
'delete_post' => $manage_cap, // manage_options
```
これにより、管理画面の「タイトル」列にカーソルを合わせた際に表示される「編集」「ゴミ箱」などのリンクが復活します。

### 2. バージョン更新
- **プラグインバージョン**: `2.2.2` → `2.2.3`
- **アセットバージョン**: 全て `2.2.3` に統一し、古いキャッシュが残らないようにしました。

## リリースの完了
GitHub リポジトリ（`aurora-ship-sato/flex-banner`）へのプッシュ、およびビルド済みバージョンのタグ発行を完了しました。
- **Branch**: `main`
- **Tag**: `v2.2.3`

これで管理画面からの編集が再び可能になります。
