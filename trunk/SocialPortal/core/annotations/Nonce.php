<?php

namespace core\annotations;

use core\debug\Logger;

use core\http\exceptions\InvalidValueForAnnotationException;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// Could be either @Nonce('nonce_action_name') or @Nonce(bidule_action)
class Nonce extends Annotation implements ValidableInterface {
	/** @var array of Capabilities (string) */
	private $nonceAction;
	
	public function __construct($data) {
		if( isset( $data['value'] ) ) {
			$value = $data['value'];
		} else {
			$resquest = FrontController::getInstance()->getRequest();
			FrontController::getInstance()->generateException( new InvalidValueForAnnotationException( 'Nonce', $resquest->module, $resquest->action ) );
		}
		$this->nonceAction = $value;
	}
	
	public function isValid() {
		$front = FrontController::getInstance();
		$nonce = $front->getCurrentNonce();
		
		if( null === $nonce ) {
			Logger::getInstance()->debug( 'The nonce is not present' );
			return false;
		}
		$result = $front->getNonceManager()->verifyNonce( $nonce, $this->nonceAction );
		if( $result > 0 ) {
			return true;
		} else {
			Logger::getInstance()->debug( "The nonce is not valid: $nonce" );
			return false;
		}
	}
}
