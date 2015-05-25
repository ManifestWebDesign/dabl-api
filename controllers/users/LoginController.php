<?php

class LoginController extends ApplicationController {
	function save() {
		return Session::login($_REQUEST);
	}

}
