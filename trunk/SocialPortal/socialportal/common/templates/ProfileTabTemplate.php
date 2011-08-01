<?php

namespace socialportal\common\templates;

use core\tools\Utils;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\model\UserProfile;

use socialportal\model\User;

use core\FrontController;

class ProfileTabTemplate implements iInsertable {
	/** @var FrontController */
	private $front;
	/** @var User */
	private $user;
	/** @var UserProfile */
	private $profile;
	/** @var User */
	private $currentUser;
	/** @var boolean */
	private $isModo;
	/** @var boolean */
	private $isSelf;
	/** @var UserHelper */
	private $userHelper;
	/** @var int */
	private $userId;
	
	/** @var array */
	private $links;
	
	public function __construct(FrontController $front, User $user, UserProfile $profile, array $links) {
		$this->front = $front;
		$this->user = $user;
		$this->profile = $profile;
		$this->currentUser = $this->front->getCurrentUser();
		$this->isModo = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role);
		$this->isSelf = ($this->user->getId() === $this->currentUser->getId());
		$this->userHelper = new UserHelper($this->front);
		$this->userHelper->setCurrentUser($this->user);
		$this->userId = $user->getId();
		$this->links = $links;
	}
	
	public function insert() {
		$isMine = $this->isSelf;
		$isModo = $this->isModo;
		$isUser = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$full_user_role);
		
		$this->front->getViewHelper()->addCssFile('profile_tab.css');
		
		$username = $this->user->getUsername();
	?>
		<div id="tab-panel">
		<span class="tab-link"><?php echo $this->links[ 'info' ] ; ?></span>
	<?php
		if( $this->hasRight($this->profile->getBmiPrivacy(), $isUser, $isModo, $isMine) ){
			echo '<span class="tab-link">' . $this->links[ 'bmi' ] . '</span>' ;
		}
		
		if( $this->hasRight($this->profile->getMoodPrivacy(), $isUser, $isModo, $isMine) ){
			echo '<span class="tab-link">' . $this->links[ 'mood' ] . '</span>' ;
		}
		?>
		</div>
		<div class="profile-field" id="username">
			<h1><?php echo $username; ?></h1>
		</div>
	<?php
	}

	private function hasRight($level, $isUser, $isModo, $isMine){
		if($isModo || $isMine){
			return true;
		}else if( 1 === $level ){
			return true;
		}else if( 2 === $level && $isUser ){
			return true;
		}
		return false;
	}
}