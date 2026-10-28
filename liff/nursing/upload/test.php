<?php
// 秘密情報は common.php 経由で config.local.php から読み込む（ベタ書き禁止）
require_once __DIR__ . '/../common.php';
$DIFY_API_KEY = DIFY_API_KEY;
$DIFY_UPLOAD  = DIFY_UPLOAD;

$tmpFile = tempnam(sys_get_temp_dir(), 'test') . '.m4a';
file_put_contents($tmpFile, str_repeat("\0", 1024));

$cfile = new CURLFile($tmpFile, 'audio/mp4', 'test.m4a');

$ch = curl_init($DIFY_UPLOAD);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => ['file' => $cfile, 'user' => 'test_user'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $DIFY_API_KEY],
]);
$res  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

unlink($tmpFile);

echo "HTTP Code: $code\n";
if ($err) echo "cURL Error: $err\n";

if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
}

echo "Response: $res\n";