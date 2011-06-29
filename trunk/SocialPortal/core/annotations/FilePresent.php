<?php

namespace core\annotations;

use core\debug\Logger;

use core\http\exceptions\InvalidValueForAnnotationException;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// @FilePresent
class FilePresent extends Annotation implements ValidableInterface {
	
	public function __construct($data) {
	}
	
	public function isValid() {
		if(empty( $_FILES )){
			Logger::getInstance()->debug_var( 'There is no file attached with that request' );
			return false;
		}
		return true;
	}
}
