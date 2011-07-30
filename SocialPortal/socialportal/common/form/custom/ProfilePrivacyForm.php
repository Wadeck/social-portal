<?php

namespace socialportal\common\form\custom;

use core\form\fields\SliderField;

use core\form\fields\DependentDropDownField;

use core\tools\Linker;

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

class ProfilePrivacyForm extends Form {
	protected $globalMode = 2;
	public function __construct(FrontController $front) {
		parent::__construct ( 'ProfilePrivacy', $front, 'formPrivacySubmit', __ ( 'Save' ) );
		$defaultPrivacyValue = array (1 => __ ( 'Public' ), 2 => __ ( 'Registered Users' ), 3 => __ ( 'Myself only' ) );
		
		$this->addInputField ( new SliderField ( 'privacy_gender', __ ( 'Gender Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_birth', __ ( 'Birth Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_description', __ ( 'Description Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_quote', __ ( 'Quote Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_hobbies', __ ( 'Hobbies Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_country', __ ( 'Country Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_state', __ ( 'State Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_activities', __ ( 'Activities Privacy' ), $defaultPrivacyValue, 1 ) );
		$this->addInputField ( new SliderField ( 'privacy_bmi', __ ( 'BMI Privacy' ), $defaultPrivacyValue, 3 ) );
		$this->addInputField ( new SliderField ( 'privacy_mood', __ ( 'Mood Privacy' ), $defaultPrivacyValue, 3 ) );
		
		$em = $this->frontController->getEntityManager ();
		
		$this->setCss ( 'privacy-form', 'privacy_form.css' );
	}
	
	protected function setGlobalMode($globalMode = 1) {
		$this->globalMode = $globalMode;
	}
	
	protected function addInputField(Field $field) {
		parent::addInputField ( $field );
		$field->setMode ( $this->globalMode );
	}
	
	public function setupWithProfile(UserProfile $profile) {
		$args ['privacy_gender'] = $profile->getGenderPrivacy ();
		$args ['privacy_birth'] = $profile->getBirthPrivacy ();
		$args ['privacy_description'] = $profile->getDescriptionPrivacy ();
		$args ['privacy_quote'] = $profile->getQuotePrivacy ();
		$args ['privacy_hobbies'] = $profile->getHobbiesPrivacy ();
		$args ['privacy_country'] = $profile->getCountryPrivacy ();
		$args ['privacy_state'] = $profile->getStatePrivacy ();
		$args ['privacy_activities'] = $profile->getActivityPrivacy ();
		$args ['privacy_bmi'] = $profile->getBmiPrivacy ();
		$args ['privacy_mood'] = $profile->getMoodPrivacy ();
		
		$this->fillWithArray ( $args );
	}
	
	public function updateProfilePrivacy(UserProfile $profile) {
		if (! $this->ready) {
			return null;
		}
		$profile->setGenderPrivacy ( $this->data ['privacy_gender'] );
		$profile->setBirthPrivacy ( $this->data ['privacy_birth'] );
		$profile->setDescriptionPrivacy ( $this->data ['privacy_description'] );
		$profile->setQuotePrivacy ( $this->data ['privacy_quote'] );
		$profile->setHobbiesPrivacy ( $this->data ['privacy_hobbies'] );
		$profile->setCountryPrivacy ( $this->data ['privacy_country'] );
		$profile->setStatePrivacy ( $this->data ['privacy_state'] );
		$profile->setActivityPrivacy ( $this->data ['privacy_activities'] );
		$profile->setBmiPrivacy ( $this->data ['privacy_bmi'] );
		$profile->setMoodPrivacy ( $this->data ['privacy_mood'] );
		
		return $profile;
	}
}