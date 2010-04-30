<?php

/**
 * @param string $route
 */
function load_controller($route){
	$render_partial = false;
	$route = str_replace('\\', '/', $route);
	$route = trim($route, '/');

	$params = explode('/', $route);

	if(@$params[0]=='partial'){
		$render_partial=true;
		array_shift($params);
	}
	if(!$params)$params[] = DEFAULT_CONTROLLER;

	$last = array_pop($params);
	$extension = "html";
	if($last!==null){
		$file_parts = explode('.', $last);
		if(count($file_parts) > 1)
			$extension = array_pop($file_parts);
		$params[] = implode('.', $file_parts);
	}

	// directory where controllers are found
	$c_dir = ROOT.'controllers'.DIRECTORY_SEPARATOR;
	$view_prefix = '';
	$view_dir = '';
	$instance = null;

	while ($segment = array_shift($params)) {
		$view_dir = strtolower($segment);
		$c_class = str_replace(array('_', '-'), ' ', $segment);
		$c_class = ucwords($c_class);
		$c_class = str_replace(' ', '', $c_class).'Controller';
		$c_class_file = $c_dir.$c_class.'.php';

		//check if file exists
		if(is_file($c_class_file)){
			require_once $c_class_file;
			$instance = new $c_class;
			break;
		}

		//check if the segment matches directory name
		$t_dir = $c_dir.$segment.DIRECTORY_SEPARATOR;
		if(is_dir($t_dir)){
			$c_dir = $t_dir;
			//if there are no segments left, and we continue, then we'll never load anything
			//so only continue the loop if there is more to loop through
			if($params){
				$view_prefix .= $segment.DIRECTORY_SEPARATOR;
				continue;
			}
		}

	}

	if (!$instance) {
		//fallback check if default index exists in directory
		$alternate_c_class = ucwords(DEFAULT_CONTROLLER).'Controller';
		$alternate_c_class_file = $c_dir.$alternate_c_class.'.php';
		if(is_file($alternate_c_class_file)){
			if ($params) {
				array_unshift($params, $segment);
				$view_dir = '';
			}
			require_once $alternate_c_class_file;
			$instance = new $alternate_c_class;
		}
	}

	//if no instance of a Controller, 404
	if(!$instance)
		file_not_found($route);

	$action = $params ? array_shift($params) : DEFAULT_CONTROLLER;

	if(!$instance->viewPrefix)
		$instance->viewPrefix = $view_prefix;

	if(!$instance->viewDir)
		$instance->viewDir = $view_dir;

	if($render_partial)
		$instance->renderPartial = $render_partial;

	$instance->outputFormat = $extension;

	//Restore Flash params
	$instance->setParams(
		array_merge_recursive(
			get_clean_persistant_values(),
			$instance->getParams(),
			Application::getParams()
	));

	$instance->doAction($action, $params);
}
