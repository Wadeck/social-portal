<?php

namespace core\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class ProfileEditEmailForm extends Form {
	protected $globalMode = 3;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'editEmailForm', $frontController, 'editEmailSubmit', __( 'Save' ) );
		$this->addInputField( new TextField( 'edit_email_username', __( 'Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new TextField( 'edit_email_password', __( 'Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new TextField( 'edit_email_email', __( 'New Email' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->setCss( 'profile-edit-email-form', 'profile_edit_email_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}

	public function getUsername() {
		return $this->ready ? $this->data['edit_email_username'] : null;
	}
	
	public function getPassword() {
		return $this->ready ? $this->data['edit_email_password'] : null;
	}
	
	public function getEmail() {
		return $this->ready ? $this->data['edit_email_email'] : null;
	}
}