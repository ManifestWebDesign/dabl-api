<?php

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
App::authenticateRequest($data, $headers);

try {
	ApplicationController::load($requested_route, $headers, $_REQUEST);
} catch (Exception $e) {
	error_log($e);
	http_response_code(401);
	die();
}