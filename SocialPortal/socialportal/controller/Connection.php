<?php

namespace socialportal\controller;
use core\FrontController;

use core;

use core\AbstractController;

class Connection extends AbstractController {
	public function displayUserAction($parameters) {
		// show the connected user
		$user = $this->frontController->getCurrentUser();
		$this->frontController->getResponse()->setVar( 'user', $user );
		$this->frontController->doDisplay( 'connection', 'displayUser' );
	}
	
	public function displayLoginFormAction($parameters) {
		$error = $this->frontController->getRequest()->getSession()->getFlash( 'loginError', null );
		if( $error ) {
			$this->frontController->getResponse()->setVar( 'error', $error );
		}
		//TODO implement the form + error handling
		$this->frontController->doDisplay( 'connection', 'displayLoginForm' );
	}
	
	/**
	 * @Method(POST)
	 */
	public function loginAction($parameters) {
		$username = $this->frontController->getRequest()->getPOSTAttribute( 'username', null );
		$password = $this->frontController->getRequest()->getPOSTAttribute( 'password', null );
		$rememberMe = $this->frontController->getRequest()->getPOSTAttribute( 'rememberMe', false );
		
		// redirect to referer or something like that, the page that was ask previously
		$referrer = $this->frontController->getRequest()->getReferrer();
		if( !$referrer ) {
			$referrer = '';
		}
		
		if( !$username || !$password ) {
			$message = __( 'The username or the password is missing' );
			$this->frontController->getRequest()->getSession()->setFlash( 'loginError', $message );
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		// pass to the user manager, connect
		$user = $this->frontController->getUserManager()->connectUser( $username, $password, $rememberMe );
		if( !$user ) {
			$message = __( 'The username and the password are not in the database' );
			$this->frontController->getRequest()->getSession()->setFlash( 'loginError', $message );
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->setCurrentUser( $user );
		$this->frontController->doRedirectUrl( $referrer );
	}
	
	public function logoutAction($parameters) {
		// logout => remove session / cookie
		$this->frontController->getUserManager()->disconnect();
		$this->frontController->doRedirect( 'home', 'index' );
	}
	
	public function displayRegisterFormAction($parameters) {
		$this->frontController->doDisplay( 'connection', 'displayRegisterForm' );
	}
	
	/**
	 * TODO debug version to create fake account
	 * @Method(POST)
	 */
	public function registerAction($parameters) {
		$username = $this->frontController->getRequest()->getPOSTAttribute( 'username', null );
		$password = $this->frontController->getRequest()->getPOSTAttribute( 'password', null );
		
		// redirect to referer or something like that, the page that was ask previously
		$referrer = $this->frontController->getRequest()->getReferrer();
		if( !$referrer ) {
			$referrer = '';
		}
		
		if( !$username || !$password ) {
			$message = __( 'The username or the password is missing' );
			$this->frontController->addMessage( $message );
			$this->frontController->doRedirectUrl( $referrer );
		}
		$withActivation = false;
		//TODO modify this to receive email by POST
		$user = $this->frontController->getUserManager()->registerNewUser( $username, $password, 'no@email', $withActivation );
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
	public function registerCompleteAction($parameters) {
		$activation = $this->frontController->getRequest()->getSession()->getFlash( 'withActivation', false );
		if( $activation ) {
			$this->frontController->doDisplay( 'connection', 'registerCompleteActivation' );
		} else {
			$this->frontController->doDisplay( 'connection', 'registerCompleteNoActivation' );
		}
	}
	
	/**
	 * @Method(GET)
	 */
	public function activationAction($parameters) {
		//TODO email activation, check with database key etc
	}
}
