<?php

namespace core\annotations;

use core\debug\Logger;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// Could be either @Secured('cap1') or @Secured({'cap1', 'cap2'}) or @Secured(caps = 'cap1') (slower)
class Secured extends Annotation implements ValidableInterface {
	/** @var array of Capabilities (string) */
	public $caps;
	
	public function __construct($data) {
		if( isset( $data['caps'] ) ) {
			$value = $data['caps'];
		} else if( isset( $data['value'] ) ) {
			$value = $data['value'];
		} else {
			$request = FrontController::getInstance()->getRequest();
			throw new exceptions\InvalidValueForAnnotationException( 'Secured', $request->module, $request->action );
		}
		if( !is_array( $value ) ) {
			$value = array( $value );
		}
		$this->caps = $value;
	}
	
	/**
	 * Determine if the current user has the capacities required by the method
	 */
	public function isValid() {
		$user = FrontController::getInstance()->getCurrentUser();
		if( !$user ) {
			Logger::getInstance()->debug( "There is no current user" );
			return false;
		}
		$userCaps = $user->getCapabilities();
		foreach( $this->caps as $requiredCap ) {
			if( in_array( $requiredCap, $userCaps ) ) {
				continue;
			} else {
				Logger::getInstance()->debug( "The current user has not the capacity ($requiredCap) to access this method" );
				return false;
			}
		}
		return true;
	}
}
