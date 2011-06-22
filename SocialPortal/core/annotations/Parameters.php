<?php

namespace core\annotations;

use core\debug\Logger;

use core\http\exceptions\InvalidValueForAnnotationException;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// @Parameters(3)
class Parameters extends Annotation implements ValidableInterface {
	/** @var length of parameters */
	private $paramLength;
	
	public function __construct($data) {
		if( isset( $data['value'] ) ) {
			$value = $data['value'];
		} else {
			$resquest = FrontController::getInstance()->getRequest();
			FrontController::getInstance()->generateException( new InvalidValueForAnnotationException( 'Parameters', $resquest->module, $resquest->action ) );
		}
		$this->paramLength = $value;
	}
	
	public function isValid() {
		$front = FrontController::getInstance();
		$parameters = $front->getRequest()->parameters;
		if( ($this->paramLength && !$parameters) || (count( $parameters ) < $this->paramLength) ) {
			Logger::getInstance()->debug( 'The number of parameters is insufficiant' );
			return false;
		}
		return true;
	}
}
