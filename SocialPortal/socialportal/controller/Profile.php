<?php

namespace socialportal\controller;
use core\tools\ImageUtils;

use socialportal\model\Token;

use core\tools\Mail;

use core\security\Crypto;

use socialportal\common\form\custom\ProfileEditEmailForm;

use socialportal\common\form\custom\ProfileEditPasswordForm;

use socialportal\common\form\custom\ProfileEditUsernameForm;

use core\debug\Logger;

use core\Config;

use socialportal\model\User;

use core\tools\Utils;

use socialportal\common\templates\ProfileTemplate;

use socialportal\common\form\custom\ProfileForm;

use socialportal\model\UserProfile;

use socialportal\common\form\custom\RegisterForm;

use core\user\UserHelper;

use socialportal\common\form\custom\LoginForm;

use core\FrontController;

use core;

use core\AbstractController;
use core\user\UserRoles;
use DateTime;

class Profile extends AbstractController {
	/**
	 * @Nonce(displayProfile)
	 * @GetAttributes(userId)
	 */
	public function displayAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$profileRepo = $this->em->getRepository( 'UserProfile' );
		$profile = $profileRepo->findByUserId( $userId );
		$userRepo = $this->em->getRepository( 'User' );
		$user = $userRepo->find( $userId );
		
		$profileTemplate = new ProfileTemplate( $this->frontController, $user, $profile );
		
