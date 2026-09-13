# フレキシブルバナーグループ 編集ガイド

このファイルは、`flex-banner` プラグインを編集するAIエージェントと開発者向けの作業ルールです。現行仕様の案内は [README.md](README.md)、変更履歴は [CHANGELOG.md](CHANGELOG.md) を参照してください。

## 現行の基準

- 現行プラグインバージョン: `2.4.7`
- 更新チェッカー: Yahnis Elsts `plugin-update-checker` 公式 `v5.7`
- 更新元: `https://github.com/ship-git-admin/flex-banner`
- 安定ブランチ: `main`
- ショートコード: `[flex_banner id="POST_ID"]`

## 編集範囲

- プラグイン本体の初期化・更新チェッカー設定は `flex-banner.php` に置く。
- カスタム投稿タイプは `includes/class-fb-post-type.php`、編集画面と保存処理は `includes/class-fb-meta-box.php`、フロント出力は `includes/class-fb-shortcode.php` に置く。
- 管理画面の動作は `assets/js/admin.js`、フロントのスライダー動作は `assets/js/frontend.js` に置く。
- 見た目の変更は `assets/css/` の既存クラス体系に合わせる。
- `lib/plugin-update-checker/` は外部ライブラリの同梱領域。手作業で改変せず、公式リリース単位で更新する。
- `docs/` 配下は過去作業の記録であり、現行仕様の正本ではない。まずこのファイルとルートのREADMEを確認する。

## 必ず守ること

1. トークン、パスワード、秘密鍵、Cookie、個人用アクセストークンをソース、Markdown、設定ファイル、コミットに書かない。
2. GitHub更新用の認証が必要なPrivate環境では、`FLEX_BANNER_GITHUB_TOKEN` を `wp-config.php` などの外部設定から渡す。値そのものはリポジトリへ保存しない。現在の公開リポジトリでは通常この設定は不要。
3. 保存処理を変更するときは、nonce、autosave判定、`current_user_can('edit_post', $post_id)`、入力値のサニタイズを維持する。
4. 既存の投稿メタキーやショートコードの互換性を壊さない。変更が必要な場合は、移行方法と影響範囲をCHANGELOGまたは作業ドキュメントに記録する。
5. PUCの更新では、公式タグの内容をそのまま取り込み、`plugin-update-checker.php`、loader、名前空間、Composer設定の整合性を確認する。
6. 既存の未コミット変更や `.history/` の内容を、依頼なしに削除・整理しない。

## バージョン更新

機能・修正をリリースする場合は、必要に応じて次を同じバージョンへ揃える。

- `flex-banner.php` の `Version` ヘッダー
- `includes/class-fb-meta-box.php` の管理画面アセットバージョン
- `includes/class-fb-shortcode.php` のフロントアセットバージョン
- `CHANGELOG.md`
- Gitタグ `vX.Y.Z` とGitHub Release

Markdownだけの変更では、プラグインバージョンを上げない。

## 編集後の検証

最低限、次を実行する。

```sh
php -l flex-banner.php
for file in $(rg --files -g '*.php'); do php -l "$file"; done
git diff --check -- . ':(exclude)lib/plugin-update-checker'
```

画面変更時は、WordPress管理画面でバナーの新規作成・保存・複製・削除を確認し、フロント側でグリッド、スライダー、リンク、表示期間、レスポンシブ画像を確認する。

