<?php

class AuthenticatedApplicationController extends ApplicationController {
	function __construct(\ControllerRoute $route = null) {
		if (!App::isAuthenticated()) {
			throw new Exception('Not authenticated');
		}

		parent::__construct($route);
	}
}
