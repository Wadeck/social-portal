<?php

namespace core\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class ProfileEditUsernameForm extends Form {
	protected $globalMode = 3;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'editUsernameForm', $frontController, 'editUsernameSubmit', __( 'Save' ) );
		$this->addInputField( new TextField( 'edit_username_old_username', __( 'Old Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new TextField( 'edit_username_password', __( 'Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new TextField( 'edit_username_new_username', __( 'New Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->setCss( 'profile-edit-username-form', 'profile_edit_username_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	public function getOldUsername() {
		return $this->ready ? $this->data['edit_username_old_username'] : null;
	}
	
	public function getPassword() {
		return $this->ready ? $this->data['edit_username_password'] : null;
	}
	
	public function getNewUsername() {
		return $this->ready ? $this->data['edit_username_new_username'] : null;
	}
}