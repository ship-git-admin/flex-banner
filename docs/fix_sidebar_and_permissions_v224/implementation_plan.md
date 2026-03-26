# 実装計画: サイドバー表示の修正と権限の完全マッピング (v2.2.4)

## 1. 現状の課題
- 前回の修正で `edit_post`, `read_post`, `delete_post` などの主要なメタ権限は修正されましたが、`capability_type` が独自の設定になっていたため、WordPressがメニュー表示の可否を判定する際に使用する他の権限（`edit_private_posts` 等）が不足していました。
- その結果、管理者の権限チェックを通過できず、サイドバーから「フレキシブルバナー」のメニューが消滅していました。

## 2. 修正方針
- `capability_type` を標準の `post` に戻すことで、複雑な接頭辞ベースの自動生成を避け、予測可能な権限名を使用します。
- `capabilities` 配列において、サイドバー表示に関係する全ての権限項目（14項目）を明示的に `$manage_cap` (`manage_options`) にマッピングします。これには以下が含まれます：
  - `edit_private_posts`
  - `edit_published_posts`
  - `delete_private_posts`
  - `delete_published_posts`
  - `delete_others_posts`
- これにより、「管理者ロールのみが編集・操作できる」という要件を維持しつつ、サイドバーメニューを確実に表示させます。

## 3. 修正対象ファイル
- `includes/class-fb-post-type.php`: 権限マッピングを網羅的に修正。
- `flex-banner.php`: バージョン 2.2.4 へ更新。
- 各アセット読み込み箇所: バージョン同期。

## 4. リリース
- `git commit` & `git tag v2.2.4`
- リポジトリへのプッシュ。
