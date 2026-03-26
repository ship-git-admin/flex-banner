# 実装計画: Git連携

## 現状の課題
- ローカルディレクトリが空、またはGit管理されていない。
- 指定されたGitHubリポジトリ（`https://github.com/aurora-ship-sato/flex-banner`）と同期する必要がある。

## 手順
1. **GitHubリポジトリの構造確認**
   - リポジトリ直下に `flex-banner` フォルダが存在することを確認。
2. **Git初期化とリモート設定**
   - `git init` でローカルリポジトリを初期化。
   - `git remote add origin` でGitHubリポジトリを接続。
3. **コードの取得**
   - `git pull origin main` を実行し、すべてのファイルを取得。
4. **環境整備**
   - 調査中に作成した一時フォルダ（`temp_repo`, `test_inspect`）を削除。
   - `git status` がクリーンであることを確認。
5. **ドキュメント作成**
   - `docs/git_integration` フォルダに作業内容を記録。

## リスク・注意点
- **ディレクトリ階層**: リポジトリ直下に `flex-banner` フォルダがあるため、ローカルでも1段ネストした状態になる。利用者の利便性を考慮しつつ、リポジトリ構成を維持する。
- **所有権エラー**: Windows/Box環境特有の `dubious ownership` エラーが発生する可能性があるため、`safe.directory` 設定を適用する。
