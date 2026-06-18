# AIdealize — LINE LIFF（PHP）用リポジトリ

このリポジトリの**現役の役割は `liff/nursing` の PHP**（LINE LIFF：音声→SOAP記録など）。
**シンVPS**（PHP実行サーバー）にデプロイする。

- GitHub: `makita611/AIdealize`（branch: **main**）
- デプロイ: シンVPS（Cloudflare ではない。PHPを動かすため）

## ⚠️ 注意：LP/サイトHTMLは触らないこと

`lp/` `service/` `index.html` などのHTMLは **移行前の残骸**で、**本番 aidealize.com には反映されない**。
aidealize.com 本体は別リポジトリ **`makita611/aidealize-com`（ローカル `sites/aidealize`）** が
Cloudflare Pages で配信している。

| 作業内容 | 触るリポジトリ |
|---|---|
| サイト・LP・フォーム・GTM | `aidealize-com`（`sites/aidealize`） |
| **LINE LIFF の PHP** | **このリポジトリの `liff/`** |

（将来 liff 以外のHTML残骸は削除して、このリポジトリを LIFF 専用に整理する予定）
