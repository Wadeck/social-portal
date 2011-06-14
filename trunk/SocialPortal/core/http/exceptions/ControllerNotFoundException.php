<?php

namespace core\http\exceptions;

class ControllerNotFoundException extends CustomException{
	public function __construct($controllerName){
		parent::__construct(404, __(
			'The controller %controller_name% does not exist',
			array(
				'%controller_name%' => $controllerName,
			)
		));
	}
}