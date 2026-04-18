<?php
// ============================================================
// Dify 音声テストツール — ブラウザからファイル選択して動作確認
// アクセス: https://aidealize.com/liff/nursing/test_dify.php
// ============================================================

require_once __DIR__ . '/common.php';

ini_set('display_errors', 1);
set_time_limit(660);

$result   = null;
$uploadOk = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audio'])) {
    $file = $_FILES['audio'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result = ['step' => 'upload', 'error' => 'ファイルエラー: ' . $file['error']];
    } else {
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'm4a';
        $mime = detectMime($file['tmp_name'], $ext);
        $userId = 'test_' . substr(md5(uniqid('', true)), 0, 8);

        // ---- Step 1: Dify アップロード ----
        $uploadResult = difyUploadFile($file['tmp_name'], $ext, $mime, $userId);
        @unlink($file['tmp_name']);

        if (!$uploadResult['success']) {
            $result = ['step' => 'dify_upload', 'error' => $uploadResult['error'],
                       'mime' => $mime, 'ext' => $ext, 'size' => $file['size']];
        } else {
            $fileId  = $uploadResult['file_id'];
            $uploadOk = true;

            // ---- Step 2: ワークフロー実行 ----
            // raw レスポンス取得のため直接呼ぶ
            $rawResponse = '';
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
            $ch2 = curl_init(DIFY_WORKFLOW);
            curl_setopt_array($ch2, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 600,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . DIFY_API_KEY,
                    'Content-Type: application/json',
                ],
            ]);
            $rawResponse = curl_exec($ch2);
            $wfCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            curl_close($ch2);

            $difyResult = callDifyWorkflow($fileId, $userId . '_2');

            if (!$difyResult['success']) {
                $result = ['step' => 'dify_workflow', 'error' => $difyResult['error'],
                           'file_id' => $fileId, 'mime' => $mime, 'ext' => $ext,
                           'raw' => $rawResponse, 'http_code' => $wfCode];
            } else {
                $result = ['step' => 'success', 'text' => $difyResult['text'],
                           'file_id' => $fileId, 'mime' => $mime, 'ext' => $ext, 'size' => $file['size'],
                           'raw' => $rawResponse];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dify 音声テスト</title>
<style>
  body { font-family: sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
  h1 { color: #2c7a7b; font-size: 1.3rem; }
  .card { background: #fff; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
  input[type=file] { display: block; margin: 12px 0; padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; }
  button { background: #2c7a7b; color: #fff; border: none; padding: 12px 28px; border-radius: 6px; cursor: pointer; font-size: 1rem; }
  button:hover { background: #235f60; }
  .info  { background: #e8f4f8; border-left: 4px solid #2c7a7b; padding: 12px 16px; border-radius: 4px; font-size: .85rem; margin-bottom: 16px; }
  .ok    { background: #e6f4ea; border-left: 4px solid #34a853; padding: 12px 16px; border-radius: 4px; }
  .err   { background: #fdecea; border-left: 4px solid #ea4335; padding: 12px 16px; border-radius: 4px; }
  pre   { white-space: pre-wrap; word-break: break-all; font-size: .82rem; margin: 8px 0 0; }
  label { font-weight: bold; font-size: .95rem; }
</style>
</head>
<body>

<h1>🎤 Dify 音声テスト</h1>

<div class="card">
  <form method="POST" enctype="multipart/form-data">
    <div class="info">
      対応形式: <strong>m4a / mp3 / wav / aac / webm / ogg</strong><br>
      ファイルを選択して「送信」を押すと Dify に送り、SOAPノートを生成します。
    </div>
    <label for="audio">音声ファイルを選択</label>
    <input type="file" id="audio" name="audio" accept=".m4a,.mp3,.wav,.aac,.webm,.ogg,.mpga" required>
    <button type="submit">⬆️ 送信して処理</button>
  </form>
</div>

<?php if ($result): ?>
<div class="card">

  <?php if ($result['step'] === 'success'): ?>
    <div class="ok">
      <strong>✅ 成功！</strong><br>
      ファイル: <code><?= htmlspecialchars($result['ext']) ?></code>
      | MIME: <code><?= htmlspecialchars($result['mime']) ?></code>
      | サイズ: <?= number_format($result['size']) ?> bytes
      | file_id: <code><?= htmlspecialchars($result['file_id']) ?></code>
    </div>
    <h3 style="margin-top:16px">📋 生成された記録</h3>
    <pre><?= htmlspecialchars($result['text']) ?></pre>

  <?php elseif ($result['step'] === 'dify_upload'): ?>
    <div class="err">
      <strong>❌ Difyアップロード失敗</strong><br>
      ファイル: <code><?= htmlspecialchars($result['ext']) ?></code>
      | MIME: <code><?= htmlspecialchars($result['mime']) ?></code>
      | サイズ: <?= number_format($result['size']) ?> bytes
      <pre><?= htmlspecialchars($result['error']) ?></pre>
    </div>

  <?php elseif ($result['step'] === 'dify_workflow'): ?>
    <div class="err">
      <strong>❌ ワークフロー失敗</strong>（アップロードは成功）<br>
      ファイル: <code><?= htmlspecialchars($result['ext']) ?></code>
      | MIME: <code><?= htmlspecialchars($result['mime']) ?></code>
      | file_id: <code><?= htmlspecialchars($result['file_id']) ?></code>
      <pre><?= htmlspecialchars($result['error']) ?></pre>
    </div>

  <?php else: ?>
    <div class="err">
      <strong>❌ エラー</strong>
      <pre><?= htmlspecialchars($result['error']) ?></pre>
    </div>
  <?php endif; ?>

  <?php if (!empty($result['raw'])): ?>
  <h3 style="margin-top:16px; font-size:.9rem; color:#555">🔍 Dify raw レスポンス（HTTP <?= $result['http_code'] ?? '?' ?>）</h3>
  <pre style="font-size:.75rem; background:#f8f8f8; padding:12px; border-radius:4px; overflow-x:auto"><?= htmlspecialchars(json_encode(json_decode($result['raw']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
  <?php endif; ?>

</div>
<?php endif; ?>

</body>
</html>
