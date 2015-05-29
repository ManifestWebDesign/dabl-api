<?php

class App {
	static protected $authToken;

	static protected $authHeaderKey = null;
	static protected $sharedSecret = null;
	static protected $clientHash = null;

	static protected $validUserToken = false;

	static protected $userId = null;

	static function setConfigValues($config) {
		if (!empty($config['authHeaderKey'])) {
			self::$authHeaderKey = $config['authHeaderKey'] . ' ';
		} else {
			throw new Exception('Auth header key not defined');
		}

		if (!empty($config['sharedSecret'])) {
			self::$sharedSecret = $config['sharedSecret'];
		} else {
			throw new Exception('Shared secret not defined');
		}

		if (!empty($config['clientHash'])) {
			self::$clientHash = $config['clientHash'];
		} else {
			throw new Exception('Client hash not defined');
		}
	}

	static function setAuthToken($value) {
		self::$authToken = $_SESSION['__authToken'] = $value;
	}

	static function getAuthToken() {

		if (!self::$authToken && !empty($_SESSION['__authToken'])) {
			self::$authToken = $_SESSION['__authToken'];
		}
		return self::$authToken;
	}

	static function isAuthenticated() {
		return self::$validUserToken;
	}

	static function getUserId() {
		if (!self::$userId && !empty($_SESSION['__userId'])) {
			self::$userId = $_SESSION['__userId'];
		}
		return self::$userId;
	}

	static private function setUserId($value) {
		self::$userId = $_SESSION['__userId'] = $value;
	}

	static function login($request) {
		if (empty($request['credentials'])) {
			throw new Exception('Invalid username/password definition');
		}
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
			$session = Session::retrieveByPK($user->getId());
			$oldsession = null;
			if (!$session) {
				$session = new Session();
				$session->setUserId($user->getId());

			} else {
				$oldsession = $session->getPhpSessionToken();
			}
			$session->createAuthToken();
			$sessionid = self::startNewSession($oldsession);
			$session->setPhpSessionToken($sessionid);
			$session->save();

			self::setAuthToken($session->getAuthToken());
			self::setUserId($user->getId());
			return $user;
		} else {
			throw new Exception('Invalid user/password combination');
		}
	}

	static function authenticateRequest($request, $headers) {
		self::$validUserToken = false;
		if (empty($headers['X-Timestamp'])) {
			throw new Exception('Missing timestamp');
		}
		$time = intval($headers['X-Timestamp']);
		if ((string)$time !== (string)$headers['X-Timestamp']) {
			throw new Exception('Invalid timestamp');
		}
		$date = new DateTime();
		if ($date->setTimestamp($time) === false) {
			throw new Exception('Invalid timestamp');
		}
		$now = new DateTime();
		$now->modify('-30 minutes');
		if ($date < $now) {
			throw new Exception('Old request');
		}
		if (empty($headers['Authorization'])) {
			throw new Exception('Missing authorization');
		}
		$authHeader = $headers['Authorization'];
		if (strpos($authHeader, self::$authHeaderKey) !== 0) {
			throw new Exception("Auth header incorrect, value '" . self::$authHeaderKey . " not found': $authHeader");
		}

		$authorization = substr($authHeader, strlen(self::$authHeaderKey));
		if (strpos($authorization, ':') === false) {
			throw new Exception('Auth header incorrect');
		}
		$auth = explode(':', $authorization);
		if ($auth[0] !== self::$clientHash) {
			throw new Exception("Client key incorrect: '$auth'");
		}

		$hash = hash_hmac('sha512', hash('sha512', $request) .','.$time, self::$sharedSecret);
		if (!hash_equals($hash, $auth[1])) {
			throw new Exception("Hash: '$hash' doesn\'t match '$auth[1]'");
		}

		if (!empty($headers['X-Username'])) {
			$user = User::retrieveByUsername($headers['X-Username']);
			if ($user && !empty($headers['X-User-Token'])) { //Can also check for active status
				$session = Session::doSelectOne(Query::create()->add(Session::USER_ID, $user->getId())->add(Session::AUTH_TOKEN, $headers['X-User-Token']));
				if ($session) {
					self::startSession($session->getPhpSessionToken());
					self::$validUserToken = true;
				}
			}
		}

		return true;
	}

	static function generateRandomString($length = 25, $max = 250) {
		$bytes = openssl_random_pseudo_bytes($length);
		$hex   = bin2hex($bytes);
		if (strlen($hex) > $max) {
			$hex = substr($hex, 0, $max);
		}
		return $hex;
	}

	static function startSession($sessionid = null) {
		self::destroyCurrentSession();
		if ($sessionid) {
			session_id($sessionid);
		}
		return session_start();
	}

	static function startNewSession($oldsession = null) {
		if ($oldsession) {
			self::startSession($oldsession);
		}

		if (self::startSession() !== true) {
			throw new Exception('Unable to start a session');
		}
		return session_id();
	}

	static function destroyCurrentSession() {
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy();
	}
}
