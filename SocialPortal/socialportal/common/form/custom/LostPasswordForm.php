<?php

namespace socialportal\common\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class LostPasswordForm extends Form {
	protected $globalMode = 3;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'lostPasswordForm', $frontController, 'lostPasswordSubmit', __( 'Send me an email' ) );
		$this->addInputField( new TextField( 'lost_password_username', __( 'Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new TextField( 'lost_password_email', __( 'Email' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->setCss( 'lost-password-form', 'lost_password_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	public function getUsername() {
		return $this->ready ? $this->data['lost_password_username'] : null;
	}
	
	public function getEmail() {
		return $this->ready ? $this->data['lost_password_email'] : null;
	}
}