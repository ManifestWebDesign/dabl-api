<?php

class LoginController extends ApplicationController {
	function save() {
		return App::login($_REQUEST);
	}

}
