<?php

namespace socialportal\common\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class LostUsernameForm extends Form {
	protected $globalMode = 3;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'lostUsernameForm', $frontController, 'lostUsernameSubmit', __( 'Send me an email' ) );
		$this->addInputField( new TextField( 'lost_username_email', __( 'Email' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->setCss( 'lost-username-form', 'lost_username_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	public function getEmail() {
		return $this->ready ? $this->data['lost_username_email'] : null;
	}
}