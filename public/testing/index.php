<?php
$publicHash = 'token';
$privateHash = 'Rl?IL:-rJb6kNW:T)3+^-H9i&u ydwyA/r>Y\TKXSHFaqiBMJ (w?<j{n.cO|7[ejSQSr[Fy(>=XzoQw5k>X/oLhSpD=9=8.dF&W';
$content    = json_encode(array(
    'test' => 'content'
));
$time = time();
$contentmd5 = md5($content);

$hash = hash_hmac('sha256', $contentmd5 .','.$time, $privateHash);

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

echo "RESULT\n======\n".print_r($result, true)."\n\n";

