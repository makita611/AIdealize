<?php
// ============================================================
// はなまるAI 看護記録 — 共通設定・共通処理
// ここの定数だけ変更すれば全ファイルに反映されます
// ============================================================

define('LINE_CHANNEL_ACCESS_TOKEN', 'gCntqA7sE/QhLfqHD/F4/hKMsHVl0bOvofa08bmKvXBTBD7dkGhGpbOa9EUnpvs45dW6eR6Z/8efBU8AXwJqVRFVQbAu8GE/UXKo4i59y52rskvM+t8myIalw195yCnKrYLCO37zp66cknwV+0bREAdB04t89/1O/w1cDnyilFU=');
define('LINE_CHANNEL_SECRET',       'ef658426103a6446fcc1a1001c5f0aa0'); // ← LINE Developers Console で確認
define('DIFY_API_KEY',              'app-ovdswSVkoArZa7TmNPjkD1Gu');
define('DIFY_UPLOAD',               'http://162.43.77.115/v1/files/upload');
define('DIFY_WORKFLOW',             'http://162.43.77.115/v1/workflows/run');

// 対応音声形式
define('ALLOWED_EXTENSIONS', ['mp3', 'm4a', 'wav', 'aac', 'webm', 'ogg', 'mpga']);

// ============================================================
// 音声変換・アップロード（シンVPSの変換エンドポイント経由）
// wavなど大きいファイルをmono mp3に変換してDifyにアップロード
// 戻り値: ['success'=>bool, 'file_id'=>string, 'error'=>string]
// ============================================================
define('CONVERTER_URL',    'http://162.43.77.115:8088/convert.php');
define('CONVERTER_SECRET', 'HANA_CONV_2026');

function convertAndUpload(string $filePath, string $ext, string $userId): array
{
    $cfile = new CURLFile($filePath, 'audio/' . $ext, 'audio.' . $ext);
    $ch = curl_init(CONVERTER_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'audio'   => $cfile,
            'secret'  => CONVERTER_SECRET,
            'user_id' => $userId,
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 300,
    ]);
    $res     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($curlErr) return ['success' => false, 'error' => 'converter curl: ' . $curlErr];
    if ($httpCode !== 200) return ['success' => false, 'error' => "converter HTTP $httpCode: $res"];

    $data = json_decode($res, true);
    if (!($data['success'] ?? false)) {
        return ['success' => false, 'error' => $data['error'] ?? '変換失敗'];
    }
    return ['success' => true, 'file_id' => $data['file_id']];
}

// ============================================================
// 音声圧縮（ローカルffmpeg使用・使えない場合はそのまま返す）
// 戻り値: [圧縮後パス, 変換したか(bool)]
// ============================================================
function compressAudio(string $inputPath, string $ext): array
{
    $candidates = ['/usr/bin/ffmpeg', '/usr/local/bin/ffmpeg', '/opt/homebrew/bin/ffmpeg'];
    $ffmpeg = '';
    foreach ($candidates as $path) {
        if (file_exists($path) && is_executable($path)) { $ffmpeg = $path; break; }
    }
    if (empty($ffmpeg) && function_exists('shell_exec')) {
        $found = trim(@shell_exec('which ffmpeg 2>/dev/null') ?? '');
        if (!empty($found)) $ffmpeg = $found;
    }
    if (empty($ffmpeg)) return [$inputPath, false];

    $outputPath = sys_get_temp_dir() . '/hana_c_' . uniqid('', true) . '.mp3';
    $cmd = sprintf(
        '%s -i %s -ac 1 -ar 16000 -ab 64k -vn -y %s 2>/dev/null',
        escapeshellarg($ffmpeg),
        escapeshellarg($inputPath),
        escapeshellarg($outputPath)
    );
    exec($cmd, $out, $code);

    if ($code !== 0 || !file_exists($outputPath) || filesize($outputPath) < 1024) {
        if (file_exists($outputPath)) unlink($outputPath);
        return [$inputPath, false];
    }
    return [$outputPath, true];
}

