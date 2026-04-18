<?php
// ============================================================
// はなまるAI 看護記録 — LIFF 音声アップロード受信
// LIFFページ（index.html）からのファイル送信を処理する
// ============================================================

require_once __DIR__ . '/../common.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 0);
set_time_limit(660);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// LINE ユーザーID（LIFF から取得）
$lineUserId = $_POST['line_user_id'] ?? '';
if (empty($lineUserId)) {
    http_response_code(400);
    echo json_encode(['error' => 'line_user_id missing']);
    exit;
}

// ファイルチェック
if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'File upload failed', 'code' => $_FILES['audio']['error'] ?? -1]);
    exit;
}

$file = $_FILES['audio'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'm4a';

if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    http_response_code(400);
    echo json_encode(['error' => '対応していないファイル形式です: ' . $ext]);
    exit;
}

// 一時ファイルとして保存（処理後に削除）
$tmpPath = sys_get_temp_dir() . '/hana_liff_' . uniqid('', true) . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $tmpPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

// ---- フロントエンドに即200を返す ----------------------------
// index.html はアップロード完了で完了画面を表示するため、
// 以降の処理（Dify・LINE push）はバックグラウンドで実行
http_response_code(200);
echo json_encode(['success' => true, 'status' => 'processing']);

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request(); // PHP-FPM 環境（推奨）
} else {
    // Apache + mod_php 環境用
    ignore_user_abort(true);
    ob_start();
    ob_end_flush();
    flush();
}

// ============================================================
// ここから処理（フロントへの応答済み）
// ============================================================

// VPS変換エンドポイント経由でmono mp3に変換＆Difyアップロード
$uploadResult = convertAndUpload($tmpPath, $ext, $lineUserId);
if (file_exists($tmpPath)) unlink($tmpPath);

if (!$uploadResult['success']) {
    sendLinePush($lineUserId,
        "❌ ファイルのアップロードに失敗しました\n\nエラー: " . $uploadResult['error'] .
        "\n\n管理者にお問い合わせください。"
    );
    exit;
}

// Dify ワークフロー実行
$difyResult = callDifyWorkflow($uploadResult['file_id'], $lineUserId);

if ($difyResult['success']) {
    $message =
        "✅ 看護記録が完成しました\n\n" .
        $difyResult['text'] .
        "\n\n─────────────────\n" .
        "⚠️ AIの出力です。必ずご確認・修正してください。\n" .
        "電子カルテ等にコピー＆ペーストしてご使用ください。";
    sendLinePush($lineUserId, $message);
} else {
    sendLinePush($lineUserId,
        "❌ 記録の生成に失敗しました\n\nエラー: " . $difyResult['error'] .
        "\n\n管理者にお問い合わせください。"
    );
}
