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
	
	public function insertFormBody( $actionUrl, $cssClass ){
		echo '<div class="login-form-container">';
		?>
		<table>
			<tr>
				<td><a class="visitor"
					title="<?php echo __( 'Enter as visitor' ); ?>"
					href="<?php $this->frontController->getViewHelper()->insertHref( 'Connection', 'logAsVisitor' )?>">
					<?php echo __( 'Enter as visitor' ); ?></a></td>
				<td><a class="sign-in"
					href="<?php $this->frontController->getViewHelper()->insertHref( 'Connection', 'displayRegisterForm' )?>">
					<?php echo __( 'Create an account' ); ?></a></td>
			</tr>
		</table>

	<?php
		parent::insertFormBody($actionUrl, $cssClass);
	}
	
	public function insertFormBodyEnd(){
		parent::insertFormBodyEnd();
		?>
		<table>
			<tr>
				<td><a class="lost-username"
			href="<?php $this->frontController->getViewHelper()->insertHrefWithNonce( 'displayLostUsernameForm', 'Connection', 'displayLostUsernameForm' )?>">
			<?php echo __( 'Lost username' ); ?></a></td>
				<td><a class="lost-password"
			href="<?php $this->frontController->getViewHelper()->insertHrefWithNonce( 'displayLostPasswordForm', 'Connection', 'displayLostPasswordForm' )?>">
			<?php echo __( 'Lost password' ); ?></a></td>
			</tr>
		</table>
		<?php
		echo '</div>';
	}
		
//	
//	/**
//	 * @param string $name
//	 * @param string $description Should be translater
//	 */
//	public function insertSubmitButton($name, $description) {
//		parent::insertSubmitButton( $name, $description );
//	}
	
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