<?php

class Session extends baseSession {

	function createAuthToken(){
		$this->setAuthToken(App::generateRandomString());
		return $this->getAuthToken();
	}


}