# 実装計画: plugin-update-checker 復旧

## 現状の課題
- `flex-banner/lib/plugin-update-checker/` 内に `README.txt` しか存在せず、ライブラリ本体が欠落していた。
- `flex-banner.php` で `Puc_v4_Factory` を使用しているが、最新の PUC (v5) では名前空間が変更されている。

## 手順
1. **ライブラリ本体の取得**
   - GitHub の公式リポジトリから最新の PUC を `flex-banner/lib/plugin-update-checker/` に展開（完了済み）。
2. **プラグインメインファイルの修正**
   - `Puc_v4_Factory` を `YahnisElsts\PluginUpdateChecker\v5\PucFactory` に書き換え。
   - `Version` を `2.0.2` に更新。
3. **GitHub へのプッシュ**
   - 不足していたライブラリファイルを含めてコミット、プッシュ。

## 修正後のコードイメージ
```php
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/aurora-ship-sato/flex-banner/',
    __FILE__,
    'flex-banner'
);
```
