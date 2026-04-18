<?php
/**
 * upload.php
 * 看護記録音声ファイル受取 → Dify API → LINE Push
 * 設置場所: public_html/liff/nursing/upload/upload.php
 */

// =============================================
// ★ここだけ変更してください
// =============================================
define('LINE_CHANNEL_ACCESS_TOKEN', 'gCntqA7sE/QhLfqHD/F4/hKMsHVl0bOvofa08bmKvXBTBD7dkGhGpbOa9EUnpvs45dW6eR6Z/8efBU8AXwJqVRFVQbAu8GE/UXKo4i59y52rskvM+t8myIalw195yCnKrYLCO37zp66cknwV+0bREAdB04t89/1O/w1cDnyilFU=');
define('DIFY_API_KEY',              'app-ovdswSVkoArZa7TmNPjkD1Gu');
define('DIFY_ENDPOINT',             'http://dify.aidealize.com/v1/workflows/run'); // またはhttps://
define('UPLOAD_DIR',                __DIR__ . '/uploads/');
// =============================================


// =============================================test

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
// =============================================test


header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// エラーログのみ、画面出力しない
ini_set('display_errors', 0);
error_reporting(E_ALL);

// POSTチェック
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

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

$file     = $_FILES['audio'];
// $allowed のチェックを一時的にコメントアウトするか、以下のように広げます v1修正
$allowed  = ['audio/mpeg','audio/mp4','audio/m4a','audio/wav','audio/aac','audio/x-m4a', 'application/octet-stream'];
$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowExt = ['mp3','m4a','wav','aac'];

if (!in_array($ext, $allowExt)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// アップロード保存
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

$newName  = uniqid('audio_', true) . '.' . $ext;
$savePath = UPLOAD_DIR . $newName;

if (!move_uploaded_file($file['tmp_name'], $savePath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

// ここでフロントに200を返す（ユーザーを待たせない）
http_response_code(200);

// デバッグ用：処理結果をそのまま返す
$uploadResult = difyUploadFile($savePath, $newName, $lineUserId);
$difyResult = ['success' => false, 'error' => 'not reached'];

if ($uploadResult['success']) {
    $fileId = $uploadResult['file_id'];
    $difyResult = callDifyApi($savePath, $newName, $lineUserId);
}

echo json_encode([
    'upload' => $uploadResult,
    'dify'   => $difyResult,
]);
exit;





// レスポンスをフラッシュして処理を続ける
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    ob_end_flush();
    flush();
}

// =============================================
// バックグラウンド処理
// =============================================

// 1. Dify APIへ送信
function callDifyApi($filePath, $fileName, $userId) {
    error_log("=== callDifyApi start ===");
    error_log("filePath: $filePath");
    
    $uploadResult = difyUploadFile($filePath, $fileName, $userId);
    error_log("uploadResult: " . json_encode($uploadResult));
    
    if (!$uploadResult['success']) {
        return ['success' => false, 'error' => 'Dify file upload failed: ' . $uploadResult['error']];
    }
    $fileId = $uploadResult['file_id'];
    error_log("fileId: $fileId");


    // 2. LINE Push返信
if ($difyResult['success']) {
    $soapText = $difyResult['text'];
    $message  = "✅ 看護記録が完成しました\n\n" . $soapText . "\n\n⚠️ AIの出力です。必ずご確認・修正してください。";
    sendLinePush($lineUserId, $message);
} else {
    sendLinePush($lineUserId, "❌ 記録の生成に失敗しました。\nエラー: " . $difyResult['error'] . "\n\n管理者にお問い合わせください。");
}

// 処理済みファイルを削除（個人情報保護）
if (file_exists($savePath)) unlink($savePath);

exit;

// =============================================
// Dify API呼び出し
// =============================================
function callDifyApi($filePath, $fileName, $userId) {
    // Difyにファイルをアップロード
    $uploadResult = difyUploadFile($filePath, $fileName, $userId);
    if (!$uploadResult['success']) {
        return ['success' => false, 'error' => 'Dify file upload failed: ' . $uploadResult['error']];
    }
    $fileId = $uploadResult['file_id'];

    // ワークフロー実行
    $payload = [
        'inputs'        => [
            'audio_file' => [
                'type'            => 'file',
                'transfer_method' => 'local_file',
                'upload_file_id'  => $fileId,
            ]
        ],
        'response_mode' => 'blocking',
        'user'          => $userId,
    ];

    $ch = curl_init(DIFY_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 600, // 10分タイムアウト
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . DIFY_API_KEY,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) return ['success' => false, 'error' => $curlErr];
    if ($httpCode !== 200) return ['success' => false, 'error' => "HTTP $httpCode: $response"];

    $data = json_decode($response, true);

    // Difyのワークフロー出力キーに合わせて変更してください
    $outputText = $data['data']['outputs']['text']
               ?? $data['data']['outputs']['text']
               ?? $data['data']['outputs']['result']
               ?? '（出力を取得できませんでした）';

    return ['success' => true, 'text' => $outputText];
}

// =============================================
// Dify ファイルアップロード    v1で修正
// =============================================
function difyUploadFile($filePath, $fileName, $userId) {
    $endpoint = str_replace('/workflows/run', '/files/upload', DIFY_ENDPOINT);

    // ファイルの本当の中身（MIMEタイプ）を調べる
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    // Difyが好む形式にマッピング
    $mimeMap = [
        'audio/mpeg' => 'mp3',
        'audio/mp3'  => 'mp3',
        'audio/mp4'  => 'm4a',
        'audio/x-m4a'=> 'm4a',
        'audio/wav'  => 'wav',
        'audio/x-wav'=> 'wav',
    ];

    // もし判別できればその拡張子を使い、ダメなら元の拡張子を信じる
    $finalExt = $mimeMap[$realMime] ?? pathinfo($fileName, PATHINFO_EXTENSION);
    $finalMime = $realMime ?: 'audio/mpeg';

    // Difyに送るための「嘘のない」ファイル名を作成
    $safeFileName = 'upload_audio.' . $finalExt;
    $cfile = new CURLFile($filePath, $finalMime, $safeFileName);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['file' => $cfile, 'user' => $userId],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . DIFY_API_KEY],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 201) return ['success' => false, 'error' => "HTTP $httpCode: $response"];

    $data = json_decode($response, true);
    return ['success' => true, 'file_id' => $data['id']];
}

// =============================================
// LINE Push送信
// =============================================
function sendLinePush($userId, $text) {
    // LINEの1メッセージ上限は5000文字
    $chunks = mb_str_split($text, 4900);

    foreach ($chunks as $chunk) {
        $payload = [
            'to'       => $userId,
            'messages' => [['type' => 'text', 'text' => $chunk]],
        ];

        $ch = curl_init('https://api.line.me/v2/bot/message/push');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . LINE_CHANNEL_ACCESS_TOKEN,
                'Content-Type: application/json',
            ],
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}
