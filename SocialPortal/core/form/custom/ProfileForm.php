<?php

namespace core\form\custom;

use core\form\fields\DropDownField;

use core\form\fields\StaticListField;

use socialportal\model\UserProfile;

use core\form\fields\DatePickerField;

use core\form\fields\RadioField;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;
use DateTime;

class ProfileForm extends Form {
	protected $globalMode = 2;
	public function __construct(FrontController $front) {
		parent::__construct( 'Profile', $front, 'formProfileSubmit', __( 'Submit' ) );
		$this->addInputField( new RadioField( 'profile_gender', __( 'Gender' ), array( __( 'Male' ), __( 'Female' ) ), null, array( 1, 2 ), true ) );
		$this->addInputField( new DatePickerField( 'profile_birth', __( 'Birth date' ) ) );
		$this->addInputField(new DropDownField('profile_date_display', __('Date display'), array(__('Not shown'), __('Day and month'), __('Age only'), __('Day, Month, Year')), array(0, 1, 2, 3), 0));
		$this->addInputField( new TextAreaField( 'profile_description', __( 'Describe yourself' ), '', array( 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'profile_objectives', __( 'Your objectives' ), '', array( 'strlen_at-least_15' ) ) );
		$this->addInputField( new TextField( 'profile_quote', __( 'Your favourite quote' ), '', 'text', array( 'strlen_at-least_5' ) ) );
		$this->addInputField( new StaticListField('profile_hobbies', __('Hobbies'), array(), 5 /*, array( 'strlen_at-least_3' ) */ ) );
		
		$this->setCss( 'profile-form', 'profile_form.css' );
	}
	
	protected function setGlobalMode($globalMode = 1) {
		$this->globalMode = $globalMode;
	}
	
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	public function setupWithProfile(UserProfile $profile) {
		$args = array();
		$birth = $profile->getBirth();
		$gender = $profile->getGender();
		if( null === $gender ) {
			// unknown 
			$gender = null;
		} elseif( !$gender ) {
			// male
			$gender = 1;
		} else {
			// female
			$gender = 2;
		}
		
		$args['profile_gender'] = $gender;
		if($birth){
			$args['profile_birth'] = $birth;
			$dateDisplay = $profile->getDateDisplay();
			if(null === $dateDisplay){
				$dateDisplay = 0;
			}
			$args['profile_date_display'] = $dateDisplay;
		}
		$args['profile_description'] = $profile->getDescription();
		$args['profile_objectives'] = $profile->getObjectives();
		$args['profile_quote'] = $profile->getQuote();
		
		$hobbies = $profile->getHobbies();
		if($hobbies){
			$hobbies = unserialize($hobbies);
			$args['profile_hobbies'] = $hobbies;
		}
		
		$this->fillWithArray( $args );
	}
	
	public function createProfile(UserProfile $profile = null) {
		if( !$this->ready ) {
			return null;
		}
		if( !$profile ) {
			$profile = new UserProfile();
		}
		$birth = $this->data['profile_birth'];
		if($birth){
			$birthDate = new DateTime( '@' . $birth );
			$profile->setBirth( $birthDate );
			
			$dateDisplay = $this->data['profile_date_display'];
			$profile->setDateDisplay($dateDisplay);
		}else{
			$profile->setBirth( null );
			$profile->setDateDisplay(null);
		}
		
		$gender = $this->data['profile_gender'];
		if( null === $gender ) {
			$profile->setGender( null );
		} elseif( 1 == $gender ) {
			// male
			$profile->setGender( false );
		} else {
			// female
			$profile->setGender( true );
		}
		
		$hobbies = $this->data['profile_hobbies'];
		if($hobbies){
			$hobbies = serialize($hobbies);
			$profile->setHobbies($hobbies);
		}else{
			$profile->setHobbies(null);
		}
		
		$description = $this->data['profile_description'];
		if($description){
			$profile->setDescription( $description );
		}else{
			$profile->setDescription( null );
		}
		$objectives = $this->data['profile_objectives'];
		if($objectives){
			$profile->setObjectives( $objectives );
		}else{
			$profile->setObjectives( null );
		}
		$quote = $this->data['profile_quote'] ;
		if($quote){
			$profile->setQuote( $quote );
		}else{
			$profile->setQuote( null );
		}
		
		return $profile;
	}
}