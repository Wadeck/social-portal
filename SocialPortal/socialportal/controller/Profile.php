<?php

namespace socialportal\controller;
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
	 * @Method(GET)
	 * @Nonce(displayProfile)
	 * @GetAttributes(userId)
	 */
	public function displayAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get('userId');
		
		$profileRepo = $this->em->getRepository('UserProfile');
		$profile = $profileRepo->findByUserId($userId);
		$userRepo = $this->em->getRepository('User');
		$user = $userRepo->find($userId);
		
		$profileTemplate = new ProfileTemplate($this->frontController, $user, $profile);
		
//		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'user', $user );
		$this->frontController->getResponse()->setVar( 'profileTemplate', $profileTemplate );
		$this->frontController->doDisplay( 'profile', 'displayProfile' );
	}
	
	/**
	 * @Method(GET)
	 * @Nonce(displayEditProfile)
	 * @GetAttributes(userId)
	 */
	public function displayEditProfileFormAction(){
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get('userId');
		
		$profileRepo = $this->em->getRepository('UserProfile');
		$profile = $profileRepo->findByUserId($userId);
		
		$form = new ProfileForm($this->frontController);
		
		$getArgs = array( 'userId' => $userId );
		
		if(null !== $profile){
			$form->setupWithProfile($profile );
			$form->setNonceAction( 'editProfile' );
			$module = 'editProfile';
		}else{
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
	public function createProfileAction(){
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get('userId');
		
		$form = new ProfileForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$profile = $form->createProfile();
		$profile->setUserId($userId);
		$profile->setLastModified(new DateTime('@'.$this->frontController->getRequest()->getRequestTime()));
		
		$this->em->persist($profile);
		if(!$this->em->flushSafe()){
			$this->frontController->addMessage(__('Profile edition fail, please try again in a moment'), 'error');
			$this->frontController->doRedirect('Profile', 'display', array('userId'=>$userId));
		}
		
		$this->frontController->addMessage(__('Profile edition success'), 'correct');
		$this->frontController->doRedirectWithNonce('displayProfile', 'Profile', 'display', array('userId'=>$userId));
	}

	/**
	 * @Method(POST)
	 * @Nonce(editProfile)
	 * @GetAttributes(userId)
	 */
	public function editProfileAction(){
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get('userId');
		
		$profileRepo = $this->em->getRepository('UserProfile');
		$existingProfile = $profileRepo->findByUserId($userId);
		if(null === $existingProfile){
			$this->frontController->addMessage(__('There was an internal error, this is not an edition but a creation'), 'error');
			$this->frontController->doRedirect('Profile', 'display', array('userId'=>$userId));
		}
		
		$form = new ProfileForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$profile = $form->createProfile($existingProfile);
		$profile->setLastModified(new DateTime('@'.$this->frontController->getRequest()->getRequestTime()));
		
		$this->em->persist($profile);
		if(!$this->em->flushSafe()){
			$this->frontController->addMessage(__('Profile edition fail, please try again in a moment'), 'error');
			$this->frontController->doRedirect('Profile', 'display', array('userId'=>$userId));
		}
		
		$this->frontController->addMessage(__('Profile edition success'), 'correct');
		$this->frontController->doRedirectWithNonce('displayProfile', 'Profile', 'display', array('userId'=>$userId));
	}
	
	/**
	 * @Method(GET)
	 * @Nonce(displayEditEmailForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditEmailFormAction(){
		
	}

	/**
	 * @Method(POST)
	 * @Nonce(editEmail)
	 * @GetAttributes(userId)
	 */
	public function editEmailAction(){
		
	}
	
	/**
	 * @Method(GET)
	 * @Nonce(displayEditPasswordForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditPasswordFormAction(){
		
	}

	/**
	 * @Method(POST)
	 * @Nonce(editPassword)
	 * @GetAttributes(userId)
	 */
	public function editPasswordAction(){
		
	}
	
	/**
	 * @Method(GET)
	 * @Nonce(displayEditUsernameForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditUsernameFormAction(){
		
	}

	/**
	 * @Method(POST)
	 * @Nonce(editUsername)
	 * @GetAttributes(userId)
	 */
	public function editUsernameAction(){
		
	}
	
	/**
	 * @Method(GET)
	 * @Nonce(displayEditAvatarForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditAvatarFormAction(){
		
	}

	/**
	 * @Method(POST)
	 * @Nonce(editAvatar)
	 * @FilePresent
	 * @GetAttributes(userId)
	 */
	public function editAvatarAction(){
		
	}
}
