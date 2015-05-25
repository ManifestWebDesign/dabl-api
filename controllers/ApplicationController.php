<?php

abstract class ApplicationController extends Controller {
	public function doAction($action_name = null, $params = array()) {
		if ($this->outputFormat != 'html') {
			if (isset($this['title'])) {
				unset($this['title']);
			}
			if (isset($this['current_page'])){
				unset($this['current_page']);
			}
			if (isset($this['actions'])){
				unset($this['actions']);
			}
		}

		if (in_array($this->outputFormat, array('json', 'jsonp', 'xml'), true)) {
			try {
				return parent::doAction($action_name, $params);
			} catch (Exception $e) {
				error_log($e);
				$this['errors'][] = $e->getMessage();
				if (!$this->loadView) {
					return;
				}
				$this->loadView('');
			}
		} else {
			return parent::doAction($action_name, $params);
		}
	}

	//Force rest and json
	static function load($route, array $headers = array(), array $request_params = array()) {
		if (!($route instanceof ControllerRoute)) {
			$route = new ControllerRoute($route, $headers, $request_params);
		}
		$route->setRestful(true);
		$route->setHeaders(array('Accept' => 'application/json'));
		parent::load($route, $headers, $request_params);
	}

}