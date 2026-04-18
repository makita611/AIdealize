<?php
// ============================================================
// はなまるAI 看護記録 — LINE Webhook
//
// LINEから音声ファイル（またはLINE内録音）を受信して
// Difyでワークフロー実行 → SOAPノートをLINEに返信する
//
// 設置場所: /liff/nursing/webhook.php
// LINE Developers Console のWebhook URL に登録してください
// 例: https://aidealize.com/liff/nursing/webhook.php
// ============================================================

require_once __DIR__ . '/common.php';

ini_set('display_errors', 0);
set_time_limit(660);

// ---- リクエスト取得 ----------------------------------------
$body      = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'] ?? '';

// ---- 署名検証 ----------------------------------------------
// LINE_CHANNEL_SECRET が未設定の場合は検証スキップ（開発中のみ）
if (LINE_CHANNEL_SECRET !== 'YOUR_CHANNEL_SECRET_HERE') {
    if (!verifyLineSignature($body, $signature)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid signature']);
        exit;
    }
}

$json   = json_decode($body, true);
$events = $json['events'] ?? [];

// ---- LINEに即200を返す（必須：5秒以内に返さないとリトライされる）
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);

// ---- HTTPコネクションをここで閉じ、以降はバックグラウンド処理 ----
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    // Apache + mod_php 環境用
    ignore_user_abort(true);
    ob_start();
    ob_end_flush();
    flush();
}

// ============================================================
// イベント処理（コネクション切断後に実行）
// ============================================================
foreach ($events as $event) {

    // メッセージイベント以外はスキップ
    if (($event['type'] ?? '') !== 'message') continue;

    $msg        = $event['message']    ?? [];
    $userId     = $event['source']['userId'] ?? '';
    $replyToken = $event['replyToken'] ?? '';
    $msgType    = $msg['type']         ?? '';
    $messageId  = $msg['id']           ?? '';

    if (!$userId || !$messageId) continue;

    // ---- 対応するメッセージタイプ ----------------------------
    // 'audio' : LINE内蔵マイクで録音した音声（約5分まで）
    // 'file'  : スマホの録音アプリ等で録った音声ファイルを添付（最大1GB）
    if (!in_array($msgType, ['audio', 'file'])) {

        // 音声以外のメッセージが来たら使い方を案内
        if (in_array($msgType, ['text', 'image', 'video'])) {
            sendLinePush($userId,
                "🎤 音声ファイルを送ってください\n\n" .
                "スマホの録音アプリで録音した音声ファイル（m4a / mp3 / wav）を\n" .
                "このトークに添付して送信してください。\n\n" .
                "AIが自動でSOAPノートを作成してお返しします。"
            );
        }
        continue;
    }

    // ---- ファイル名・拡張子の特定 ----------------------------
    if ($msgType === 'file') {
        $fileName = $msg['fileName'] ?? 'audio.m4a';
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) ?: 'm4a';
    } else {
        // LINE audio メッセージは m4a 形式
        $ext      = 'm4a';
        $fileName = 'line_audio.m4a';
    }

    // 対応外の形式チェック
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        sendLinePush($userId,
            "❌ 対応していないファイル形式です（.{$ext}）\n\n" .
            "対応形式：mp3 / m4a / wav / aac\n" .
            "スマホの標準録音アプリで録音したファイルをお送りください。"
        );
        continue;
    }

    // ---- ファイルサイズチェック（LINE fileメッセージのみ） -----
    if ($msgType === 'file') {
        $fileSize = $msg['fileSize'] ?? 0;
        $maxBytes = 200 * 1024 * 1024; // 200MB
        if ($fileSize > $maxBytes) {
            sendLinePush($userId,
                "❌ ファイルが大きすぎます（" . round($fileSize / 1024 / 1024) . "MB）\n\n" .
                "200MB以下のファイルをお送りください。\n" .
                "※ wavファイルは非常に大きくなるため、m4aまたはmp3形式での送信をお勧めします。"
            );
            continue;
        }
    }

    // ---- 受付メッセージを先に送信 ----------------------------
    sendLinePush($userId,
        "📥 音声ファイルを受け付けました\n\n" .
        "SOAPノートを作成中です。\n" .
        "通常2〜5分ほどかかります。このままお待ちください…"
    );

    // ---- LINE Content API で音声を一時ダウンロード -----------
    $tmpPath = sys_get_temp_dir() . '/hana_wh_' . uniqid('', true) . '.' . $ext;

    if (!downloadLineContent($messageId, $tmpPath)) {
        sendLinePush($userId,
            "❌ ファイルのダウンロードに失敗しました\n\n" .
            "再度ファイルを送信してください。\n" .
            "繰り返し失敗する場合は管理者にご連絡ください。"
        );
        continue;
    }

    // ---- VPS変換エンドポイント経由でmono mp3に変換＆Difyアップロード ----
    $uploadResult = convertAndUpload($tmpPath, $ext, $userId);
    if (file_exists($tmpPath)) unlink($tmpPath);

    if (!$uploadResult['success']) {
        sendLinePush($userId,
            "❌ アップロードに失敗しました\n\n" .
            "エラー: " . $uploadResult['error'] . "\n\n" .
            "再度お試しいただくか、管理者にご連絡ください。"
        );
        continue;
    }

    // ---- Dify ワークフロー実行 --------------------------------
    $difyResult = callDifyWorkflow($uploadResult['file_id'], $userId);

    if ($difyResult['success']) {
        $message =
            "✅ 看護記録が完成しました\n\n" .
            $difyResult['text'] .
            "\n\n─────────────────\n" .
            "⚠️ AIの出力です。必ずご確認・修正してください。\n" .
            "電子カルテ等にコピー＆ペーストしてご使用ください。";
        sendLinePush($userId, $message);
    } else {
        sendLinePush($userId,
            "❌ 記録の生成に失敗しました\n\n" .
            "エラー: " . $difyResult['error'] . "\n\n" .
            "再度お試しいただくか、管理者にご連絡ください。"
        );
    }
}
