<?php

namespace core\http\exceptions;

use core\debug\Logger;

class InvalidMethodException extends CustomException {
	public function __construct($controllerName, $methodName, $methodUsed) {
		parent::__construct( 403, __( 'The action (%controller_name%)#(%method_name%) does not accept a request with method=(%method_use%)', array( '%controller_name%' => $controllerName, '%method_name%' => $methodName, '%method_use%' => $methodUsed ) ) );
	}
}