// ============================================================
// MIME タイプ判定
// ============================================================
function detectMime(string $filePath, string $ext): string
{
    // 音声ファイルは拡張子ベースのMIMEを優先
    // finfo は m4a を audio/mp4 や video/mp4 と返すことがあり Whisper に拒否される
    $mimeMap = [
        'mp3'  => 'audio/mpeg',
        'm4a'  => 'audio/m4a',
        'wav'  => 'audio/wav',
        'aac'  => 'audio/aac',
        'webm' => 'video/webm',
        'ogg'  => 'audio/ogg',
        'mpga' => 'audio/mpeg',
    ];

    // 既知の音声形式は mimeMap を直接使用（finfo より信頼性が高い）
    if (isset($mimeMap[$ext])) return $mimeMap[$ext];

    // 未知の拡張子のみ finfo で検出
    if (function_exists('finfo_open')) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $detected = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        if ($detected && $detected !== 'application/octet-stream') {
            return $detected;
        }
    }

    return 'audio/mpeg';
}

// ============================================================
// Dify: 音声ファイルをアップロード → file_id を返す
// ============================================================
function difyUploadFile(string $filePath, string $ext, string $mime, string $userId): array
{
    $cfile = new CURLFile($filePath, $mime, 'audio.' . $ext);

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
    if ($httpCode !== 201) return ['success' => false, 'error' => "HTTP {$httpCode}: {$response}"];

    $data = json_decode($response, true);
    return ['success' => true, 'file_id' => $data['id']];
}

// ============================================================
// Dify: ワークフロー実行 → テキスト結果を返す
// ============================================================
function callDifyWorkflow(string $fileId, string $userId): array
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
    if ($httpCode !== 200) return ['success' => false, 'error' => "HTTP {$httpCode}: {$response}"];

    $data = json_decode($response, true);
    $status = $data['data']['status'] ?? $data['status'] ?? '';

    if (!in_array($status, ['succeeded', 'completed', 'partial-succeeded'])) {
        $errMsg = $data['data']['error'] ?? $data['message'] ?? json_encode($data, JSON_UNESCAPED_UNICODE);
        return ['success' => false, 'error' => $errMsg];
    }

    // outputs のキー名はDifyワークフローの設定による（text / output / result 等）
    $outputs = $data['data']['outputs'] ?? $data['outputs'] ?? [];
    $text = $outputs['text']
         ?? $outputs['output']
         ?? $outputs['result']
         ?? $outputs['answer']
         ?? $outputs['soap']
         ?? (count($outputs) > 0 ? array_values($outputs)[0] : '（出力なし）');

    return ['success' => true, 'text' => $text];
}

// ============================================================
// LINE: Push送信（4900文字超えは自動分割）
// ============================================================
function sendLinePush(string $userId, string $text): void
{
    foreach (mb_str_split($text, 4900) as $chunk) {
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

// ============================================================
// LINE: Reply送信（replyToken 使用・webhook の即時返信用）
// ============================================================
function sendLineReply(string $replyToken, string $text): void
{
    $payload = [
        'replyToken' => $replyToken,
        'messages'   => [['type' => 'text', 'text' => $text]],
    ];
    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . LINE_CHANNEL_ACCESS_TOKEN,
            'Content-Type: application/json',
        ],
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// ============================================================
// LINE Content API: メッセージIDから音声を一時ファイルにDL
// ============================================================
function downloadLineContent(string $messageId, string $savePath): bool
{
    $ch = curl_init("https://api-data.line.me/v2/bot/message/{$messageId}/content");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 180,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . LINE_CHANNEL_ACCESS_TOKEN,
        ],
    ]);

    $data     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr || $httpCode !== 200 || !$data) return false;
    return file_put_contents($savePath, $data) !== false;
}

// ============================================================
// LINE Webhook: 署名検証
// ============================================================
function verifyLineSignature(string $body, string $signature): bool
{
    if (empty($signature)) return false;
    $hash = hash_hmac('sha256', $body, LINE_CHANNEL_SECRET, true);
    return hash_equals(base64_encode($hash), $signature);
}
