<?php

class AuthenticatedApplicationController extends ApplicationController {
	function __construct(\ControllerRoute $route = null) {


		parent::__construct($route);
	}
}
