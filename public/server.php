<?php

if (isset($_SERVER['HTTP_ORIGIN']) && !headers_sent()) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	//header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS' && !headers_sent()) {
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
		header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
	}

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	}
	die;
}

require_once '../config.php';

// string with url requested by visitor.  Usually in the form of:
// controller/action/arg1/arg2?param1=value1
// @see public/.htaccess
$requested_route = @$_GET['_url'];

// clear params used for routing
unset($_GET['_url'], $_REQUEST['_url']);

$headers = get_request_headers();

// transfer posted json data to global request data arrays
$data = file_get_contents('php://input');

$json_data = json_decode($data, true);
if (is_array($json_data)) {
	$_REQUEST = array_merge($_REQUEST, $json_data);
	$_POST = $json_data;
}

try {
	App::authenticateRequest($data, $headers);
	ApplicationController::load($requested_route, $headers, $_REQUEST);
} catch (Exception $e) {
	error_log($e);
	http_response_code(401);
	die();
}