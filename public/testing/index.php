<?php
$publicHash = 'token';
$privateHash = 'Rl?IL:-rJb6kNW:T)3+^-H9i&u ydwyA/r>Y\TKXSHFaqiBMJ (w?<j{n.cO|7[ejSQSr[Fy(>=XzoQw5k>X/oLhSpD=9=8.dF&W';
$content    = json_encode(array(
    'credentials' => base64_encode('nathan') . ':' . base64_encode('nathan')
));
$time = time();
$contentmd5 = md5($content);

$hash = hash_hmac('sha512', $contentmd5 .','.$time, $privateHash);

$headers = array(
    'Authorization: RedtruckAPI ' . $publicHash . ':' . $hash,
	'X-Timestamp: ' . $time
);

$ch = curl_init('http://redtruck-api/users/login/');
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$content);

$result = curl_exec($ch);
curl_close($ch);

$obj = json_decode($result, true);



echo "RESULT\n======\n<pre>".print_r($obj, true)."</pre>\n\n";

$content = null;
$time = time();
$contentmd5 = md5($content);

$hash = hash_hmac('sha512', $contentmd5 .','.$time, $privateHash);

$headers = array(
    'Authorization: RedtruckAPI ' . $publicHash . ':' . $hash,
	'X-Timestamp: ' . $time,
	'X-Username: ' . $obj['username'],
	'X-User-Token: ' . $obj['authToken']
);

$ch = curl_init('http://redtruck-api/users/index/');
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

$result = curl_exec($ch);
curl_close($ch);

$obj = json_decode($result, true);

echo "RESULT\n======\n<pre>".print_r($obj, true)."</pre>\n\n";