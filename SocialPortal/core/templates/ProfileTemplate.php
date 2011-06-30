<?php

namespace core\templates;

use core\tools\Utils;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\model\UserProfile;

use socialportal\model\User;

use core\templates\iInsertable;

use core\FrontController;

class ProfileTemplate implements iInsertable {
	/** @var FrontController */
	private $front;
	/** @var User */
	private $user;
	/** @var UserProfile */
	private $profile;
	/** @var User */
	private $currentUser;
	/** @var boolean */
	private $isAdmin;
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
		$this->isAdmin = $this->front->getViewHelper()->currentUserIs(UserRoles::$admin_role);
		$this->isSelf = ($this->user->getId() === $this->currentUser->getId());
		$this->userHelper = new UserHelper($this->front);
		$this->userHelper->setCurrentUser($this->user);
		$this->userId = $user->getId();
	}
	
	public function insert() {
		$this->front->getViewHelper()->addCssFile('profile_display.css');
		?>
		<!-- container of the profile, contains the both columns -->
		<table class="profile-container"><tbody><tr>
			<!-- first column: avatar + links -->
			<td class="profil-avatar-column">
				<?php $this->insertAvatar(); ?>
				<?php $this->insertCorrectTools(); ?>
			</td>
			<td class="vertical-bar"></td>
			<!-- second column: Username, age/gender / description / objective / quote -->
			<td class="profil-content-column">
				<?php $this->insertCorrectContent(); ?>
			</td>
			</tr>
		</tbody>
		</table>
	
	<?php
	}
	
	private function insertAvatar(){
		?><div id="avatar"><?php $this->userHelper->insertAvatar(75); ?></div>
	<?php
	}
	private function insertCorrectTools(){
		if($this->isSelf){
			$this->insertToolSelf();
		}else if($this->isAdmin){
			$this->insertToolAdmin();
		}else{
			$this->insertToolVisitor();
		}
	}
	
	
	private function insertToolSelf(){
		$editProfile = __('Edit profile');
		$createProfile = __('Create profile');
		$editUsername = __('Edit username');
		$editPassword = __('Edit password');
		$editEmail = __('Edit email');
		?><div class="tool_links"><?php
		if(null !== $this->profile):
		?>
			<!-- edition of the profile -->

			<a title="<?php echo $editProfile; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId))?>"><?php echo $editProfile; ?></a>
		<?php else: ?>
			<!-- creation of the profile -->
			<a title="<?php echo $createProfile; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId))?>"><?php echo $createProfile; ?></a>
		<?php endif; ?>
		<!-- edit username -->
		<a class="unimplemented" title="<?php echo $editUsername; ?>" href="<?php 
			$this->front->getViewHelper()->insertHrefWithNonce('displayEditUsername', 'Profile', 'displayEditUsernameForm', array('userId'=>$this->userId))?>"><?php
			echo $editUsername; ?></a>
		<!-- edit password -->
		<a class="unimplemented" title="<?php echo $editPassword; ?>" href="<?php 
			$this->front->getViewHelper()->insertHrefWithNonce('displayEditPassword', 'Profile', 'displayEditPasswordForm', array('userId'=>$this->userId))?>"><?php
			echo $editPassword; ?></a>
		<!-- edit email -->
		<a class="unimplemented" title="<?php echo $editEmail; ?>" href="<?php 
			$this->front->getViewHelper()->insertHrefWithNonce('displayEditEmail', 'Profile', 'displayEditEmailForm', array('userId'=>$this->userId))?>"><?php
			echo $editEmail; ?></a>
		
		</div>
	<?php
	}
	private function insertToolAdmin(){
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
		
		
		
		
		$editProfile = __('Edit profile');
		$createProfile = __('Create profile');
		$editUsername = __('Edit username');
		$editPassword = __('Edit password');
		$editEmail = __('Edit email');
		?><div class="tool_links"><?php
		if(null !== $this->profile):
		?>
			<!-- edition of the profile -->

			<a title="<?php echo $editProfile; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId))?>"><?php echo $editProfile; ?></a>
		<?php else: ?>
			<!-- creation of the profile -->
			<a title="<?php echo $createProfile; ?>" href="<?php 
				$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId))?>"><?php echo $createProfile; ?></a>
		<?php endif; ?>
		<!-- edit username -->
		<a class="unimplemented" title="<?php echo $editUsername; ?>" href="<?php 
			$this->front->getViewHelper()->insertHrefWithNonce('displayEditUsername', 'Profile', 'displayEditUsernameForm', array('userId'=>$this->userId))?>"><?php
			echo $editUsername; ?></a>
		<!-- edit password -->
		<a class="unimplemented" title="<?php echo $editPassword; ?>" href="<?php 
			$this->front->getViewHelper()->insertHrefWithNonce('displayEditPassword', 'Profile', 'displayEditPasswordForm', array('userId'=>$this->userId))?>"><?php
			echo $editPassword; ?></a>
		<!-- edit email -->
		<a class="unimplemented" title="<?php echo $editEmail; ?>" href="<?php 
			$this->front->getViewHelper()->insertHrefWithNonce('displayEditEmail', 'Profile', 'displayEditEmailForm', array('userId'=>$this->userId))?>"><?php
			echo $editEmail; ?></a>
		
		</div>
	<?php
	}
	
	private function insertCorrectContent(){
		if($this->isSelf){
			$this->insertContentSelf();
		}else if($this->isAdmin){
			$this->insertContentAdmin();
		}else{
			$this->insertContentVisitor();
		}
	}
	
	private function insertContentSelf(){
		$username = $this->user->getUsername();
		$email = $this->user->getEmail();
		?>
		<div class="profile-field" id="username">
			<h1><?php echo $username; ?></h1>
		</div>
		<div class="profile-field" id="email">
			<span class="label"><?php echo __('Email:'); ?></span>
			<span><?php echo $email; ?></span>
			<em class="discrete"><?php echo __('(Email is not visible to other)'); ?></em>
		</div>
	<?php
		if(null !== $this->profile){
			$this->insertInformation();
		}else{
			$createMessage = __('Create the profile');
			$this->front->getViewHelper()->addCssFile('messages.css');
			?>
			<div class="message info"><?php echo __('Currently you do not have a profile, but you can create one by clicking on the following link:'); ?>
				<a class="button" title="<?php echo $createMessage ?>" href="<?php 
					$this->front->getViewHelper()->insertHrefWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$this->userId));
					?>"><?php echo $createMessage ?></a>
			</div>
			<?php
		}	
	}
	
	private function insertContentVisitor(){
		$username = $this->user->getUsername();
		$email = $this->user->getEmail();
		?>
		<div class="profile-field" id="username">
			<h2><?php echo $username; ?></h2>
		</div>
	<?php
		if(null !== $this->profile){
			$this->insertInformation();
		}else{
			$this->front->getViewHelper()->addCssFile('messages.css');
			?>
			<div class="message info"><?php echo __('Currently the user has not filled his profile, try to come back in some days'); ?></div>
			<?php
		}	
	}
	
	private function insertContentAdmin(){
		$username = $this->user->getUsername();
		$email = $this->user->getEmail();
		?>
		<div class="profile-field" id="username">
			<h2><?php echo $username; ?></h2>
		</div>
		<div class="profile-field" id="email">
			<span class="label"><?php echo __('Email:'); ?></span>
			<em class="discrete"><?php echo __('(Email is not visible to other)'); ?></em>
		</div>
	<?php
		if(null !== $this->profile){
			$this->insertInformation();
		}else{
			$this->front->getViewHelper()->addCssFile('messages.css');
			?>
			<div class="message info"><?php echo __('Currently the user has not filled his profile, try to come back in some days'); ?></div>
			<?php
		}	
	}
	
	private function insertInformation(){
		$birth = $this->profile->getBirth();
		$gender = $this->profile->getGender();
		if(1 == $gender){
			$gender = __('Male');
		}elseif(2 == $gender){
			$gender = __('Female');
		}else{
			$gender = null;	
		}
			
		$description = $this->profile->getDescription();
		$objectives = $this->profile->getObjectives();
		$quote = $this->profile->getQuote();
		if($birth){
			$age = Utils::getAgeFromBirthday($birth->getTimestamp());
			$age = __('%age% years old', array('%age%'=>$age));
		}else{
			$age = null;
		}
		$lastModificationDate = $this->profile->getLastModified();
		$lastModificationDate = Utils::getDataSince($lastModificationDate, false);
		?>
		
		<?php if(null !== $gender):?>
			<div class="profile-field" id="gender">
				<span class="label"><?php echo __('Gender:'); ?></span>
				<span><?php echo $gender; ?></span>
			</div>
		<?php endif; ?>
		<?php if(null !== $age):?>
			<div class="profile-field" id="age">
				<span class="label"><?php echo __('Age:'); ?></span>
				<span><?php echo $age; ?></span>
			</div>
		<?php endif; ?>
		<?php if($description):?>
		<div class="profile-field" id="description">
			<span class="label"><?php echo __('Description:'); ?></span>
			<span><?php echo $description; ?></span>
		</div>
		<?php endif; ?>
		<?php if($objectives):?>
		<div class="profile-field" id="objectives">
			<span class="label"><?php echo __('Objectives:'); ?></span>
			<span><?php echo $objectives; ?></span>
		</div>
		<?php endif; ?>
		<div class="profile-field" id="quote">
			<span class="start_quote"> </span>
			<span><?php echo $quote; ?></span>
			<span class="end_quote"> </span>
		</div>
		<div class="profile-field" id="last_modification">
			<span><?php echo __('Last modification: %date%', array('%date%'=>$lastModificationDate)); ?></span>
		</div>
		<?php
	}

}