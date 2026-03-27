# タスク: 権限管理の本格的な再構築 (v2.4.0)

## 概要
以前のバージョンで発生していた「管理者でもサイドバーからメニューが消える」問題を根本から防ぎつつ、要望通り「管理者（Administrator）のみ」にフレキシブルバナーを利用・表示させるため、正攻法となるカスタム権限（Capabilities）の割り当てロジックを実装します。

## ステップ
1. [x] `includes/class-fb-post-type.php` の修正
   - `capability_type` を `array('flex_banner', 'flex_banners')` に変更。
   - `map_meta_cap` を `true` に設定し、正確な権限マッピングを有効化。
2. [x] `flex-banner.php` へのメインロジック追加
   - 新しい権限（`edit_flex_banner` 等全14種）を定義。
   - `admin_init` 時に、指定バージョン未満であれば管理者（`administrator`）ロールにのみこの権限群を明示的に付与するアップグレードルーチンを追加。
3. [x] バージョンアップ
   - バージョンを 2.4.0 に更新。
4. [x] Git へのプッシュとタグ `v2.4.0` の発行。
