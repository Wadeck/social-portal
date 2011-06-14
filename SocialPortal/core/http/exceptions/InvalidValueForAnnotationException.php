<?php

namespace core\http\exceptions;

class InvalidValueForAnnotationException extends CustomException{
	public function __construct($annotationName, $controllerName, $actionName){
		parent::__construct(500, __(
			'There is an invalid annotation (%annotation_name%) in the controller (%controller_name%)#(%action_name%)',
			array(
				'%annotation_name%' => $annotationName,
				'%controller_name%' => $controllerName,
				'%action_name%' => $actionName,
			)
		));
	}
}