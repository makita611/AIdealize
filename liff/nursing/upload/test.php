<?php
$DIFY_API_KEY = 'app-ovdswkD1Gu';
$DIFY_UPLOAD  = 'http://162.43.77.115/v1/files/upload';

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