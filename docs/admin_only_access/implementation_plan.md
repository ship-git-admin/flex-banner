# 実装計画：ロール制限の導入とバージョン 2.2.0 公開

`flex-banner` プラグインにおいて、管理者（Administer/Administrator）のみがバナーの管理を行えるように、カスタム投稿タイプの権限設定を修正し、最新バージョンとして公開します。

## 変更内容

### 投稿タイプ登録の修正
カスタム投稿タイプ `flex_banner_group` の権限を `manage_options` に制限します。これにより、通常「管理者」ロールのみがこのメニューを表示・操作できるようになります。

### バージョン情報
ファイル名: `flex-banner.php`
現在のバージョン: `2.1.0`
新しいバージョン: `2.2.0`

## 影響範囲
- 管理画面の「フレキシブルバナー」メニューが、`manage_options` 権限を持たないユーザーには非表示になります。
- フロントエンドでのショートコード実行には影響ありません（`publicly_queryable` は既に `false` であり、内部的にのみ使用されています）。

## 修正対象ファイル

### [Component Name]

#### [MODIFY] [class-fb-post-type.php](file:///Users/satoukanouyume/Library/CloudStorage/Box-Box/flex-banner/includes/class-fb-post-type.php)
- `register_post_type` の引数に `capabilities` 配列を追加し、すべて `manage_options` に設定します。

#### [MODIFY] [flex-banner.php](file:///Users/satoukanouyume/Library/CloudStorage/Box-Box/flex-banner/flex-banner.php)
- `Version` を `2.1.0` から `2.2.0` に更新します。

## 検証プラン

### 自動テスト
- なし（WordPress環境が必要なため）

### 手動検証
- ファイルの構文チェック
- Gitのコミット内容の確認
- `git push` 後に GitHub 上で変更が反映されていることを確認

## Git公開手順
1. すべての変更をステージ
2. コミット（メッセージ: `feat: restrict management to administrator role`）
3. リモート `main` ブランチにプッシュ
