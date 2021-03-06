<?php

ini_set('session.use_cookies', '0'); //Turn off session cookies

define('IS_MVC', true);

ClassLoader::addRepository('MVC', LIBRARIES_DIR . 'dabl/mvc');

$helpers = glob(LIBRARIES_DIR . 'dabl/mvc/helpers/*.php');
sort($helpers);
foreach ($helpers as $helper) {
	require_once $helper;
}

/** Session * */
// start the session

$result = @session_start();
if (!$result) {
	if (!headers_sent()) {
		redirect('/');
	}
	throw new RuntimeException('Session ID was invalid and couldn\'t recover');
}

// the browser path to this application.  it should be:
// a full url with http:// and a trailing slash OR
// a subdirectory with leading and trailing slashes
define('BASE_URL', '/');

// directory for public html files that are directly exposed to the web server
define('PUBLIC_DIR', APP_DIR . 'public/');

// default controller
define('DEFAULT_CONTROLLER', 'index');

// controllers directory
define('CONTROLLERS_DIR', APP_DIR . 'controllers/');
ClassLoader::addRepository('CONTROLLERS', CONTROLLERS_DIR);

// views directory
define('VIEWS_DIR', APP_DIR . 'views/');

// default timestamp format for views
define('VIEW_TIMESTAMP_FORMAT', 'n/j/Y g:i a');

// default date format for views
define('VIEW_DATE_FORMAT', 'n/j/Y');
