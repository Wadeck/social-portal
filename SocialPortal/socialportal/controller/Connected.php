<?php

namespace socialportal\controller;
use core\security\Crypto;

use core\debug\Logger;

use core\tools\Utils;

use core\Config;

use core\tools\Mail;

use socialportal\common\form\custom\LostChangePasswordForm;

use socialportal\common\form\custom\LostPasswordForm;

use socialportal\common\form\custom\LostUsernameForm;

use socialportal\common\form\custom\RegisterForm;

use core\user\UserHelper;

use socialportal\common\form\custom\LoginForm;

use core\FrontController;

use core;

use core\AbstractController;
use core\user\UserRoles;

class Connected extends AbstractController {
	public function displayUserPanelAction() {
		$user = $this->frontController->getCurrentUser();
		if( !$user->getId() ) {
			$this->displayAnonymousAction();
		} else {
			$this->displayUserAction( );
		}
	}
	
	public function displayUserAction() {
		// show the connected user
		$user = $this->frontController->getCurrentUser();
		$this->frontController->getResponse()->setVar( 'user', $user );
		
		$userHelper = new UserHelper( $this->frontController );
		$userHelper->setCurrentUser($user);
		$this->frontController->getResponse()->setVar( 'userHelper', $userHelper );
		
		if( $this->frontController->getViewHelper()->currentUserIsAtLeast( UserRoles::$admin_role ) ) {
			$toolLink = $this->frontController->getViewHelper()->createHref( 'Tool', 'index' );
			$this->frontController->getResponse()->setVar( 'toolLink', $toolLink );
		}
		
		$this->frontController->doDisplay( 'connected', 'displayUser' );
	}
	
	public function displayAnonymousAction() {
		// show the anonymous user
		$userHelper = new UserHelper( $this->frontController );
		$userHelper->setCurrentUserAnonymous();
		
		$this->frontController->getResponse()->setVar( 'userHelper', $userHelper );
		
		$this->frontController->doDisplay( 'connected', 'displayAnonymous' );
	}
	
	/**
	 * @RoleAtLeast(fullUser)
	 */
	public function logoutAction() {
		// logout => remove session / cookie
		$this->frontController->getUserManager()->disconnect();
		$this->frontController->doRedirect( 'Connection' );
	}
}
