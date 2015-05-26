<?php

class User extends baseUser {

	function jsonSerialize() {
		$arr = parent::jsonSerialize();
		unset($arr['passwordHash']);
		$sessionid = App::getAuthToken();
		if ($sessionid) {
			$arr['authToken'] = $sessionid;
		}
		return $arr;
	}
}