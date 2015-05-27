<?php

function makeRequest($url, $content, $username = null, $userToken = null) {
	$publicHash = '9e6678ebcabedc15a82f270fee26f5e694f78600c24a60cf74';
	$privateHash = 'Rl?IL:-rJb6kNW:T)3+^-H9i&u ydwyA/r>Y\TKXSHFaqiBMJ (w?<j{n.cO|7[ejSQSr[Fy(>=XzoQw5k>X/oLhSpD=9=8.dF&W';
	$time = time();
	$contentmd5 = md5($content);

	$hash = hash_hmac('sha512', $contentmd5 .','.$time, $privateHash);

	$headers = array(
		'Authorization: RedtruckAPI ' . $publicHash . ':' . $hash,
		'X-Timestamp: ' . $time
	);
	if ($username) {
		$headers[] = "X-Username: $username";
	}
	if ($userToken) {
		$headers[] = "X-User-Token: $userToken";
	}
	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	if ($content) {
		curl_setopt($ch,CURLOPT_POSTFIELDS,$content);
	}

	$result = curl_exec($ch);
	curl_close($ch);

	return json_decode($result, true);
}

function login() {
	$content    = json_encode(array(
		'credentials' => base64_encode('nathan') . ':' . base64_encode('nathan')
	));


	$obj = makeRequest('http://redtruck-api/users/login/', $content);

	echo "RESULT\n======\n<pre>".print_r($obj, true)."</pre>\n\n";
}

function getUsers() {
	$obj = makeRequest('http://redtruck-api/users/index/', null, 'nathan', '6666d5d45d482b1c19007582f1d413aeb2f8dd9cea5a29d4a3');


	echo "<pre>RESULT\n======\nJson decoded: " . print_r($obj, true). "</pre>\n\n";
}

getUsers();