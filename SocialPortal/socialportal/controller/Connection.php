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

class Connection extends AbstractController {
	public function indexAction(){
		if($this->frontController->getCurrentUser()->getId()){
			$this->frontController->doRedirect('Home');
		}
		
		$loginForm = new LoginForm($this->frontController);
		$loginForm->setNonceAction( 'login' );
		$loginForm->setupWithArray();
		$loginUrl = $this->frontController->getViewHelper()->createHref( 'Connection', 'login' );
		$loginForm->setTargetUrl( $loginUrl );
		
		$response = $this->frontController->getResponse();
		$response->setVar('loginForm', $loginForm);
		$this->frontController->doDisplay('connection', 'index');
	}
	
	/**
	 * @RoleEquals(anonymous)
	 */
	public function displayUserPanelAction() {
		$user = $this->frontController->getCurrentUser();
		if( !$user->getId() ) {
			$this->displayAnonymousAction();
		} else {
			$this->displayUserAction( );
		}
	}
	
	/**
	 * @RoleEquals(anonymous)
	 */
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
		
		$this->frontController->doDisplay( 'connection', 'displayUser' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 */
	public function displayAnonymousAction() {
		// show the anonymous user
		$userHelper = new UserHelper( $this->frontController );
		$userHelper->setCurrentUserAnonymous();
		
		$this->frontController->getResponse()->setVar( 'userHelper', $userHelper );
		
		$this->frontController->doDisplay( 'connection', 'displayAnonymous' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 */
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
	 * @RoleEquals(anonymous)
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
		
		// pass to the user manager, connect
		$user = $this->frontController->getUserManager()->connectUser( $username, $password, $rememberMe );
		if( !$user ) {
			// redirect to referer or something like that, the page that was ask previously
			$referrer = $this->frontController->getRequest()->getReferrer();
			if( !$referrer ) {
				$referrer = '';
			}
			$this->frontController->addMessage( __( 'The username and the password are not in the database' ), 'error' );
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->setCurrentUser( $user );
		$this->frontController->doRedirect( 'Home' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 */
	public function logAsVisitorAction(){
		// nothing special
		$this->frontController->doRedirect('Home');
	}
	
	/**
	 * @RoleEquals(anonymous)
	 * @Nonce(displayLostUsernameForm)
	 */
	public function displayLostUsernameFormAction() {
		$form = new LostUsernameForm($this->frontController );
		$form->setNonceAction( 'lostUsername' );
		$form->setupWithArray();
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Connection', 'lostUsername' );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'connection', 'displayLostUsernameForm' );
	}
	

	/**
	 * @RoleEquals(anonymous)
	 * @Nonce(lostUsername)
	 */
	public function lostUsernameAction(){
		$form = new LostUsernameForm($this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$email = $form->getEmail();
		// check in the database if the email exist, if yes we send an email, if no, we don't do anything
		// but the message to the user is the same, "email sent" to avoid spamming issues
		$userRepo = $this->em->getRepository('User');
		$user = $userRepo->findUserByEmail($email);
		if( null !== $user ){
			$username = $user->getUsername();
			
			$instrRepo = $this->em->getRepository('Instruction');
			$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'username_lost');
			$mailContent = $instruction->getInstructions();
			$mailContent = strtr($mailContent, array( '%username%' => $username ) );
			
			$mailContent = nl2br($mailContent);
			Mail::sendHtml($email, Config::getOrDie('site_display_name'). ': username lost', $mailContent);
		}
		
		$this->frontController->addMessage( __( 'Email sent, you will receive an email containing your username' ), 'correct' );
		$this->frontController->doRedirect( 'Connection' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 * @Nonce(displayLostPasswordForm)
	 */
	public function displayLostPasswordFormAction() {
		$form = new LostPasswordForm( $this->frontController );
		$form->setNonceAction( 'lostPassword' );
		$form->setupWithArray();
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Connection', 'lostPassword' );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'connection', 'displayLostPasswordForm' );
	}

	/**
	 * @RoleEquals(anonymous)
	 * @Nonce(lostPassword)
	 */
	public function lostPasswordAction(){
		$form = new LostPasswordForm($this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$username = $form->getUsername();
		$email = $form->getEmail();
		// check in the database if the email exist, if yes we send an email, if no, we don't do anything
		// but the message to the user is the same, "email sent" to avoid spamming issues
		$userRepo = $this->em->getRepository('User');
		$user = $userRepo->findUserByUsernameAndEmail($username, $email);
		if( null === $user ){
			$this->frontController->addMessage( __( 'There is no account with this combination of username/email' ), 'error' );
			$this->frontController->doRedirectWithNonce('displayLostPasswordForm', 'Connection', 'displayLostPasswordForm' );
		}
		$userId = $user->getId();
		
		$expiration = Config::get('lost_password_expiration_time', 48 * 60 * 60);
		$meta = array('userId' => $userId);
		$token = $this->em->getRepository('Token')->createValidToken($meta, 'lost_password', $expiration);
		
		if( null === $token ){
			Logger::getInstance()->log('Problem during creation of token in password change, number of possible attempts reach');
			$this->frontController->addMessage( __( 'Internal error, please retry in a moment' ), 'correct' );
			$this->frontController->doRedirectWithNonce('displayLostPasswordForm', 'Connection', 'displayLostPasswordForm' );
		}
		
		$resetLink = $this->frontController->getViewHelper()->createHref('Connection', 'displayLostChangePasswordForm', array( 'token' => $token->getToken() ));
		$resetLink = Utils::getBaseUrlWithoutName() . $resetLink;
		$resetLink = '<a href="' . $resetLink . '" title="' . __('Change password link') . '">'. __('Click here to change your password') .'</a>';
	
		$instrRepo = $this->em->getRepository('Instruction');
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'password_lost');
		$mailContent = $instruction->getInstructions();
		$mailContent = strtr($mailContent, array( '%change_password_link%' => $resetLink ) );
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($email, Config::getOrDie('site_display_name'). ': password change', $mailContent);
		$this->frontController->addMessage( __( 'Email sent, you will receive an email containing a link to a page where you will be able to modify your password' ), 'correct' );
		$this->frontController->doRedirect( 'Connection' );
		
	}
	
	/**
	 * @GetAttributes(token)
	 */
	public function displayLostChangePasswordFormAction() {
		$get = $this->frontController->getRequest()->query;
		$token = $get->get( 'token' );
		
		// use old_email, new_email and user_id to send both mails
		$tokenRepo = $this->em->getRepository('Token');
		$tokenMeta = $tokenRepo->findValidTokenMeta($token, 'lost_password');
		if( false === $tokenMeta){
			// expiration or never exist
			Logger::getInstance()->log("Request expired: [$token]");
			$this->frontController->addMessage( __( 'Your request has expired' ), 'error' );
			$this->frontController->doRedirect( 'Connection' );
		}
		$userId = $tokenMeta['userId'];
		
		// if the user is connected, we redirect him to the homepage
		if($this->frontController->getCurrentUser()->getId()){
			$this->frontController->addMessage( __( 'You are not supposed to be here, being connected. So you are redirected to the Home page' ), 'error' );
			$this->frontController->doRedirect( 'Home' );
		}
		
		$form = new LostChangePasswordForm( $this->frontController );
		$form->setNonceAction( 'lostChangePassword' );
		$form->setupWithArray();
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Connection', 'lostChangePassword', array( 'userId' => $userId ) );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'connection', 'displayLostChangePasswordForm' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 * @Nonce(lostChangePassword)
	 * @GetAttributes(userId)
	 */
	public function lostChangePasswordAction(){
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new LostChangePasswordForm($this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$newPassword = $form->getNewPassword();
		// check in the database if the email exist, if yes we send an email, if no, we don't do anything
		// but the message to the user is the same, "email sent" to avoid spamming issues
		$user = $this->em->find('User', $userId);
		if( null !== $user ){
			$username = $user->getUsername();
			$email = $user->getEmail();
			$passwordHint = Utils::getPasswordHint($newPassword);
		
			$newPassword = Crypto::encodeDBPassword($user->getRandomKey(), $newPassword);
			
			$user->setPassword($newPassword);
			$this->em->persist($user);
			if( !$this->em->flushSafe() ){
				$this->frontController->addMessage( __( 'The password change failed' ), 'correct' );
				$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
			}
			
			$instrRepo = $this->em->getRepository('Instruction');
			$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'password_lost_reset');
			$mailContent = $instruction->getInstructions();
			$mailContent = strtr($mailContent, array( '%username%' => $username, '%password_hint%' => $passwordHint ) );
			
			$mailContent = nl2br($mailContent);
			Mail::sendHtml($email, Config::getOrDie('site_display_name'). ': username lost', $mailContent);
		}
		
		$this->frontController->addMessage( __( 'Email sent, you will receive an email containing your information' ), 'correct' );
		$this->frontController->doRedirect( 'Connection' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 */
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
	 * @RoleEquals(anonymous)
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
		$activationKey = $form->getActivationKey();
		
		// test if the token is correct
		$tokenRepo = $this->em->getRepository('Token');
		$tokenMeta = $tokenRepo->findValidTokenMeta($activationKey, 'register');
		if( false === $tokenMeta){
			// expiration or never exist
			Logger::getInstance()->log("Request expired: [$activationKey]");
			$this->frontController->addMessage( __( 'Your key has expired' ), 'error' );
			$this->frontController->doRedirect( 'Connection' );
		}
		
		// we check if the key is already used AFTER we check it is usable, because the keys are removed only when
		// the account is validated, so there is a certain time during which the key is still in database but not 
		// really used
		if( $this->frontController->getUserManager()->isKeyAlreadyUsed($activationKey) ){
			// the key is already used to activate an account
			$this->frontController->addMessage( __( 'Your key is already used' ), 'error' );
			$this->frontController->doRedirect( 'Connection' );
		}
		
		// if yes, we check the role that is gained by the user
		$role = $tokenMeta['role'];
		// role must be either UserRoles::$moderator_role or UserRoles::$full_user_role
		if( UserRoles::$moderator_role === $role ){
			Logger::getInstance()->log('Register of a new moderator');
		}else if( UserRoles::$full_user_role === $role ){
			Logger::getInstance()->log('Register of a new full user');
		}else{
			$this->frontController->addMessage( __('The role linked with this key is not valid'), 'error');
			$this->frontController->doRedirect( 'Connection' );
		}
		
		// create a user that is pending
		$user = $this->frontController->getUserManager()->registerNewUser( $username, $password, $email, true, $role, $activationKey );
		if( false === $user ) {
			$this->frontController->addMessage( __( 'The username is already in use' ), 'error' );
			$this->frontController->doRedirect( 'Connection', 'displayRegisterForm' );
		}
		$userId = $user->getId();
		
		$expiration = Config::get('account_email_validation_expiration_time', 48 * 60 * 60);
		$meta = array( 'key' => $activationKey, 'userId' => $userId );
		$token = $this->em->getRepository('Token')->createValidToken($meta, 'account_validation', $expiration);
		
		if( null === $token ){
			Logger::getInstance()->log('Problem during creation of token in password change, number of possible attempts reach');
			$this->frontController->addMessage( __( 'Internal error, please retry in a moment' ), 'correct' );
			$this->frontController->doRedirect( 'Connection' );
		}
		
		$validationLink = $this->frontController->getViewHelper()->createHref('Connection', 'validRegister', array( 'token' => $token->getToken() ));
		$validationLink = Utils::getBaseUrlWithoutName() . $validationLink;
		$validationLink = '<a href="' . $validationLink . '" title="' . __('Valid your account') . '">'. __('Click here to valid your account') .'</a>';
	
		$instrRepo = $this->em->getRepository('Instruction');
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'validation_account');
		$mailContent = $instruction->getInstructions();
		$mailContent = strtr($mailContent, array( '%validation_link%' => $validationLink ) );
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($email, Config::getOrDie('site_display_name'). ': account validation', $mailContent);	
			
		$this->frontController->addMessage( __('An email to validate your address was sent, when you will click on the link inside it, the account will be activated'), 'correct');
		$this->frontController->doRedirect( 'Connection' );
	}
	
	/**
	 * @RoleEquals(anonymous)
	 * @GetAttributes(token)
	 * Token contains userId and key
	 * When the registration is success, it explains to the user the fact that he will receive an email with validation link
	 */
	public function validRegisterAction() {
		$get = $this->frontController->getRequest()->query;
		$token = $get->get( 'token' );
		
		$tokenRepo = $this->em->getRepository('Token');
		$tokenMeta = $tokenRepo->findValidTokenMeta($token, 'account_validation');
		if( false === $tokenMeta){
			// expiration or never exist
			Logger::getInstance()->log("Request expired: [$token]");
			$this->frontController->addMessage( __( 'Your request has expired' ), 'error' );
			$this->frontController->doRedirect( 'Connection' );
		}
		
		$userId = $tokenMeta['userId'];
		$activationKey = $tokenMeta['key'];
		
		// set user status to 0;
		$user = $this->em->find('User', $userId);
		if( 0 === $user->getStatus() ){
			$this->frontController->addMessage( __( 'The account was already activated !' ), 'error' );
			$this->frontController->doRedirect( 'Connection' );
		}
		$user->setStatus(0);
		$this->em->persist($user);
		if( !$this->em->flushSafe() ){
			$this->frontController->addMessage( __( 'There was a problem during activation of the account' ), 'error' );
			$this->frontController->doRedirect( 'Connection' );
		}
		
		// here the account is activated, so we can remove the both token used
		$activationToken = $tokenRepo->findValidToken($activationKey);
		$emailToken = $tokenRepo->findValidToken($token);
		$this->em->remove($activationToken);
		$this->em->remove($emailToken);
		if( !$this->em->flushSafe() ){
			Logger::getInstance()->log('At least one token (activation or email) was not correctly deleted, normally it cannot be already deleted automatically, but perhaps manually, please check the database');
			Logger::getInstance()->log_var('activation token', $activationToken);
			Logger::getInstance()->log_var('email token', $emailToken);
		}
		
		// send mail to inform the client that is account is activated
		$email = $user->getEmail();
		$instrRepo = $this->em->getRepository('Instruction');
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'account_validated');
		$mailContent = $instruction->getInstructions();
//		$mailContent = strtr($mailContent, array( '%validation_link%' => $validationLink ) );
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($email, Config::getOrDie('site_display_name'). ': account validated', $mailContent);	
		
		$this->frontController->getUserManager()->connectUserByUserId($userId);
		
		$this->frontController->addMessage( __( 'Congratulations, you are connected to the site for the first time !' ), 'correct' );
		$this->frontController->doRedirect( 'Connection' );
	}
}
