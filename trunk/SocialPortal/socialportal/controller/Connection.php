<?php

namespace socialportal\controller;
use socialportal\common\form\custom\RegisterForm;

use core\user\UserHelper;

use socialportal\common\form\custom\LoginForm;

use core\FrontController;

use core;

use core\AbstractController;
use core\user\UserRoles;

class Connection extends AbstractController {
	public function displayUserPanelAction() {
		$user = $this->frontController->getCurrentUser();
		if( !$user->getId() ) {
			$this->displayLoginFormAction( );
		} else {
			$this->displayUserAction( );
		}
	}
	
	public function displayUserAction() {
		// show the connected user
		$user = $this->frontController->getCurrentUser();
		$this->frontController->getResponse()->setVar( 'user', $user );
		
		$userHelper = new UserHelper( $this->frontController );
		$this->frontController->getResponse()->setVar( 'userHelper', $userHelper );
		
		//TODO remove when the capabilities will be done
		if( $this->frontController->getViewHelper()->currentUserIsAtLeast( UserRoles::$admin_role ) ) {
			$toolLink = $this->frontController->getViewHelper()->createHref( 'Tool', 'index' );
			$this->frontController->getResponse()->setVar( 'toolLink', $toolLink );
		}
		
		$this->frontController->doDisplay( 'connection', 'displayUser' );
	}
	
	public function displayLoginFormAction() {
		$form = new LoginForm( $this->frontController );
		$form->setNonceAction( 'login' );
		$form->setupWithArray();
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Connection', 'login' );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'connection', 'displayLoginForm' );
	}
	
	/**
	 * @Nonce(login)
	 * @Method(POST)
	 */
	public function loginAction() {
		$form = new LoginForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$username = $form->getUsername();
		$password = $form->getPassword();
		$rememberMe = $form->getIsRememberMe();
		
		// redirect to referer or something like that, the page that was ask previously
		$referrer = $this->frontController->getRequest()->getReferrer();
		if( !$referrer ) {
			$referrer = '';
		}
		
		// pass to the user manager, connect
		$user = $this->frontController->getUserManager()->connectUser( $username, $password, $rememberMe );
		if( !$user ) {
			$message = __( 'The username and the password are not in the database' );
			$this->frontController->addMessage( $message, 'error' );
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->setCurrentUser( $user );
		$this->frontController->doRedirectUrl( $referrer );
	}
	
	public function logoutAction() {
		// logout => remove session / cookie
		$this->frontController->getUserManager()->disconnect();
		$this->frontController->doRedirect( 'Home' );
	}
	
	public function displayRegisterFormAction() {
		$form = new RegisterForm( $this->frontController );
		$form->setNonceAction( 'register' );
		$form->setupWithArray();
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Connection', 'register' );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'connection', 'displayRegisterForm' );
	}
	
	/**
	 * TODO debug version to create fake account
	 * @Method(POST)
	 * @Nonce(register)
	 */
	public function registerAction() {
		$form = new RegisterForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$username = $form->getUsername();
		$password = $form->getPassword();
		$email = $form->getEmail();
		
		//		$username = $this->frontController->getRequest()->getPOSTAttribute( 'username', null );
		//		$password = $this->frontController->getRequest()->getPOSTAttribute( 'password', null );
		

		// redirect to referer or something like that, the page that was ask previously
		$referrer = $this->frontController->getRequest()->getReferrer();
		if( !$referrer ) {
			$referrer = '';
		}
		
		// TODO email-activation:: here the point to modify
		$withActivation = false;
		$user = $this->frontController->getUserManager()->registerNewUser( $username, $password, $email, $withActivation );
		if( $user ) {
			$this->frontController->getRequest()->getSession()->setFlash( 'withActivation', $withActivation ? 'true' : 'false' );
			$this->frontController->doRedirect( 'connection', 'registerComplete' );
		} else {
			$message = __( 'The username is already in use' );
			//			$this->frontController->getRequest()->getSession()->setFlash( 'loginError', $message );
			$this->frontController->addMessage( $message );
			$this->frontController->doRedirectUrl( $referrer );
		}
	}
	
	/**
	 * When the registration is success, it explains to the user the fact that he will receive an email with validation link
	 */
	public function registerCompleteAction() {
		$activation = $this->frontController->getRequest()->getSession()->getFlash( 'withActivation', false );
		if( $activation ) {
			$this->frontController->doDisplay( 'connection', 'registerCompleteActivation' );
		} else {
			$this->frontController->doDisplay( 'connection', 'registerCompleteNoActivation' );
		}
	}
	
	/**
	 */
	public function activationAction() {
		//TODO email activation, check with database key etc
	}
}
