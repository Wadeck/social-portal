<?php

namespace core\http\exceptions;

class InvalidControllerClassException extends CustomException{
	public function __construct($controllerName){
		parent::__construct(404, __(
			'The controller %controller_name% does not implement the interface AbstractController',
			array(
				'%controller_name%' => $controllerName,
			)
		));
	}
}