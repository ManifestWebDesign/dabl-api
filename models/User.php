<?php

class User extends baseUser {

	function jsonSerialize() {
		$arr = parent::jsonSerialize();
		unset($arr['passwordHash']);
		$appuserid = App::getUserId();
		if ($appuserid && $appuserid == $this->getId()) {
			$sessionid = App::getAuthToken();
			if ($sessionid) {
				$arr['authToken'] = $sessionid;
			}
		}
		return $arr;
	}
}