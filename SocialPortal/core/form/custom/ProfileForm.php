<?php

namespace core\form\custom;

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
		parent::__construct( 'Profile', $front, 'formProfileSubmit', __('Submit') );
		$this->addInputField( new RadioField('profile_gender', __('Gender'),  array(__('Male'), __('Female')), null, array(1,2), true));
		$this->addInputField( new DatePickerField('profile_birth', __('Birth date')));
		$this->addInputField( new TextAreaField( 'profile_description', __( 'Describe yourself' ), '', array( 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'profile_objectives', __( 'Your objectives' ), '', array( 'strlen_at-least_15' ) ) );
		$this->addInputField( new TextField( 'profile_quote', __( 'Your favourite quote' ), '', 'text', array( 'strlen_at-least_5' ) ) );
	
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
		$args['profile_gender'] = $profile->getGender();
		$args['profile_birth'] = $profile->getBirth();
		$args['profile_description'] = $profile->getDescription();
		$args['profile_objectives'] = $profile->getObjectives();
		$args['profile_quote'] = $profile->getQuote();
		
		$this->fillWithArray( $args );
	}
	
	public function createProfile(UserProfile $profile=null){
		if(!$this->ready){
			return null;
		}
		if(!$profile){
			$profile = new UserProfile();
		}
		$birth = $this->data['profile_birth'];
		$birthDate = new DateTime('@'.$birth);
		
		$profile->setGender($this->data['profile_gender']);
		$profile->setBirth($birthDate);
		$profile->setDescription($this->data['profile_description']);
		$profile->setObjectives($this->data['profile_objectives']);
		$profile->setQuote($this->data['profile_quote']);
		
		return $profile;
	}
}