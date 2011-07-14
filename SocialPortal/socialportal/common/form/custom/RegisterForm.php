<?php

namespace socialportal\common\form\custom;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class RegisterForm extends Form {
	protected $globalMode = 3;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'registerForm', $frontController, 'registerSubmit', __( 'Register' ) );
		$this->addInputField( new TextField( 'register_username', __( 'Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		// TODO constraint : strong password
		$this->addInputField( new TextField( 'register_password', __( 'Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) ) );
		// TODO constraint : email type
		$this->addInputField( new TextField( 'register_email', __( 'Email' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->setCss( 'register-form', 'register_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		//		$this->fields[] = $field;
		//		$field->setMode( $this->globalMode );
		//		if( $field->isFileAccepter() ) {
		//			$this->mustAcceptFile = true;
		//		}
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	/**
	 * @param string $name
	 * @param string $description Should be translater
	 */
	/*
	public function insertSubmitButton($name, $description) {
		parent::insertSubmitButton( $name, $description );
		?>
<a class="sign-in"
	href="<?php
		$this->frontController->getViewHelper()->insertHref( 'Connection', 'displayRegisterForm' )?>"><?php
		echo __( 'Sign in' );
		?></a>
<?php
	}*/
	
	public function getUsername() {
		return $this->ready ? $this->data['register_username'] : null;
	}
	
	public function getPassword() {
		return $this->ready ? $this->data['register_password'] : null;
	}
	
	public function getEmail() {
		return $this->ready ? $this->data['register_email'] : null;
	}
}