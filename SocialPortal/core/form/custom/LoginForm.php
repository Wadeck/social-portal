<?php

namespace core\form\custom;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class LoginForm extends Form {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( $frontController, 'loginSubmit', __( 'Log in' ) );
		$this->addInputField( new Field( 'username', __( 'Username' ), '', 'text' ) );
		$this->addInputField( new Field( 'password', __( 'Password' ), '', 'password' ) );
		$this->addInputField( new CheckBoxField( 'rememberMe', __( 'Remember me' ), false ) );
	}
}