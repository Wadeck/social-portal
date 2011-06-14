<?php

namespace core\http\exceptions;

class PageNotFoundException extends CustomException{
	public function __construct($moduleName, $actionName=''){
		parent::__construct(404, __(
			'There is not page with name (%module_name% / %action_name%)',
			array(
				'%module_name%' => $moduleName,
				'%action_name%' => $actionName
			)
		));
	}
}