# 実装計画: デバイス別（PC/TB/SP）サイド余白設定の拡張 (v2.1.0)

## 概要
レスポンシブデザインをより緻密に制御するため、PC・タブレット・スマートフォンの3段階で左右余白（サイドパディング）を設定可能にします。

## ターゲット・ブレイクポイント
- **PC**: 1025px 以上
- **タブレット (TB)**: 768px 〜 1024px
- **スマートフォン (SP)**: 767px 以下

## 変更内容

### 1. メタボックスの拡張 (`includes/class-fb-meta-box.php`)
- コンテナ設定ボックスに「タブレット側余白 (左右)」の入力項目を追加。
- メタキー `_flex_banner_container_padding_tab` を使用。
- 保存処理にタブレット用余白の保存ロジックを追加（0〜150px程度を許容）。

### 2. フロントエンドの出力 (`includes/class-fb-shortcode.php`)
- `@media` クエリを3段階で出力。
  - 基本設定 (PC): `padding-left/right` にPC用余白を適用。
  - `max-width: 1024px` (Tablet): `padding-left/right` にタブレット用余白を適用。
  - `max-width: 767px` (Smartphone): `padding-left/right` にスマホ用余白を適用。

### 3. 管理画面JSの同期 (`assets/js/admin.js`)
- `updatePreviewStyles` の入力監視対象にタブレット用入力を追加。
- 推奨サイズ計算への影響を確認。
  - PC推奨サイズは既存通り「PC用余白」を使用。
  - SP推奨サイズは既存通り「SP用余白」を使用。
  - タブレットは中間値のため、ガイド表示はPC/SPのままとし、必要に応じて計算ロジックを整理。

### 4. バージョンアップ
- `flex-banner.php` のバージョンを `2.1.0` に更新。
