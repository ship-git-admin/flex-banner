# フレキシブルバナーグループ

WordPress上で、画像バナーをグループ単位で管理・表示するプラグインです。グリッド表示とスライダー表示、PC/SP画像、テキストオーバーレイ、リンク、表示期間、コンテナ幅と余白を設定できます。

## 主な機能

- バナーグループの作成・並べ替え・複製
- セクションごとのグリッド / スライダー切り替え
- PC用・スマートフォン用画像の個別設定
- バナー全体またはキャプションボタンへのリンク設定
- 見出し・本文・ボタンのテキストオーバーレイ
- 表示開始日時・表示終了日時によるスケジュール制御
- PC、タブレット、スマートフォンのコンテナ幅・外側余白設定
- 管理者向けの専用編集権限

## 導入

1. このリポジトリを `wp-content/plugins/flex-banner/` に配置します。
2. WordPress管理画面の「プラグイン」から有効化します。
3. 管理画面の「フレキシブルバナー」からバナーグループを作成します。

同梱の更新チェッカーは公式 `plugin-update-checker` v5.7です。現在のGitHubリポジトリはPublicのため、通常はGitHubトークンの設定は必要ありません。

## 表示方法

バナーグループを保存すると、一覧画面と編集画面にショートコードが表示されます。固定ページや投稿に次の形式で貼り付けます。

```text
[flex_banner id="123"]
```

`123` は表示したいバナーグループの投稿IDに置き換えます。

## ディレクトリ構成

```text
flex-banner.php                 # 初期化、定数、更新チェッカー
includes/
  class-fb-post-type.php         # カスタム投稿タイプ
  class-fb-meta-box.php          # 管理画面、保存処理
  class-fb-shortcode.php         # ショートコード、フロント出力
assets/
  js/admin.js                    # 管理画面操作
  js/frontend.js                 # スライダー操作
  css/                           # 管理画面・フロント表示
lib/plugin-update-checker/       # 公式更新チェッカー v5.7
docs/                            # 過去の実装記録
```

編集時のルールと検証方法は [AGENTS.md](AGENTS.md)、協力開発の手順は [CONTRIBUTING.md](CONTRIBUTING.md) を確認してください。変更履歴は [CHANGELOG.md](CHANGELOG.md) にあります。

## Private環境での利用

Privateなフォークや社内ミラーを更新元にする場合だけ、`wp-config.php` 等でトークンを外部設定します。トークンをこのリポジトリへ記述しないでください。

```php
define('FLEX_BANNER_GITHUB_TOKEN', '（GitHubトークン）');
```

