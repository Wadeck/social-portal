<?php

namespace socialportal\common\templates;

use core\tools\Utils;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\model\UserProfile;

use socialportal\model\User;

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
		}else if($this->isModo){
			$this->insertToolModerator();
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
		$changeAvatar = __('Change avatar');
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
	
	private function insertCorrectContent(){
		if($this->isSelf){
			$this->insertContentSelf();
		}else if($this->isModo){
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
	<?php
		$this->insertBasicInformation(true);
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
		
		?>
		<div class="profile-field" id="username">
			<h2><?php echo $username; ?></h2>
		</div>
		
	<?php
		$this->insertBasicInformation();
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
		?>
		<div class="profile-field" id="username">
			<h2><?php echo $username; ?></h2>
		</div>
	<?php
		$this->insertBasicInformation(true);
		if(null !== $this->profile){
			$this->insertInformation();
		}else{
			$this->front->getViewHelper()->addCssFile('messages.css');
			?>
			<div class="message info"><?php echo __('Currently the user has not filled his profile, try to come back in some days'); ?></div>
			<?php
		}	
	}
	
	/**
	 * Insert the email / registration date
	 * @param boolean withEmail true if we want to insert the email 
	 */
	private function insertBasicInformation($withEmail=false){
		if($withEmail){
			$email = $this->user->getEmail();
			$emailLabel = __('Email:');
			$emailInformation = __('(Email is not visible to other)');
		}
		
		$registrationDate = $this->user->getRegistered();
		$registrationDate = Utils::getDataSince($registrationDate->getTimestamp());
		// date format
		// $registrationDate = date('H:i a F d Y', $registrationDate->getTimestamp());
		$registrationDateLabel = __('Registration:');
		?>
		<table>
			<?php if($withEmail): ?>
				<tr class="profile-field" id="email">
					<td><span class="label"><?php echo $emailLabel; ?></span></td>
					<td>
						<span><?php echo $email; ?></span>
						<em class="discrete"><?php echo $emailInformation; ?></em>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="profile-field" id="registration">
				<td class="label"><?php echo $registrationDateLabel; ?></td>
				<td><?php echo $registrationDate; ?></td>
			</tr>
		</table>
	<?php
	}
	
	private function insertInformation(){
		$country = $this->profile->getCountry();
		$state = $this->profile->getState();
		
		$birth = $this->profile->getBirth();
		$dateDisplay = $this->profile->getDateDisplay();
		if($birth){
			// "0: not shown, 1: only day/month, 2: only age, 3: total"
			switch($dateDisplay){
				case '0': default: // not shown
					$date = null;
					$dateLabel = null;
					break;
				case '1': // only day/month
					$date = date('F d', $birth->getTimestamp());
					$dateLabel = __('Birth:');
					break;
				case '2': // only age
					$date = Utils::getAgeFromBirthday($birth->getTimestamp());
					$date = __('%age% years old', array('%age%'=>$date));
					$dateLabel = __('Age:');
					break;
				case '3': // total
					$date = date('F d Y', $birth->getTimestamp());
					$dateLabel = __('Birth:');
					break;
			}
		}else{
			$date = null;
			$dateLabel = null;
		}

		$gender = $this->profile->getGender();
		
		if(null === $gender){
			$gender = null;
		}elseif(!$gender){
			$gender = __('Male');
		}else{
			$gender = __('Female');
		}
			
		$description = $this->profile->getDescription();
		$objectives = $this->profile->getObjectives();
		$quote = $this->profile->getQuote();
		$lastModificationDate = $this->profile->getLastModified();
		$lastModificationDate = Utils::getDataSince($lastModificationDate, false);
		
		$hobbies = $this->profile->getHobbies();
		if($hobbies){
			$hobbies = unserialize($hobbies);
		}
		
		if(null !== $country){
			$country = $this->front->getEntityManager()->find('UserProfileCountry', $country);
			$country = utf8_decode($country->getCountryName());
			$countryLabel = __('Country:');
			if(null !== $state){
				$state = $this->front->getEntityManager()->find('UserProfileState', $state);
				$state = utf8_decode($state->getStateName());
				$stateLabel = __('State:');
			}	
		}	
		
		?>
		<table class="profile_information">
		<?php if(null !== $gender):?>
			<tr class="profile-field" id="gender">
				<td class="label"><?php echo __('Gender:'); ?></td>
				<td><?php echo $gender; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if(null !== $date):?>
			<tr class="profile-field" id="age">
				<td class="label"><?php echo $dateLabel; ?></td>
				<td><?php echo $date; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if(null !== $country):?>
			<tr class="profile-field" id="country">
				<td class="label"><?php echo $countryLabel; ?></td>
				<td><?php echo $country; ?></td>
			</tr>
			<?php if(null !== $state):?>
				<tr class="profile-field" id="state">
					<td class="label"><?php echo $stateLabel; ?></td>
					<td><?php echo $state; ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if($description):?>
			<tr class="profile-field" id="description">
				<td class="label"><?php echo __('Description:'); ?></td>
				<td><?php echo $description; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if($objectives):?>
			<tr class="profile-field" id="objectives">
				<td class="label"><?php echo __('Objectives:'); ?></td>
				<td><?php echo $objectives; ?></td>
			</tr>
		<?php endif; ?>
		<?php if($hobbies):?>
			<tr class="profile-field" id="hobbies">
				<td class="label"><?php echo __('Hobbies:'); ?></td>
				<td>
					<ul>
					<?php foreach($hobbies as $h): ?>
						<li>
							<?php echo $h; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				</td>
			</tr>
		<?php endif; ?>
		</table>
		
			<?php if($quote): ?>
			<div class="profile-field" id="quote">
				<div>
					<div class="center">
						<div class="start_quote"></div>&nbsp;<?php echo $quote ; ?>&nbsp;<div class="end_quote"></div>
					</div>
				</div>
			</div>
			<?php endif; ?>
		
		<div class="profile-field" id="last_modification">
			<span><?php echo __('Last modification: %date%', array('%date%'=>$lastModificationDate)); ?></span>
		</div>
		
		<?php
	}
}