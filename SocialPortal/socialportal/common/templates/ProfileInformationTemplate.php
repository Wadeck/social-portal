<?php

namespace socialportal\common\templates;

use core\tools\Utils;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\model\UserProfile;

use socialportal\model\User;

use core\FrontController;

class ProfileInformationTemplate implements iInsertable {
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
		$this->front->getViewHelper()->addCssFile('profile_information.css');
		$this->insertCorrectContent();
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
		$email = $this->user->getEmail();
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
		$isMine = $this->isSelf;
		$isModo = $this->isModo;
		$isUser = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$full_user_role);
		
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
		<?php if(null !== $gender && $this->hasRight($this->profile->getGenderPrivacy(), $isUser, $isModo, $isMine) ):?>
			<tr class="profile-field" id="gender">
				<td class="label"><?php echo __('Gender:'); ?></td>
				<td><?php echo $gender; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if(null !== $date && $this->hasRight($this->profile->getBirthPrivacy(), $isUser, $isModo, $isMine)):?>
			<tr class="profile-field" id="age">
				<td class="label"><?php echo $dateLabel; ?></td>
				<td><?php echo $date; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if(null !== $country && $this->hasRight($this->profile->getCountryPrivacy(), $isUser, $isModo, $isMine)):?>
			<tr class="profile-field" id="country">
				<td class="label"><?php echo $countryLabel; ?></td>
				<td><?php echo $country; ?></td>
			</tr>
			<?php if(null !== $state && $this->hasRight($this->profile->getStatePrivacy(), $isUser, $isModo, $isMine)):?>
				<tr class="profile-field" id="state">
					<td class="label"><?php echo $stateLabel; ?></td>
					<td><?php echo $state; ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if($description && $this->hasRight($this->profile->getDescriptionPrivacy(), $isUser, $isModo, $isMine)):?>
			<tr class="profile-field" id="description">
				<td class="label"><?php echo __('Description:'); ?></td>
				<td><?php echo $description; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if($objectives && $this->hasRight($this->profile->getObjectivesPrivacy(), $isUser, $isModo, $isMine)):?>
			<tr class="profile-field" id="objectives">
				<td class="label"><?php echo __('Objectives:'); ?></td>
				<td><?php echo $objectives; ?></td>
			</tr>
		<?php endif; ?>
		<?php if($hobbies && $this->hasRight($this->profile->getHobbiesPrivacy(), $isUser, $isModo, $isMine)):?>
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
		
			<?php if($quote && $this->hasRight($this->profile->getQuotePrivacy(), $isUser, $isModo, $isMine)): ?>
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