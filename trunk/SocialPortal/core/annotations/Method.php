<?php

namespace core\annotations;

use core\debug\Logger;

use core\http\exceptions\InvalidValueForAnnotationException;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// Could be either @Method('get') or @Method({'get', 'post'}) or even @Method(GeT)
class Method extends Annotation implements ValidableInterface {
	/** @var array of methods (string) */
	private $methods;
	
	public function __construct($data) {
		if( isset( $data['value'] ) ) {
			$value = $data['value'];
		} else {
			$resquest = FrontController::getInstance()->getRequest();
			FrontController::getInstance()->generateException( new InvalidValueForAnnotationException( 'Method', $resquest->module, $resquest->action ) );
		}
		if( !is_array( $value ) ) {
			$value = array( $value );
		}
		// seems to be the faster way to do that according to http://lixlpixel.org/php-benchmarks/array-values-to-uppercase/
		// even if the benchmarks are done with very small number of iteration
		$value = explode( '§', strtoupper( implode( '§', $value ) ) );
		$this->methods = $value;
	}
	
	/**
	 * Determine if the used request method is allowed for this function/object
	 */
	public function isValid() {
		$method = FrontController::getInstance()->getRequest()->getMethod();
		$method = strtoupper( $method );
		if( in_array( $method, $this->methods ) ) {
			return true;
		} else {
			Logger::getInstance()->debug( 'The method is not accepted' );
			return false;
		}
	}
}
