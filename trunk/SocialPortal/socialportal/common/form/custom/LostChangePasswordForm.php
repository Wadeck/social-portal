<?php

namespace socialportal\common\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class LostChangePasswordForm extends Form {
	protected $globalMode = 3;
	/** @var TextField */
	protected $newPass;
	/** @var TextField */
	protected $newPassSecond;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'lostChangePasswordForm', $frontController, 'lostChangePasswordSubmit', __( 'Save' ) );
		$this->newPass = new TextField( 'lost_change_password_new_password', __( 'New Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) );
		$this->newPassSecond = new TextField( 'lost_change_password_new_password_second', __( 'Retype new Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) );
		$this->addInputField( $this->newPass );
		$this->addInputField( $this->newPassSecond );
		$this->setCss( 'lost-change-password-form', 'lost_change_password_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	/**
	 * Overload of parent validate, test if the two passwords are the same or not
	 * 
	 * @see core\form.Form::validate()
	 */
	protected function validate(){
		$errors = parent::validate();
		$valFirst = $this->newPass->getValue();
		$valSecond = $this->newPassSecond->getValue();
		
		if($valFirst !== $valSecond){
			$errors[$this->newPassSecond->getName()] = __('The two passwords must be the same');
		}
		return $errors;
	}

	public function getNewPassword() {
		return $this->ready ? $this->data['lost_change_password_new_password'] : null;
	}
	
}