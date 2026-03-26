# 実装計画: 管理画面権限の復旧 (v2.2.3)

## 1. 現状の課題
- `register_post_type` の `capabilities` 配列において、`edit_post` などのメタ権限に `edit_flex_banner` という独自の文字列を割り当てていた。
- 当該文字列が各ロールに付与されておらず、また `map_meta_cap => true` による適切なマッピングが行われていなかった（あるいは命名による競合）ため、管理画面の一覧で操作メニューが表示されない状態となっていた。

## 2. 修正方針
- `capabilities` のメタ権限（`edit_post`, `read_post`, `delete_post`）を、定数 `$manage_cap` (標準: `manage_options`) に明示的に紐付ける。
- これにより、`administrator` ロールなど `manage_options` を持つユーザーが確実に編集権限を保持するようにする。

## 3. 修正対象ファイル
- `includes/class-fb-post-type.php`: 権限マッピングを修正。
- `flex-banner.php`: プラグインバージョンを `2.2.3` に更新。
- `includes/class-fb-meta-box.php`: 管理画面用 JS/CSS のキャッシュバスティングバージョンを更新。
- `includes/class-fb-shortcode.php`: フロントエンド用 JS/CSS のバージョンを更新。

## 4. リリース手順
1. ソースコードの修正。
2. `git commit` による変更の記録。
3. `git tag v2.2.3` の作成。
4. `git push origin main` および `git push origin v2.2.3` の実行。
