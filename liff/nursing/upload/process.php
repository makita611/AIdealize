<?php
// =============================================
// ★書き換え箇所
// =============================================
define('LINE_CHANNEL_ACCESS_TOKEN', 'gCntqA7sE/QhLfqHD/F4/hKMsHVl0bOvofa08bmKvXBTBD7dkGhGpbOa9EUnpvs45dW6eR6Z/8efBU8AXwJqVRFVQbAu8GE/UXKo4i59y52rskvM+t8myIalw195yCnKrYLCO37zp66cknwV+0bREAdB04t89/1O/w1cDnyilFU=');
define('DIFY_API_KEY',              'app-ovdswSVkoArZa7TmNPjkD1Gu');
define('DIFY_UPLOAD',               'http://162.43.77.115/v1/files/upload');
define('DIFY_WORKFLOW',             'http://162.43.77.115/v1/workflows/run');
// =============================================

// 直接アクセス禁止（upload.phpからのみ呼び出し可）
$secret = $_POST['secret'] ?? '';
if ($secret !== 'HANAMARU_SECRET_2026') {
    http_response_code(403);
    exit;
}

$lineUserId = $_POST['line_user_id'] ?? '';
$filePath   = $_POST['file_path'] ?? '';
$ext        = $_POST['ext'] ?? '';

if (empty($lineUserId) || empty($filePath) || !file_exists($filePath)) {
    exit;
}

// MIMEタイプ判定
$mimeMap = [
    'mp3' => 'audio/mpeg',
    'm4a' => 'audio/m4a',
    'wav' => 'audio/wav',
    'aac' => 'audio/aac',
];
$mime = $mimeMap[$ext] ?? 'audio/mpeg';

// ステップ1：Difyにファイルアップロード
$uploadResult = difyUploadFile($filePath, $ext, $mime, $lineUserId);

if (!$uploadResult['success']) {
    sendLinePush($lineUserId, "❌ ファイルのアップロードに失敗しました。\nエラー: " . $uploadResult['error'] . "\n\n管理者にお問い合わせください。");
    if (file_exists($filePath)) unlink($filePath);
    exit;
}

// ステップ2：ワークフロー実行
$difyResult = callDifyWorkflow($uploadResult['file_id'], $lineUserId);

if ($difyResult['success']) {
    $message = "✅ 看護記録が完成しました\n\n" . $difyResult['text'] . "\n\n⚠️ AIの出力です。必ずご確認・修正してください。";
    sendLinePush($lineUserId, $message);
} else {
    sendLinePush($lineUserId, "❌ 記録の生成に失敗しました。\nエラー: " . $difyResult['error'] . "\n\n管理者にお問い合わせください。");
}

if (file_exists($filePath)) unlink($filePath);
exit;

// =============================================
// Dify ファイルアップロード
// =============================================
function difyUploadFile($filePath, $ext, $mime, $userId)
{
    $cfile = new CURLFile($filePath, $mime, 'upload.' . $ext);

    $ch = curl_init(DIFY_UPLOAD);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['file' => $cfile, 'user' => $userId],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 300,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . DIFY_API_KEY],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) return ['success' => false, 'error' => 'curl: ' . $curlErr];
    if ($httpCode !== 201) return ['success' => false, 'error' => "HTTP $httpCode: $response"];

    $data = json_decode($response, true);
    return ['success' => true, 'file_id' => $data['id']];
}

// =============================================
// Dify ワークフロー実行
// =============================================
function callDifyWorkflow($fileId, $userId)
{
    $payload = [
        'inputs' => [
            'audio_file' => [
                'type'            => 'audio',
                'transfer_method' => 'local_file',
                'upload_file_id'  => $fileId,
            ],
        ],
        'response_mode' => 'blocking',
        'user'          => $userId,
    ];

    $ch = curl_init(DIFY_WORKFLOW);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 600,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . DIFY_API_KEY,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) return ['success' => false, 'error' => 'curl: ' . $curlErr];
    if ($httpCode !== 200) return ['success' => false, 'error' => "HTTP $httpCode: $response"];

    $data = json_decode($response, true);

    if ($data['data']['status'] !== 'succeeded') {
        return ['success' => false, 'error' => $data['data']['error'] ?? '不明なエラー'];
    }

    $text = $data['data']['outputs']['text'] ?? '（出力なし）';
    return ['success' => true, 'text' => $text];
}

// =============================================
// LINE Push送信
// =============================================
function sendLinePush($userId, $text)
{
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
