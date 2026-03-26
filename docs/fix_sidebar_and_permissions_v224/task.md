# タスク: サイドバーメニューの復旧および管理者権限の厳格化 (v2.2.4)

## 概要
前回の修正 (v2.2.3) で一部の権限マッピングが不足していたため、管理画面のサイドバーからメニュー自体が消えてしまう問題が発生しました。これを修正し、管理者（Administrator）のみが確実に操作できる状態でメニューを復旧させます。

## ステップ
1. [x] `includes/class-fb-post-type.php` の修正
   - `capability_type` を `post` に戻し、WordPress標準のマッピングを利用
   - `capabilities` 配列に、`edit_private_posts` や `delete_others_posts` など、サイドバー表示や権限チェックに漏れていた項目を全て追加し `$manage_cap` に割り当て
2. [x] バージョン番号の更新
   - `flex-banner.php` を 2.2.4 に更新
   - 各ファイルのアセットバージョンを 2.2.4 に同期
3. [x] Git へのプッシュとタグ `v2.2.4` の発行
