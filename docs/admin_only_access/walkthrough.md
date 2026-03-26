# 修正内容の確認：ロール制限の実装とバージョン 2.2.0 公開

管理者（Administrator）のみがバナーを管理できるように権限を制限し、バージョンを 2.2.0 に更新して Git に公開しました。

## 実施内容

### 1. ロール制限の実装
カスタム投稿タイプ `flex_banner_group` の権限設定を `post` から `manage_options` に変更しました。
これにより、WordPress の標準的な管理者権限を持つユーザーのみがメニューを表示・編集できるようになります。

- 修正ファイル: `includes/class-fb-post-type.php`
- 理由: 「Administer だけ一番上位のロールのみ表示されるように」という要件に基づき、最も上位の権限である `manage_options` を割り当てました。

### 2. バージョン更新とタグ付与
プラグインのメインファイルおよびアセット（JS/CSS）のバージョンを更新し、Git タグを付与しました。
- 修正ファイル: `flex-banner.php`, `includes/class-fb-meta-box.php`
- バージョン: `2.2.1`
- Git タグ: `v2.2.1`

### 3. Git 公開
以下の変更をリモートリポジトリ（`main` ブランチ）にプッシュしました。
- ロール制限の変更
- バージョンアップ（2.2.1）
- Git タグ（v2.2.1）の作成とプッシュ
- 今回の作業ドキュメント（`docs/admin_only_access`）

## 検証結果
- PHP 構文チェック (`php -l`) を実施し、エラーがないことを確認済み。
- Git のリモートリポジトリへのプッシュが完了。

## 公開リポジトリ
[aurora-ship-sato/flex-banner](https://github.com/aurora-ship-sato/flex-banner)
