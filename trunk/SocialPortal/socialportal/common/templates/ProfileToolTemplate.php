<?php

namespace socialportal\common\templates;

use core\tools\Utils;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\model\UserProfile;

use socialportal\model\User;

use core\FrontController;

class ProfileToolTemplate implements iInsertable {
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
	
	public function __construct(FrontController $front, User $user, UserProfile $profile = null) {
		$this->front = $front;
		$this->user = $user;
		$this->profile = $profile;
		$this->currentUser = $this->front->getCurrentUser();
		$this->isModo = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role);
		$this->isSelf = ($this->user->getId() === $this->currentUser->getId());
		$this->userHelper = new UserHelper($this->front);
		$this->userHelper->setCurrentUser($this->user);
		$this->userId = $user->getId();
	}
	
	public function insert() {
		$this->front->getViewHelper()->addCssFile('profile_tool.css');
		$this->insertAvatar();
		$this->insertCorrectTools();
	}
	
	private function insertAvatar(){
		?><div id="avatar"><?php $this->userHelper->insertAvatar(75); ?></div>
	<?php
	}
	
	private function insertCorrectTools(){
		if($this->isSelf){
			$this->insertToolSelf();
		}else if($this->isModo){
			$this->insertToolModerator();
		}else{
			$this->insertToolVisitor();
		}
	}
	
	private function insertToolSelf(){
		$editProfile = __('Edit profile');
		$editPrivacy = __('Edit privacy');
		$createProfile = __('Create profile');
		$editUsername = __('Edit username');
		$editPassword = __('Edit password');
		$editEmail = __('Edit email');
		$changeAvatar = __('Change avatar');
		?>
		
		<div class="tool_links">
			<?php if(null !== $this->profile): ?>
				<!-- edition of the profile -->
				<a title="<?php echo $editProfile; ?>" href="<?php 
					$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId))?>"><?php echo $editProfile; ?></a>
				<!-- edition of the privacy -->
				<a title="<?php echo $editPrivacy; ?>" href="<?php 
					$this->front->getViewHelper()->insertHrefWithNonce('displayEditPrivacyForm', 'Profile', 'displayEditPrivacyForm', array('userId'=>$this->userId))?>"><?php echo $editPrivacy; ?></a>
			<?php else: ?>
				<!-- creation of the profile -->
				<a title="<?php echo $createProfile; ?>" href="<?php 
					$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId))?>"><?php echo $createProfile; ?></a>
			<?php endif; ?>
			
			<!-- change avatar -->
			<a title="<?php echo $changeAvatar; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditAvatarForm', 'Profile', 'displayEditAvatarForm', array('userId'=>$this->userId))?>"><?php
				echo $changeAvatar; ?></a>
			<!-- edit username -->
			<a class="" title="<?php echo $editUsername; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditUsernameForm', 'Profile', 'displayEditUsernameForm', array('userId'=>$this->userId))?>"><?php
				echo $editUsername; ?></a>
			<!-- edit password -->
			<a class="" title="<?php echo $editPassword; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditPasswordForm', 'Profile', 'displayEditPasswordForm', array('userId'=>$this->userId))?>"><?php
				echo $editPassword; ?></a>
			<!-- edit email -->
			<a class="" title="<?php echo $editEmail; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditEmailForm', 'Profile', 'displayEditEmailForm', array('userId'=>$this->userId))?>"><?php
				echo $editEmail; ?></a>
				<!-- edit email -->
			<?php if($this->isModo){
			$manageReports = __('Manage reports');	?>
			<a class="" title="<?php echo $editEmail; ?>" href="<?php 
				$this->front->getViewHelper()->insertHref('ReportAbuse', 'displayManageReportForm', array('userId'=>$this->userId))?>">
				<?php echo $manageReports; ?></a>
			<?php }?>
		</div>
	<?php
	}
	private function insertToolModerator(){
		$resetPassword = __('Reset password');
		$username = $this->user->getUsername();
		?>
		<div class="tool_links">
			<a class="unimplemented" <?php $this->front->getViewHelper()->insertConfirmLink(__('Do you really want to reset the password of %username%', array('%username%'=>$username)));?>
				title="<?php echo $resetPassword; ?>"
				href="<?php $this->front->getViewHelper()->insertHrefWithNonce('resetPassword', 'Profile', 'resetPassword', array('userId'=>$this->userId))?>"><?php
				echo $resetPassword; ?></a>
		</div>
	<?php
	}
	
	private function insertToolVisitor(){
		return;
	}
}