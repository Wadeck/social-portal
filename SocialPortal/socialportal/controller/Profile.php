<?php

namespace socialportal\controller;
use core\form\custom\RegisterForm;

use core\user\UserHelper;

use core\form\custom\LoginForm;

use core\FrontController;

use core;

use core\AbstractController;
use core\user\UserRoles;

class Profile extends AbstractController {
	/**
	 * @Method(GET)
	 * @Nonce(displayProfile)
	 * @GetAttributes(userId)
	 */
	public function displayAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get('userId');
		
		
		
	}
	
	public function displayEditProfileAction(){
		
	}

	/**
	 * @Method(POST)
	 * @Nonce(editProfile)
	 */
	public function editProfileAction(){
		
	}
	
	public function displayEditAvatarFormAction(){
		
	}

	/**
	 * @Method(POST)
	 * @Nonce(editAvatar)
	 * @FilePresent
	 */
	public function editAvatarAction(){
		
	}
}
