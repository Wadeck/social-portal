<?php

namespace core\http\exceptions;

use core\debug\Logger;

class AccessDeniedException extends CustomException{
	public function __construct($controllerName, $methodName){
		parent::__construct(403, __(
			'The current user has not the right to access (%controller_name%)#(%method_name%)',
			array(
				'%controller_name%' => $controllerName,
				'%method_name%' => $methodName
			)
		));
	}
}