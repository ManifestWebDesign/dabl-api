<?php

define('CONFIG_DIR', '../../config/');
echo "<pre>";
function makeRequest($url, $content, $username = null, $userToken = null) {
	$hashdata = parse_ini_file(CONFIG_DIR . 'api.ini', true);
	$publicHash = $hashdata['clientHash'];
	$privateHash = $hashdata['sharedSecret'];
	$time = time();
	$contenthash = hash('sha512', $content);

	$hash = hash_hmac('sha512', $contenthash .','.$time, $privateHash);

	$headers = array(
		"Authorization: {$hashdata['authHeaderKey']} $publicHash:$hash",
		'X-Timestamp: ' . $time
	);
	if ($username) {
		$headers[] = "X-Username: $username";
	}
	if ($userToken) {
		$headers[] = "X-User-Token: $userToken";
	}
	$ch = curl_init($_SERVER['SERVER_NAME'] . '/'.$url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	if ($content) {
		curl_setopt($ch,CURLOPT_POSTFIELDS,$content);
	}

	$result = curl_exec($ch);
	$infos = curl_getinfo($ch);
	curl_close($ch);

	echo "Curl info:\n";
	print_r($infos);
	echo "\n";
	return json_decode($result, true);
}

function login($username, $password) {
	$content    = json_encode(array(
		'credentials' => base64_encode($username) . ':' . base64_encode($password)
	));

	echo "CALLING LOGIN\n\n";
	$obj = makeRequest('users/login/', $content);

	echo print_r($obj, true)."\n\nFINISHED CALLING LOGIN\n\n";
	return $obj;
}

function getUsers($username, $authtoken) {
	echo "CALLING GET USERS\n\n";
	$obj = makeRequest('users/index/', null, $username, $authtoken);
	echo print_r($obj, true). "\n\nFINISHED CALLING GET USERS\n\n";
}


$user = login('nathan', 'nathan');

getUsers($user['username'], $user['authToken']);