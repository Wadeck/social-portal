<?php

namespace socialportal\common\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class RegisterForm extends Form {
	protected $globalMode = 3;
	/** @var TextField */
	protected $newPass;
	/** @var TextField */
	protected $newPassSecond;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'registerForm', $frontController, 'registerSubmit', __( 'Register' ) );
		$this->addInputField( new TextField( 'register_username', __( 'Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		
		// TODO constraint : strong password
		$this->newPass = new TextField( 'register_password', __( 'Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) );
		$this->newPassSecond = new TextField( 'register_password_second', __( 'Retype Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) );
		$this->addInputField( $this->newPass );
		$this->addInputField( $this->newPassSecond );
		
		// TODO constraint : email type
		$this->addInputField( new TextField( 'register_email', __( 'Email' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new TextField( 'register_activation_key', __( 'Key' ), '', 'text', array( 'mandatory' ) ) );
		
		$this->setCss( 'register-form', 'register_form.css' );
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
	
	public function getUsername() {
		return $this->ready ? $this->data['register_username'] : null;
	}
	
	public function getPassword() {
		return $this->ready ? $this->data['register_password'] : null;
	}
	
	public function getEmail() {
		return $this->ready ? $this->data['register_email'] : null;
	}
	
	public function getActivationKey() {
		return $this->ready ? $this->data['register_activation_key'] : null;
	}
}