		//		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'user', $user );
		$this->frontController->getResponse()->setVar( 'profileTemplate', $profileTemplate );
		$this->frontController->doDisplay( 'profile', 'displayProfile' );
	}
	
	/**
	 * @Nonce(displayEditProfile)
	 * @GetAttributes(userId)
	 */
	public function displayEditProfileFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$profileRepo = $this->em->getRepository( 'UserProfile' );
		$profile = $profileRepo->findByUserId( $userId );
		
		$form = new ProfileForm( $this->frontController );
		
		$getArgs = array( 'userId' => $userId );
		
		if( null !== $profile ) {
			$form->setupWithProfile( $profile );
			$form->setNonceAction( 'editProfile' );
			$module = 'editProfile';
		} else {
			$form->setNonceAction( 'createProfile' );
			$module = 'createProfile';
		}
		
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Profile', $module, $getArgs );
		
		// fill the form with the posted field and errors
		$form->setupWithArray();
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'profile', 'displayProfileForm' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createProfile)
	 * @GetAttributes(userId)
	 */
	public function createProfileAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$now =  $this->frontController->getRequest()->getRequestDateTime();

		$profile = $form->createProfile();
		$profile->setUserId( $userId );
		$profile->setLastModified( $now );
		
		$this->em->persist( $profile );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Profile edition fail, please try again in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'Profile edition success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editProfile)
	 * @GetAttributes(userId)
	 */
	public function editProfileAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$profileRepo = $this->em->getRepository( 'UserProfile' );
		$existingProfile = $profileRepo->findByUserId( $userId );
		if( null === $existingProfile ) {
			$this->frontController->addMessage( __( 'There was an internal error, this is not an edition but a creation' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$form = new ProfileForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$profile = $form->createProfile( $existingProfile );
		$now = $this->frontController->getRequest()->getRequestDateTime();
		$profile->setLastModified( $now );
		
		$this->em->persist( $profile );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Profile edition fail, please try again in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'Profile edition success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	}

	/**
	 * @Nonce(displayEditUsernameForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditUsernameFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileEditUsernameForm($this->frontController);
		$form->setupWithArray();
		$form->setNonceAction( 'editUsername' );
		
		$targetUrl = $this->frontController->getViewHelper()->createHref( 'Profile', 'editUsername', array('userId' => $userId) );
		
		$form->setTargetUrl($targetUrl);
		$this->frontController->getResponse()->setVar('userId', $userId);
		$this->frontController->getResponse()->setVar('form', $form);
		$this->frontController->doDisplay('profile', 'displayEditUsernameForm');
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editUsername)
	 * @GetAttributes(userId)
	 */
	public function editUsernameAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileEditUsernameForm($this->frontController);
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$oldUsername = $form->getOldUsername();
		$newUsername = $form->getNewUsername();
		$password = $form->getPassword();
		$user = $this->frontController->getUserManager()->getUser($oldUsername, $password);
		
		if(null === $user || $user->getId() !== $userId ){
			$this->frontController->doRedirectToReferrer( __( 'The username / password are not related to the user' ), 'error' );
		}
		
		$user->setUsername($newUsername);
		$this->em->persist($user);
		if( !$this->em->flushSafe() ){
			$this->frontController->doRedirectToReferrer( __( 'This username is already taken, please choose another one' ), 'error' );
		}
		
		$instrRepo = $this->em->getRepository('Instruction');
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'username_change');
		$mailContent = $instruction->getInstructions();
		$mailContent = strtr($mailContent, array( '%new_username%' => $newUsername ) );
		
		$userMail = $user->getEmail();
		Mail::send($userMail, Config::getOrDie('site_display_name'). ': username edition', $mailContent);
		
		$this->frontController->addMessage( __( 'Edition of the username success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @Nonce(displayEditEmailForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditEmailFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileEditEmailForm($this->frontController);
		$form->setupWithArray();
		$form->setNonceAction( 'editEmail' );
		
		$targetUrl = $this->frontController->getViewHelper()->createHref( 'Profile', 'editEmail', array('userId' => $userId) );
		
		$form->setTargetUrl($targetUrl);
		$this->frontController->getResponse()->setVar('userId', $userId);
		$this->frontController->getResponse()->setVar('form', $form);
		$this->frontController->doDisplay('profile', 'displayEditEmailForm');
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editEmail)
	 * @GetAttributes(userId)
	 * Receive the edit email form, retrieve the information and send email to validate
	 */
	public function editEmailAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileEditEmailForm($this->frontController);
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$username = $form->getUsername();
		$password = $form->getPassword();
		$newEmail = $form->getEmail();
		$user = $this->frontController->getUserManager()->getUser($username, $password);
		
		if(null === $user || $user->getId() !== $userId ){
			$this->frontController->doRedirectToReferrer( __( 'The username / password are not related to the user' ), 'error' );
		}
		
		// we create a token that contains old_email, new_email, user_id
		$oldEmail = $user->getEmail();
		if($oldEmail === $newEmail){
			$this->frontController->doRedirectToReferrer( __( 'You cannot change the email to the same one' ), 'error' );
		}
		
		$expiration = Config::get('email_change_validation_expiration_time', 48 * 60 * 60);
		$meta = array('oldEmail' => $oldEmail, 'newEmail' => $newEmail, 'userId' => $userId);
		$token = $this->em->getRepository('Token')->createValidToken($meta, $expiration);
		
		if( null ===$token ){
			Logger::getInstance()->log('Problem during creation of token in email change, number of possible attempts reach');
			$this->frontController->addMessage( __( 'Internal error, please retry in a moment' ), 'correct' );
			$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$validationLink = $this->frontController->getViewHelper()->createHref('Profile', 'validateEmailChange', array( 'token' => $token->getToken() ));
		$validationLink = Utils::getBaseUrlWithoutName() . $validationLink;
		$validationLink = '<a href="' . $validationLink . '" title="' . __('Validation link') . '">'. __('Click here to validate your email') .'</a>';
		
		$instrRepo = $this->em->getRepository('Instruction');
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'email_validation');
		$mailContent = $instruction->getInstructions();
		$mailContent = strtr($mailContent, array( '%validation_link%' => $validationLink ) );
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($newEmail, Config::getOrDie('site_display_name'). ': email validation', $mailContent);
		
		$this->frontController->addMessage( __( 'Email sent, the email will not be changed until the validation was completed' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @GetAttributes(token)
	 * The link comes from email sent at the first step of email change to validate that email and also change it
	 * Will send two emails, one for information to the new email and one to the old one to be able to reset
	 */
	public function validateEmailChangeAction() {
		$get = $this->frontController->getRequest()->query;
		$token = $get->get( 'token' );
		
		// use old_email, new_email and user_id to send both mails
		$tokenRepo = $this->em->getRepository('Token');
		$tokenMeta = $tokenRepo->findValidTokenMeta($token);
		if( false === $tokenMeta){
			// expiration or never exist
			Logger::getInstance()->log("Request expired: [$token]");
			$this->frontController->addMessage( __( 'Your request has expired' ), 'error' );
			$this->frontController->doRedirect( 'Home' );
		}
		$userId = $tokenMeta['userId'];
		$oldEmail = $tokenMeta['oldEmail'];
		$newEmail = $tokenMeta['newEmail'];
		
		$user = $this->em->find('User', $userId);
		if( false === $user ){
			Logger::getInstance()->log("Request about user that does not exist: userId=[$userId]");
			$this->frontController->addMessage( __( 'The request you made was about a user that does not exist anymore' ), 'error' );
			$this->frontController->doRedirect( 'Home' );
		}
		
		// update user email
		$user->setEmail($newEmail);
		$this->em->persist($user);
		if(!$this->em->flushSafe()){
			Logger::getInstance()->log("Email reset failed for: userId=[$userId] email=[$newEmail]");
			$this->frontController->addMessage( __( 'The email modification fails' ), 'error' );
			$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$instrRepo = $this->em->getRepository('Instruction');
		
		// send mail to new: inform new email that is validated
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'email_information');
		$mailContent = $instruction->getInstructions();
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($newEmail, Config::getOrDie('site_display_name'). ': email confirmed', $mailContent);
		
		// send mail to old: possibility to reset email
		$expiration = Config::get('email_change_validation_expiration_time', 48 * 60 * 60);
		$meta = array('oldEmail' => $oldEmail, 'userId' => $userId);
		$token = $tokenRepo->createValidToken($meta, $expiration);
		
		$resetLink = $this->frontController->getViewHelper()->createHref('Profile', 'resetEmail', array( 'token' => $token->getToken() ));
		$resetLink = Utils::getBaseUrlWithoutName() . $resetLink;
		$resetLink = '<a href="' . $resetLink . '" title="' . __('Reset link') . '">'. __('Click here to reset the email address of your profile to the one that receive this email') .'</a>';
		
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'email_reset');
		$mailContent = $instruction->getInstructions();
		$mailContent = strtr($mailContent, array( '%new_email%' => $newEmail, '%reset_link%' => $resetLink ) );
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($oldEmail, Config::getOrDie('site_display_name'). ': email changed', $mailContent);
		
		//redirect to profile home
		$this->frontController->addMessage( __( 'The email modification completes' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
	}
	/**
	 * @GetAttributes(token)
	 * The link comes from email sent at the same time as information in second step of email change
	 */
	public function resetEmailAction() {
		$get = $this->frontController->getRequest()->query;
		$token = $get->get( 'token' );
		
		$tokenRepo = $this->em->getRepository('Token');
		$tokenMeta = $tokenRepo->findValidTokenMeta($token);
		if( false === $tokenMeta){
			// expiration or never exist
			Logger::getInstance()->log("Request expired: [$token]");
			$this->frontController->addMessage( __( 'Your request has expired' ), 'error' );
			$this->frontController->doRedirect( 'Home' );
		}
		$userId = $tokenMeta['userId'];
		$oldEmail = $tokenMeta['oldEmail'];
		
		$user = $this->em->find('User', $userId);
		if( false === $user ){
			Logger::getInstance()->log("Request about user that does not exist: userId=[$userId]");
			$this->frontController->addMessage( __( 'The request you made was about a user that does not exist anymore' ), 'error' );
			$this->frontController->doRedirect( 'Home' );
		}
		
		// update user email
		$user->setEmail($oldEmail);
		$this->em->persist($user);
		if(!$this->em->flushSafe()){
			Logger::getInstance()->log("Email reset failed for: userId=[$userId] email=[$oldEmail]");
			$this->frontController->addMessage( __( 'The email reset fails' ), 'error' );
			$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
		}
		
		//redirect to profile home
		$this->frontController->addMessage( __( 'The email reset completes' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @Nonce(displayEditPasswordForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditPasswordFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileEditPasswordForm($this->frontController);
		$form->setupWithArray();
		$form->setNonceAction( 'editPassword' );
		
		$targetUrl = $this->frontController->getViewHelper()->createHref( 'Profile', 'editPassword', array('userId' => $userId) );
		
		$form->setTargetUrl($targetUrl);
		$this->frontController->getResponse()->setVar('userId', $userId);
		$this->frontController->getResponse()->setVar('form', $form);
		$this->frontController->doDisplay('profile', 'displayEditPasswordForm');
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editPassword)
	 * @GetAttributes(userId)
	 */
	public function editPasswordAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileEditPasswordForm($this->frontController);
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$username = $form->getUsername();
		$oldPassword = $form->getOldPassword();
		$newPassword = $form->getNewPassword();
		$user = $this->frontController->getUserManager()->getUser($username, $oldPassword);
		
		if(null === $user || $user->getId() !== $userId ){
			$this->frontController->doRedirectToReferrer( __( 'The username / password are not related to the user' ), 'error' );
		}
		
		// we create a token that contains old_email, new_email, user_id
		if($oldPassword === $newPassword){
			$this->frontController->doRedirectToReferrer( __( 'You cannot change the password to the same one' ), 'error' );
		}
		
		$passLength = strlen($newPassword);
		$passFirst = $newPassword[0];
		$passLast = $newPassword[$passLength-1];
		$hintPassword = $passFirst . str_repeat('?', $passLength-2) . $passLast;
		
		$newPassword = Crypto::encodeDBPassword($user->getRandomKey(), $newPassword);
		
		$user->setPassword($newPassword);
		$this->em->persist($user);
		if( !$this->em->flushSafe() ){
			$this->frontController->addMessage( __( 'The password change failed' ), 'correct' );
			$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$instrRepo = $this->em->getRepository('Instruction');
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixEmail, 'password_change');
		$mailContent = $instruction->getInstructions();
		// [username, password_hint]
		$mailContent = strtr($mailContent, array( '%username%' => $user->getUsername(), '%password_hint%' => $hintPassword ) );
		
		$mailContent = nl2br($mailContent);
		Mail::sendHtml($user->getEmail(), Config::getOrDie('site_display_name'). ': password change', $mailContent);
		
		$this->frontController->addMessage( __( 'The password is changed' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile','Profile', 'display', array( 'userId' => $userId ) );
	
	}
	
	/**
	 * @Nonce(displayEditAvatarForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditAvatarFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$userRepo = $this->em->getRepository( 'User' );
		$user = $userRepo->find( $userId );
		
		$userHelper = new UserHelper( $this->frontController );
		$userHelper->setCurrentUser( $user );
		
		$keys = $links = array();
		$rows = 6;
		$cols = 8;
		$avatarSize = 50;
		$size = $rows * $cols;
		for( $i = 0; $i < $size; $i++ ) {
			$key = Utils::createRandomString( 12, 'numberuppercase' );
			$keys[] = $key;
			$links[] = $userHelper->getGravatar( $key, $avatarSize, 'identicon' );
		}
		$emailLink = $userHelper->getGravatar( $user->getEmail(), $avatarSize, 'identicon' );
		
		$linkWithPlaceholder = $this->frontController->getViewHelper()->createHrefWithNonce( 'editAvatar', 'Profile', 'editAvatar', array( 'userId' => $userId, 'type' => 0, 'avatarKey' => '%avatarKey%' ) );
		
		$referrer = $this->frontController->getRequest()->getRequestedUrl();
		$referrerField =  '<input type="hidden" name="' . Config::get('referrer_field_name', '_http_referrer') . '" value="' . $referrer . '">';
		
		$response = $this->frontController->getResponse();
		$response->setVar( 'userId', $userId );
		$response->setVar( 'user', $user );
		$response->setVar( 'emailLink', $emailLink );
		$response->setVar( 'keys', $keys );
		$response->setVar( 'links', $links );
		$response->setVar( 'userHelper', $userHelper );
		$response->setVar( 'cols', $cols );
		$response->setVar( 'rows', $rows );
		$response->setVar( 'size', $avatarSize );
		$response->setVar( 'linkWithPlaceholder', $linkWithPlaceholder );
		$response->setVar( 'referrerField', $referrerField );
		
		$this->frontController->doDisplay( 'profile', 'chooseAvatar' );
	}
	
	/**
	 * @Nonce(editAvatar)
	 * @GetAttributes({userId, avatarKey, type})
	 */
	public function editAvatarAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		$avatarKey = $get->get( 'avatarKey' );
		$type = $get->get( 'type' );
		
		$userRepo = $this->em->getRepository( 'User' );
		$user = $userRepo->find( $userId );
		
		if( !$user ) {
			$this->frontController->addMessage( __( 'The user does not exist', 'error' ) );
			// we can't redirect to the profile, no correct user id
			$this->frontController->doRedirect( 'Home' );
		}
		{// deletion of previous avatar image
			if(1 == $user->getAvatarType()){
				$fileToDelete = $user->getAvatarKey();
				$pathToDelete = Config::getOrDie('avatar_dir') . $fileToDelete . '.jpg';
				if(file_exists($pathToDelete)){
					@unlink( $pathToDelete );
				}
			}
		}
			
		$user->setAvatarType( $type );
		$user->setAvatarKey( $avatarKey );
		$this->em->persist( $user );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'An error occurred during the modification' ), 'error' );
			$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'The avatar modification was a success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	
	}
	
	// receive an image, propose to the user to crop it and send it to saveImage
	/**
	 * @Nonce(displayCropAvatar)
	 * @GetAttributes({userId})
	 * @FilePresent
	 */
	public function displayCropImageAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		if( !isset( $_FILES['avatar_file'] ) ){
			$this->frontController->doRedirectToReferrer(__('Please upload an image' ), 'error');
			return false;	
		}

		$viewHelper = $this->frontController->getViewHelper();
		$viewHelper->addJavascriptFile('jquery.js');
		$viewHelper->addJavascriptFile('jcrop.js');
		$viewHelper->addJavascriptFile('crop_interaction.js');
		$viewHelper->addJavascriptVar('avatarImageMaxWidth', Config::get('avatar_original_max_width', 650));
		$viewHelper->addJavascriptVar('avatarImageMaxHeight', Config::get('avatar_original_max_height', 650));
		$viewHelper->addJavascriptVar('avatarCropMaxWidth', Config::get('avatar_crop_max_width', 200));
		$viewHelper->addJavascriptVar('avatarCropMaxHeight', Config::get('avatar_crop_max_height', 200));
		$viewHelper->addJavascriptVar('avatarCropMinWidth', Config::get('avatar_crop_min_width', 15));
		$viewHelper->addJavascriptVar('avatarCropMinHeight', Config::get('avatar_crop_min_height', 15));
		$viewHelper->addCssFile('profile_avatar_crop.css');
		$filename = ImageUtils::saveImage($this->frontController, $_FILES['avatar_file']);
		$imageLink = Config::getOrDie('temp_dir') . $filename;
		$tempKey = $filename;
		$imageSrcLink = $imageLink;
		
		$http = Utils::isSSL() ? 'https://' : 'http://';
		$imageLink = $http . $_SERVER['HTTP_HOST'] . '/' . Config::getOrDie('site_name') . '/' . $imageLink;
		
		$actionLink = $viewHelper->createHrefWithNonce('cropAvatar', 'Profile', 'cropImage', array('userId' => $userId, 'key' => $tempKey));
		
		$referrer = $this->frontController->getRequest()->getRequestedUrl();
		
		$response = $this->frontController->getResponse();
		$response->setVar('actionLink', $actionLink);
		$response->setVar('imageLink', $imageLink);
		$response->setVar('imageSrcLink', $imageSrcLink);
		$response->setVar('referrer', $referrer);
		$response->setVar('userId', $userId);
		$this->frontController->doDisplay('profile', 'displayCropImage', array('avatarKey' => $tempKey, 'userId' => $userId));
	}
	
	/**
	 * @Nonce(cropAvatar)
	 * @GetAttributes({userId, key})
	 * @PostAttributes({x, y, w, h})
	 */
	public function cropImageAction(){
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		$tempKey = $get->get( 'key' );
		
		if (!extension_loaded('gd') && !extension_loaded('gd2') ){
			trigger_error("GD is not loaded", E_USER_WARNING);
	        return false;
	    }
		
		$temporaryFile = Config::getOrDie('temp_dir') . $tempKey;
		
		$avatarDir = Config::getOrDie('avatar_dir');
		$randomName = ImageUtils::createRandomFilename($avatarDir, 12);
		$destinationFile = $avatarDir . $randomName . '.jpg';
		
		$targ_w = $targ_h = 150;
		$jpeg_quality = 90;
	
		$src = $temporaryFile;
		$ext = pathinfo($src, PATHINFO_EXTENSION);
		switch($ext){
			case 'png':
				$img_r = imagecreatefrompng($src);
				break;
			case 'jpg': case 'jpeg':
				try{
				$img_r = imagecreatefromjpeg($src);
				}catch(\Exception $e){
					$e;
				}
				break;
			case 'gif':
				$img_r = imagecreatefromgif($src);
				break;
			default:
				Logger::getInstance()->log_var('Bad extension name', $src);
				$result = false;
				break;
				
		}
		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

		$avatarCropMinWidth = Config::get('avatar_crop_min_width', 15);
		$avatarCropMinHeigth = Config::get('avatar_crop_min_height', 15);
		if($_POST['w'] < $avatarCropMinWidth || $_POST['h'] < $avatarCropMinHeigth){
			$this->frontController->addMessage(__('The selection was too small'), 'error');
			$this->frontController->doRedirectWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$userId) );
		}
		
		$avatarCropMaxWidth = Config::get('avatar_crop_max_width', 200);
		$avatarCropMaxHeigth = Config::get('avatar_crop_max_height', 200);
		if($_POST['w'] > $avatarCropMaxWidth || $_POST['h'] > $avatarCropMaxHeigth){
			$this->frontController->addMessage(__('The selection was too big'), 'error');
			$this->frontController->doRedirectWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$userId) );
		}
		
		$result = imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);
		
		if($result){
			$result = imagejpeg($dst_r, $destinationFile ,$jpeg_quality);
		}

		if($result){
			$userRepo = $this->em->getRepository('User');
			$user = $userRepo->find($userId);
			if(1 == $user->getAvatarType()){
				$fileToDelete = $user->getAvatarKey();
				$pathToDelete = $avatarDir . $fileToDelete . '.jpg';
				if(file_exists($pathToDelete)){
					@unlink( $pathToDelete );
				}
			}
			$user->setAvatarKey($randomName);
			$user->setAvatarType(1);
			$this->em->persist($user);
			$result = $this->em->flushSafe();
		}
		
		if(!$result){
			$this->frontController->addMessage(__('An error occurred during the crop of the image'), 'error');
			$this->frontController->doRedirectWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$userId) );
		}
		$this->frontController->addMessage(__('The modification of your avatar was a success'), 'correct');
		$this->frontController->doRedirectWithNonce('displayProfile', 'Profile', 'display', array('userId'=>$userId) );
	}
}

