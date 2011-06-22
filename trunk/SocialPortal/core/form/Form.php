<?php

namespace core\form;

use core\topics\templates\iInsertable;

use core\debug\Logger;

use core\FrontController;

class Form implements iInsertable {
	private static $REFERRER_FIELD_NAME = '_http_referrer';
	
	/** @var FrontController */
	protected $frontController;
	protected $fields;
	private $mustAcceptFile;
	private $nonceAction;
	
	protected $submitButtonDescription;
	protected $submitButtonName;
	
	protected $futureReferrerUrl;
	protected $currentReferrerUrl;
	
	/** @var string The action field in form tag */
	protected $targetUrl;
	/** @var string The css class that will receive the form tag */
	protected $cssClass;
	/** @var string The css file that will be included additionnally to form.css */
	protected $cssFile;
	
	/** @var array containing $fieldName => $fieldValue used in the children to exhibit attributes */
	protected $data;
	/** @var boolean flag when set to true, enable the use of the children attributes */
	protected $ready;
	
	protected $formName;
	
	/**
	 * 
	 * @param string $formName used to set and retrieve the good information in post etc
	 * @param FrontController $front
	 * @param string $submitName
	 * @param string $submitDescription
	 */
	protected function __construct($formName, FrontController $front, $submitName = '', $submitDescription = '') {
		$this->formName = $formName;
		$this->frontController = $front;
		$this->fields = array();
		$this->submitButtonName = $submitName ? $submitName : 'submit';
		$this->submitButtonDescription = $submitDescription ? $submitDescription : __( 'Submit' );
	}
	
	//#################### dependant of the context ############################
	/**
	 * This method must be used in conjonction with the annotation @Nonce($nonceAction) in the reception method
	 * @param string $nonceAction
	 */
	public function setNonceAction($nonceAction) {
		$this->nonceAction = $nonceAction;
	}
	
	/** @return string Url */
	public function getCurrentReferrerUrl() {
		if( !$this->currentReferrerUrl ) {
			$this->currentReferrerUrl = $_REQUEST[self::$REFERRER_FIELD_NAME];
		}
		return $this->currentReferrerUrl;
	}
	
	public function getFutureReferrerUrl() {
		if( !$this->futureReferrerUrl ) {
			// always valid, the future referrer is the current page url
			$this->futureReferrerUrl = $this->frontController->getRequest()->getRequestedUrl();
		}
		return $this->futureReferrerUrl;
	}
	
