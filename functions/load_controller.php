<?php

/**
 * @param string $route
 */
function load_controller($route){
	$render_partial = false;
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

	$c_dir = ROOT.'controllers'.DIRECTORY_SEPARATOR;
	$view_prefix = '';
	$view_dir = '';
	$instance = null;

	foreach($params as $key => $segment){
		$view_dir = strtolower($segment);
		$c_class = str_replace(array('_', '-'), ' ', $segment);
		$c_class = ucwords($c_class);
		$c_class = str_replace(' ', '', $c_class).'Controller';
		$c_class_file = $c_dir.$c_class.'.php';

		//check if file exists
		if(file_exists($c_class_file)){
			require_once $c_class_file;
			unset($params[$key]);
			$instance = new $c_class;
			break;
		}

		//check if the segment matches directory name
		$c_dir .= $segment.DIRECTORY_SEPARATOR;
		if(is_dir($c_dir)){
			unset($params[$key]);
			//if there are no segments left, and we continue, then we'll never load anything
			//so only continue the loop if there is more to loop through
			if($params){
				$view_prefix .= $segment.DIRECTORY_SEPARATOR;
				continue;
			}
		}

		//fallback check if default index exists in directory
		$alternate_c_class = ucwords(DEFAULT_CONTROLLER).'Controller';
		$alternate_c_class_file = $c_dir.$alternate_c_class.'.php';
		if(file_exists($alternate_c_class_file)){
			require_once $alternate_c_class_file;
			$instance = new $alternate_c_class;
			break;
		}

		//we tried...but 404
		file_not_found($route);
	}

	$action = $params ? array_shift($params) : DEFAULT_CONTROLLER;
	$instance->view_prefix = $view_prefix;
	$instance->view_dir = $view_dir;
	$instance->output_format = $extension;
	$instance->render_partial = $render_partial;
	$instance->doAction($action, $params);
}