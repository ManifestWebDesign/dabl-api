<?php

class Session {
	protected $sessionid;
	protected $requestData;

	static protected $authHeaderKey = 'RedtruckAPI ';
	static protected $token = 'Rl?IL:-rJb6kNW:T)3+^-H9i&u ydwyA/r>Y\TKXSHFaqiBMJ (w?<j{n.cO|7[ejSQSr[Fy(>=XzoQw5k>X/oLhSpD=9=8.dF&W';
	static protected $client = 'token';

	static function setSessionID($value) {
		$this->sessionid = $value;
	}

	static function setRequestData($value){
		$this->requestData = $value;
	}

	static function login($request) {
		$credentials = $request['credentials'];
		if (strpos($credentials, ':') === false || substr_count($credentials, ':') !== 1) {
			throw new Exception('Invalid username/password definition');
		}
		$credArray = explode(':', $credentials);
		if (count($credArray) !== 2) {
			throw new Exception('Invalid username/password definition');
		}
		$username = base64_decode($credArray[0]);
		$password = base64_decode($credArray[1]);

		if (base64_encode($username) !== $credArray[0] || base64_encode($password) !== $credArray[1]) {
			throw new Exception('Base64 error');
		}

		$user = User::retrieveByUsername($username);
		if (!$user) {
			throw new Exception('Invalid user');
		}

		if (password_verify($password, $user->getPasswordHash())) {
			return $user;
		} else {
			throw new Exception('Invalid user/password combination');
		}
	}

	static function authenticateRequest($request, $headers) {
		print_r2($headers);
		print_r2($request);

		if (empty($headers['X-Timestamp'])) {
			throw new Exception('Missing timestamp');
		}
		$time = $headers['X-Timestamp'];

		if (empty($headers['Authorization'])) {
			throw new Exception('Missing authorization');
		}
		$authHeader = $headers['Authorization'];
		if (strpos($authHeader, self::$authHeaderKey) !== 0) {
			throw new Exception('Auth header incorrect');
		}
		print_r2($authHeader);
		$authorization = substr($authHeader, strlen(self::$authHeaderKey));
		if (strpos($authorization, ':') === false) {
			throw new Exception('Auth header incorrect');
		}
		$auth = explode(':', $authorization);
		if ($auth[0] !== self::$client) {
			print_r2($auth);
			throw new Exception("Client key incorrect: '$auth'");
		}

		$hash = hash_hmac('sha256', md5($request) .','.$time, self::$token);
		if (!hash_equals($hash, $auth[1])) {
			throw new Exception("Hash: '$hash' doesn\'t match '$auth[1]'");
		}

		die("Success!");
	}

}
