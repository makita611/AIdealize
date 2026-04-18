<?php
// ============================================================
// はなまるAI 看護記録 — PC版 音声アップロード受信
// 即200返却 → バックグラウンドでDify処理 → 結果をファイルに保存
// フロントはpc_status.phpをポーリングして結果を取得
// ============================================================

require_once __DIR__ . '/../common.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 0);
set_time_limit(660);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['ok' => true, 'msg' => 'pc_upload.php is reachable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'ファイルのアップロードに失敗しました', 'code' => $_FILES['audio']['error'] ?? -1]);
    exit;
}

$file = $_FILES['audio'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'webm';

if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    http_response_code(400);
    echo json_encode(['error' => '対応していないファイル形式です: ' . $ext]);
    exit;
}

// 一時保存
$tmpPath = sys_get_temp_dir() . '/hana_pc_' . uniqid('', true) . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $tmpPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'ファイルの保存に失敗しました']);
    exit;
}

// ジョブID生成・結果ファイルパス
$jobId      = uniqid('pcjob_', true);
$resultPath = sys_get_temp_dir() . '/hana_result_' . $jobId . '.json';

// ---- 即200を返す ------------------------------------------------
http_response_code(200);
echo json_encode(['status' => 'processing', 'job_id' => $jobId]);

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    ignore_user_abort(true);
    ob_start();
    ob_end_flush();
    flush();
}

// ================================================================
// ここから処理（クライアントへの応答済み）
// ================================================================

// VPS変換エンドポイント経由でmono mp3に変換＆Difyアップロード
$userId = 'pc_' . substr(md5($jobId), 0, 16);
$uploadResult = convertAndUpload($tmpPath, $ext, $userId);
if (file_exists($tmpPath)) unlink($tmpPath);

if (!$uploadResult['success']) {
    file_put_contents($resultPath, json_encode([
        'status' => 'error',
        'error'  => 'アップロード失敗: ' . $uploadResult['error'],
    ]));
    exit;
}

// Dify ワークフロー実行
$difyResult = callDifyWorkflow($uploadResult['file_id'], $userId);

if ($difyResult['success']) {
    file_put_contents($resultPath, json_encode([
        'status' => 'done',
        'text'   => $difyResult['text'],
    ]));
} else {
    file_put_contents($resultPath, json_encode([
        'status' => 'error',
        'error'  => $difyResult['error'],
    ]));
}
