# AIdealize — LINE LIFF（PHP）用リポジトリ

このリポジトリの**現役の役割は `liff/nursing` の PHP**（LINE LIFF：音声→SOAP記録など）。
**シンVPS**（PHP実行サーバー）にデプロイする。

- GitHub: `makita611/AIdealize`（branch: **main**）
- デプロイ: シンVPS（Cloudflare ではない。PHPを動かすため）

## ⚠️ サイト・LPはこのリポジトリではない

aidealize.com 本体（LP・service・index 等のHTML）は別リポジトリ
**`makita611/aidealize-com`（ローカル `sites/aidealize`）** が Cloudflare Pages で配信している。
このリポジトリには **liff/ しか置かない**（2026-06-19 に古いサイトHTMLの残骸を削除して整理済み）。

| 作業内容 | 触るリポジトリ |
|---|---|
| サイト・LP・フォーム・GTM | `aidealize-com`（`sites/aidealize`） |
| **LINE LIFF の PHP** | **このリポジトリの `liff/`** |
