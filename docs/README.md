# 過去の実装ドキュメント

このディレクトリには、過去の作業ごとに作成した `task.md`、`implementation_plan.md`、`walkthrough.md` を保存しています。

現行仕様や編集ルールの正本は、リポジトリ直下の [AGENTS.md](../AGENTS.md)、[README.md](../README.md)、[CONTRIBUTING.md](../CONTRIBUTING.md) です。過去ドキュメントに記載された古いリポジトリURL、ローカルパス、Private運用の説明は、現在の設定を示すものではありません。

## 主な記録

| ディレクトリ | 内容 |
| --- | --- |
| `flex_banner_plugin` | 初期設計と基本機能 |
| `major_update_flex_banner` | グリッド、スライダー、管理画面機能の拡張 |
| `add_usage_guide_v231` | 管理画面の使い方ガイド |
| `implement_proper_admin_permissions_v240` | 専用権限の導入 |
| `fix_relative_path_urls_v241` | 相対URLの解決修正 |
| `fix_puc_integration` | PUC v5系の初期統合 |
| `fix_update_checker_v205` | 更新チェッカー設定の修正 |
| `puc_private_repo_v206` | 過去のPrivateリポジトリ認証対応 |
| `version_bump_*` / `minor_update_*` | バージョン更新の記録 |

## 作業記録の読み方

- `task.md`: 依頼内容と完了条件
- `implementation_plan.md`: 実装方針と対象ファイル
- `walkthrough.md`: 実施結果と確認内容

新しい機能や修正を追加するときは、必要に応じて新しい作業ディレクトリを作り、完了後にルートの [CHANGELOG.md](../CHANGELOG.md) へ利用者向けの要点を追記します。

