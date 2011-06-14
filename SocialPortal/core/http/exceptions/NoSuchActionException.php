<?php

namespace core\http\exceptions;

class NoSuchActionException extends CustomException{
	public function __construct($controllerName, $methodName){
		parent::__construct(404, __(
			'There is no action called (%method_name%) in the controller %controller_name%',
			array(
				'%controller_name%' => $controllerName,
				'%method_name%' => $methodName
			)
		));
	}
}