	//#################### set up in children #########################
	

	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		$this->fields[] = $field;
		if( $field->isFileAccepter() ) {
			$this->mustAcceptFile = true;
		}
	}
	//#################### construction part #####################
	

	public function _insertReferrerField() {
		$referrer = $this->getFutureReferrerUrl();
		// add this variable into an hidden field
		echo '<input type="hidden" name="' . self::$REFERRER_FIELD_NAME . '" value="' . $referrer . '">';
	}
	
	public function insertNonceField() {
		$nonce = $this->frontController->getNonceManager()->createNonce( $this->nonceAction );
		// add this variable into an hidden field
		echo '<input type="hidden" name="_nonce" value="' . $nonce . '">';
		if(defined('DEBUG') && DEBUG){
			echo '<input type="hidden" name="_nonce_clear" value="' . $this->nonceAction . '">';
		}
	}
	
	/**
	 * @param string $name
	 * @param string $description Should be translater
	 */
	public function insertSubmitButton($name, $description) {
		?>
<input type="submit" name="<?php
		echo $name;
		?>" class="button" value="<?php
		echo $description;
		?>"
	onClick="return validForm(this);">
<?php
	}
	
	public function insertFormBody($actionUrl, $cssClass) {
		echo '<form class="' . $cssClass . '" action="' . $actionUrl . '" method="POST">';
	}
	
	public function insertFields() {
		foreach( $this->fields as $key => $field ) {
			echo '<div class="field_container">';
			$field->displayAll();
			echo '</div>';
		}
	}
	
	//#################### creation part #####################
	

	/**
	 * Display the form in the view part without having to pass parameters
	 * @echo
	 */
	public function insert() {
		if( !$this->targetUrl ) {
			throw new \ErrorException( 'The target URL for the form is not set, please use setTarget / setTargetUrl' );
		}
		$cssClass = $this->cssClass ? $this->cssClass : 'generic_form';
		
		$this->frontController->getViewHelper()->addCssFile( 'form.css' );
		if( $this->cssFile ) {
			$this->frontController->getViewHelper()->addCssFile( $this->cssFile );
		}
		$this->frontController->getViewHelper()->addJavascriptFile( 'jquery.js' );
		$this->frontController->getViewHelper()->addJavascriptFile( 'form_validator.js' );
		$this->frontController->getViewHelper()->addJavascriptVar( '_error_messages', Field::getErrorMessages() );
		
		$this->insertFormBody( $this->targetUrl, $cssClass );
		$this->insertFields();
		
		$this->insertNonceField();
		$this->_insertReferrerField();
		$this->insertSubmitButton( $this->submitButtonName, $this->submitButtonDescription );
		
		echo '</form>';
	}
	
	public function setTarget($module, $action) {
		$url = $this->frontController->getViewHelper()->createHref( $module, $action );
		$this->setTargetUrl( $url );
	}
	public function setTargetUrl($url) {
		$this->targetUrl = $url;
	}
	/**
	 * @param string $cssClass The css class of the form tag
	 * @param string $cssFile The css file that will be included (no need to specify form.css)
	 */
	public function setCss($cssClass, $cssFile = '') {
		$this->cssClass = $cssClass;
		$this->cssFile = $cssFile;
	}
	//###################### validation part #####################
	

	/**
	 * If the form is invalid, it redirects automatically (except if we passe as parameter false
	 * Enable the use of the children method like ForumForm#getForumName()
	 * @return array $fieldName => $fieldValue if valid, false otherwise and we assume that the controller will doReferrerAction
	 */
	public function checkAndPrepareContent() {
		$errors = $this->validate();
		if( !empty( $errors ) ) {
			
			// in this case there are some errors, so we display the referrerUrl instead of normal action,
			// but this will be done in the controller, we just return false
			$this->frontController->getRequest()->request->set( $this->formName . '_errors_form', $errors );
			$url = $this->frontController->getRequest()->getReferrer();
			// will exit
			$this->frontController->dispatch( $url );
		}
		$result = array();
		foreach( $this->fields as $field ) {
			$result[$field->getName()] = $field->getValue();
		}
		$this->data = $result;
		$this->ready = true;
		return $result;
	}
	
	/** @return array $fieldName => $error */
	private function validate() {
		$errors = array();
		foreach( $this->fields as $field ) {
			$error = $field->containsError();
			if( $error !== false ) {
				$errors[$field->getName()] = $error;
			}
		}
		return $errors;
	}
	
	/**
	 * After this call, we can use either validate / display
	 */
	public function setupWithArray($withErrors, $forceEmptyForm = false) {
		if( $forceEmptyForm ) {
			$args = array();
		} else {
			$request = $this->frontController->getRequest();
			
			$args = $request->request->all();
			if( isset( $args[$this->formName . '_errors_form'] ) ) {
				// means we are in the case the form was already created (and then deleted) but found a validation error, and so add it to the $_POST
				$errors = $args[$this->formName . '_errors_form'];
			} else {
				// no error, we don't have to care about them
				$errors = array();
			}
		}
		
		$this->fillWithArray( $args, $errors );
	}
	
	protected function fillWithArray(array $args = array(), array $errors = array()) {
		if( empty( $args ) && empty( $errors ) ) {
			return;
		}
		foreach( $this->fields as $field ) {
			$fieldName = $field->getName();
			if( isset( $args[$fieldName] ) ) {
				$field->setValue( $args[$fieldName] );
			}
			if( $errors && isset( $errors[$fieldName] ) ) {
				$field->setError( $errors[$fieldName] );
			}
		}
	}
	
	//#############################
	//TODO remove those methods after having changed the login form
	

	public static function insertReferrerField() {
		$referrer = FrontController::getInstance()->getRequest()->getRequestedUrl();
		echo '<input type="hidden" name="' . self::$REFERRER_FIELD_NAME . '" value="' . $referrer . '">';
	}
}