<?php

namespace socialportal\common\form\custom;

use core\form\fields\LabelInTextField;

use core\form\fields\SingleCheckBoxField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class LoginForm extends Form {
	protected $globalMode = 2;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'loginForm', $frontController, 'loginSubmit', __( 'Log in' ) );
		$this->addInputField( new LabelInTextField( 'login_username', __( 'Username' ), '', 'text', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new LabelInTextField( 'login_password', __( 'Password' ), '', 'password', array( 'mandatory', 'strlen_at-least_5' ) ) );
		$this->addInputField( new SingleCheckBoxField( 'login_rememberMe', __( 'Remember me' ), false ) );
		$this->setCss( 'login-form rounded-box', 'login_form.css' );
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
	public function insertSubmitButton($name, $description) {
		parent::insertSubmitButton( $name, $description );
		?>
<a class="sign-in"
	href="<?php
		$this->frontController->getViewHelper()->insertHref( 'Connection', 'displayRegisterForm' )?>"><?php
		echo __( 'Sign in' );
		?></a>
<?php
	}
	
	public function getUsername() {
		return $this->ready ? $this->data['login_username'] : null;
	}
	
	public function getPassword() {
		return $this->ready ? $this->data['login_password'] : null;
	}
	
	public function getIsRememberMe() {
		return $this->ready ? $this->data['login_rememberMe'] : null;
	}
}