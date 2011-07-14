<?php

namespace core\annotations;

use core\user\UserRoles;

use core\debug\Logger;

use core\http\exceptions\InvalidValueForAnnotationException;

use core\FrontController;

use core\http\exceptions;

use Doctrine\Common\Annotations\Annotation;

// @RoleAtLeast(role) with role in admin|administrator, moderator|modo, fullUser|full_user|user, anon|anonymous without care of letter cases
class RoleAtLeast extends Annotation implements ValidableInterface {
	/** list of key that need to be passed as get attributes, not counting _nonce */
	private $role;
	
	public function __construct($data) {
		if( isset( $data['value'] ) ) {
			$value = $data['value'];
		} else {
			$request = FrontController::getInstance()->getRequest();
			FrontController::getInstance()->generateException( new InvalidValueForAnnotationException( 'GetAttributes', $request->module, $request->action ) );
		}
		if(is_numeric($value)){
			$value = intval($value);
		}
		$value = UserRoles::stringToInt($value);
		$this->role = $value;
	}
	
	public function isValid() {
		$front = FrontController::getInstance();
		$viewHelper = $front->getViewHelper();
		if( $viewHelper->currentUserIsAtLeast($this->role) ){
			return true;
		}else{
			Logger::getInstance()->debug_var( "The user has not the sufficiant role to access this method, role required [{$this->role}]" );
			return false;
		}
	}
}
