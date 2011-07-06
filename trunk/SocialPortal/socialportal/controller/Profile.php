<?php

namespace socialportal\controller;
use core\debug\Logger;

use core\Config;

use socialportal\model\User;

use core\tools\Utils;

use core\templates\ProfileTemplate;

use core\form\custom\ProfileForm;

use socialportal\model\UserProfile;

use core\form\custom\RegisterForm;

use core\user\UserHelper;

use core\form\custom\LoginForm;

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
		$form->setupWithArray( true );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'profile', 'displayForm' );
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
		
		$profile = $form->createProfile();
		$profile->setUserId( $userId );
		$profile->setLastModified( new DateTime( '@' . $this->frontController->getRequest()->getRequestTime() ) );
		
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
		$profile->setLastModified( new DateTime( '@' . $this->frontController->getRequest()->getRequestTime() ) );
		
		$this->em->persist( $profile );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Profile edition fail, please try again in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'Profile edition success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @Nonce(displayEditEmailForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditEmailFormAction() {

	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editEmail)
	 * @GetAttributes(userId)
	 */
	public function editEmailAction() {

	}
	
	/**
	 * @Nonce(displayEditPasswordForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditPasswordFormAction() {

	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editPassword)
	 * @GetAttributes(userId)
	 */
	public function editPasswordAction() {

	}
	
	/**
	 * @Nonce(displayEditUsernameForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditUsernameFormAction() {

	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editUsername)
	 * @GetAttributes(userId)
	 */
	public function editUsernameAction() {

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
		//TODO put in configuration file
		$rows = 6;
		$cols = 8;
		$avatarSize = 50;
		$size = $rows * $cols;
		for( $i = 0; $i < $size; $i++ ) {
			$key = Utils::createRandomString( 6, 'numberuppercase' );
			$keys[] = $key;
			$links[] = $userHelper->getGravatar( $key, $avatarSize, 'identicon' );
		}
		$emailLink = $userHelper->getGravatar( $user->getEmail(), $avatarSize, 'identicon' );
		
		$linkWithPlaceholder = $this->frontController->getViewHelper()->createHrefWithNonce( 'editAvatar', 'Profile', 'editAvatar', array( 'userId' => $userId, 'type' => 0, 'avatarKey' => '%avatarKey%' ) );
		
		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'user', $user );
		$this->frontController->getResponse()->setVar( 'emailLink', $emailLink );
		$this->frontController->getResponse()->setVar( 'keys', $keys );
		$this->frontController->getResponse()->setVar( 'links', $links );
		$this->frontController->getResponse()->setVar( 'userHelper', $userHelper );
		$this->frontController->getResponse()->setVar( 'cols', $cols );
		$this->frontController->getResponse()->setVar( 'rows', $rows );
		$this->frontController->getResponse()->setVar( 'size', $avatarSize );
		$this->frontController->getResponse()->setVar( 'linkWithPlaceholder', $linkWithPlaceholder );
		
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

		$viewHelper = $this->frontController->getViewHelper();
		$viewHelper->addJavascriptFile('jquery.js');
		$viewHelper->addJavascriptFile('jcrop.js');
		$viewHelper->addJavascriptFile('crop_interaction.js');
		
		$filename = $this->saveImage();
		$imageLink = Config::$instance->TEMP_DIR . $filename;
		$avatarKey = $filename;
		$imageSrcLink = $imageLink;
		
		
		$http = Utils::isSSL() ? 'https://' : 'http://';
		$imageLink = $http . $_SERVER['HTTP_HOST'] . '/' . Config::$instance->SITE_NAME . '/' . $imageLink;
		
		
		$actionLink = $viewHelper->createHrefWithNonce('cropAvatar', 'Profile', 'cropImage', array('userId' => $userId, 'avatarKey' => $avatarKey));
		
		$this->frontController->getResponse()->setVar('actionLink', $actionLink);
		$this->frontController->getResponse()->setVar('imageLink', $imageLink);
		$this->frontController->getResponse()->setVar('imageSrcLink', $imageSrcLink);
		$this->frontController->doDisplay('profile', 'displayCropImage', array('avatarKey' => $avatarKey, 'userId' => $userId));
	}
	
	/**
	 * @Nonce(cropAvatar)
	 * @GetAttributes({userId, avatarKey})
	 */
	public function cropImageAction(){
		
	}
	
	/**
	 *  receive cropped image, save it and call editAvatarAction
	 * @return string|false filename to the file or false if an error occurred
	 */
	private function saveImage(){
		if(!$this->verifyFile($_FILES['avatar_file'])){
			return false;
		}
		$filename = $this->createRandomFilename(Config::$instance->TEMP_DIR);
		if(false === $filename){
			return false;
		}
		$url = $this->writeToFilename($_FILES['avatar_file'], Config::$instance->TEMP_DIR, $filename);
		if(false === $url){
			return false;
		}
		
		return $filename;
		
//		$targ_w = $targ_h = 150;
//		$jpeg_quality = 90;
//	
//		$src = 'demo_files/flowers.jpg';
//		$img_r = imagecreatefromjpeg($src);
//		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
//
//		imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);
//	
//		header('Content-type: image/jpeg');
//		imagejpeg($dst_r,null,$jpeg_quality);
	}
	
	private function verifyFile($file){
		if ( $file['error'] ){
			return false;
		}
		if ( $file['size'] > Config::$instance->MAX_AVATAR_FILE_SIZE ){
			return false;
		}
		if ( ( !empty( $file['type'] ) &&
				!preg_match('/(jpe?g|gif|png|pjpeg)$/', $file['type'] ) ) ||
				!preg_match( '/(jpe?g|gif|png|pjpeg)$/', $file['name'] ) ){
			return false;
		}
		return true;
	}
	
	/** @return string representing a filename that is not used in folder */
	private function createRandomFilename($folder){
		if( !file_exists( $folder ) ) {
			Logger::getInstance()->log( 'Creation of the temp path: ' . $folder );
			mkdir( $folder, '0777', true );
		}
		
		$num = 0;
		do{
			$name = Utils::createRandomString(6, 'alphanumeric');
			$num++;
			if($num > 100){
				Logger::getInstance()->log('Number of attempt to create random filename is reached...');
				return false;
			}
		}while(file_exists($folder . $name));
		
		return $name;
	}
	
	private function writeToFilename($file, $folder, $filename){
		// Move the file to the uploads dir
		$new_file = $folder . '/' . $filename;
		if ( false === @move_uploaded_file( $file['tmp_name'], $new_file ) ){
			return false;
//			return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );
		}
	
		// Set correct file permissions
		$stat = stat( dirname( $new_file ));
		$perms = $stat['mode'] & 0000666;
		@chmod( $new_file, $perms );
	
		return $new_file;
		
	}
}
