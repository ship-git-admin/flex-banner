# コントリビューションガイド

## 編集前

- [AGENTS.md](AGENTS.md) と [README.md](README.md) を確認する。
- 変更対象をPHP、JavaScript、CSS、同梱ライブラリのどこに置くべきか判断する。
- `docs/` の記録は過去の経緯として参照し、現行仕様と異なるURL・構成・設定をそのまま再利用しない。
- 認証情報や個人情報を作業ファイル・コミット・Release本文へ入れない。

## 実装ルール

- PHPの入力値は既存のnonce、権限確認、サニタイズ処理を維持する。
- 管理画面の動作は `assets/js/admin.js`、フロントの動作は `assets/js/frontend.js` に実装する。
- CSSは既存の `fb-` クラスとBEM風の命名に合わせる。
- 新しい設定値やメタキーを追加する場合は、初期値、保存処理、表示処理、既存データとの互換性を確認する。
- `lib/plugin-update-checker/` は公式リリースの内容を取り込む。機能追加や個別修正を直接混在させない。

## 検証

```sh
php -l flex-banner.php
for file in $(rg --files -g '*.php'); do php -l "$file"; done
git diff --check -- . ':(exclude)lib/plugin-update-checker'
```

WordPress上では、少なくとも次を確認します。

- バナーグループの新規作成、保存、再編集
- セクションとバナーの並べ替え、複製、削除
- グリッドとスライダーの表示
- PC/SP画像、リンク、キャプション、表示期間
- 管理画面以外のページへの影響

## バージョンとリリース

機能・修正をリリースするときは、`flex-banner.php` のプラグインバージョン、管理画面・フロントアセットのバージョン、[CHANGELOG.md](CHANGELOG.md) を揃えます。GitHubでは `vX.Y.Z` タグを作成し、Pre-releaseではないReleaseとして公開します。

同梱PUCだけを更新する場合も、loaderと名前空間が対応していることを確認します。公開リポジトリの通常運用では認証トークンを設定せず、Private環境でのみ `FLEX_BANNER_GITHUB_TOKEN` を外部設定から渡します。

## コミット前

- `git diff` で意図しない変更がないことを確認する。
- `git status` で秘密情報や一時ファイルが追加されていないことを確認する。
- 変更内容を [CHANGELOG.md](CHANGELOG.md) に追記する。
- リリースを伴う場合は、タグとReleaseのバージョンが一致していることを確認する。

