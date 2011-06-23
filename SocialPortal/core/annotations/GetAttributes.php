<?php

namespace core\annotations;

use core\debug\Logger;

use core\http\exceptions\InvalidValueForAnnotationException;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// @GetAttributes({bidule, truc, machin}) or for single item @GetAttributes(topidId)
class GetAttributes extends Annotation implements ValidableInterface {
	/** list of key that need to be passed as get attributes, not counting _nonce */
	private $attributes;
	
	public function __construct($data) {
		if( isset( $data['value'] ) ) {
			$value = $data['value'];
		} else {
			$request = FrontController::getInstance()->getRequest();
			FrontController::getInstance()->generateException( new InvalidValueForAnnotationException( 'GetAttributes', $request->module, $request->action ) );
		}
		if( !is_array( $value ) ) {
			$value = array( $value );
		}
		$this->attributes = $value;
	}
	
	public function isValid() {
		$front = FrontController::getInstance();
		$query = $front->getRequest()->query->all();
		$query = array_keys( $query );
		$emptyArray = array_diff( $this->attributes, $query );
		if( $this->attributes && count( $emptyArray ) > 0 ) {
			Logger::getInstance()->debug_var( 'Some get attributes are missing', $emptyArray );
			return false;
		}
		return true;
	}
}
