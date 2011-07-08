<?php

namespace core\form;

use core\Config;

use core\templates\iInsertable;

use core\debug\Logger;

use core\FrontController;

/**
 * Two main utilisation of this class can be used
 * 
 * 1) Creation of the form, using optionnally a basic object

	============ in controller ============
	$form = TopicFormFactory::createForm( $topicType, $this->frontController );
	// case of edition
	$form->setupWithTopic( $currentTopic );
	$form->setNonceAction( 'editTopic' );
	// case of creation
	$form->setNonceAction( 'createTopic' );
	
	$targetUrl = $this->frontController->getViewHelper()->createHref( ... );
	
	// fill the form with the posted field and errors
	$form->setupWithArray( );
	$form->setTargetUrl( $targetUrl );
	
	$this->frontController->getResponse()->setVar( 'form', $form );
	============ in view ============
	$vars['form']->insert();
	 
 * 2) Validation and retrieval of information

	$form = TopicFormFactory::createForm( $typeId, $this->frontController );
	// or $form = new LoginForm($this->frontController);
	// to use information from POST
	$form->setupWithArray( true );
	// validation of the information given, could lead to a redirection
	// if javascript validation doesn't work
	// the php validation will do the job and re-ask the user
	$form->checkAndPrepareContent();
	// finally retrieve information from the form, some form have ability to create object directly
	$xxx = $form->getXXX();
	$yyy = $form->getYYY();

 */
class Form implements iInsertable {
	/** @var FrontController */
	protected $frontController;
	protected $fields;
	protected $mustAcceptFile;
	protected $nonceAction;
	
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
	/** @var array of string containing the names of the javascript file we want to add at the insert time */
	protected $jsFiles;
	
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
		$this->jsFiles = array( 'jquery.js', 'form_validator.js' );
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
//			$this->currentReferrerUrl = $_REQUEST[self::$REFERRER_FIELD_NAME];
			$this->currentReferrerUrl = $_REQUEST[Config::$instance->REFERRER_FIELD_NAME];
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
		$field->setForm( $this );
		$this->fields[] = $field;
		if( $field->isFileAccepter() ) {
			$this->mustAcceptFile = true;
		}
	}
	//#################### construction part #####################
	

	public function _insertReferrerField() {
		$referrer = $this->getFutureReferrerUrl();
		// add this variable into an hidden field
//		echo '<input type="hidden" name="' . self::$REFERRER_FIELD_NAME . '" value="' . $referrer . '">';
		echo '<input type="hidden" name="' . Config::$instance->REFERRER_FIELD_NAME . '" value="' . $referrer . '">';
	}
	
	public function insertNonceField() {
		$nonce = $this->frontController->getNonceManager()->createNonce( $this->nonceAction );
		// add this variable into an hidden field
		echo '<input type="hidden" name="_nonce" value="' . $nonce . '">';
		if( defined( 'DEBUG' ) && DEBUG ) {
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
		?>" class="button"
	value="<?php
		echo $description;
		?>"
	onClick="return validForm(this);">
<?php
	}
	
	public function insertFormBody($actionUrl, $cssClass) {
		echo '<form class="' . $cssClass . '" action="' . $actionUrl . '" method="POST">';
	}
	
	public function insertFields() {
		echo '<div class="form_content">';
		foreach( $this->fields as $key => $field ) {
			echo '<div class="field_container">';
			$field->displayAll();
			echo '</div>';
		}
		echo '</div>';
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
		
		$this->insertFormBody( $this->targetUrl, $cssClass );
		$this->insertFields();
		
		$this->insertNonceField();
		$this->_insertReferrerField();
		$this->insertSubmitButton( $this->submitButtonName, $this->submitButtonDescription );
		
		// doing this stuff after the insertion, allow the fields to register javascript within insert function
		// not really good in term of conception but work for the moment
		$jss = array_unique( $this->jsFiles );
		foreach( $jss as $js ) {
			$this->frontController->getViewHelper()->addJavascriptFile( $js );
		}
		$this->frontController->getViewHelper()->addJavascriptVar( '_error_messages', Field::getErrorMessages() );
		
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
	
	public function addJavascriptFile($jsFile) {
		$this->jsFiles[] = $jsFile;
	}
	//###################### validation part #####################
	

	/**
	 * If the form is invalid, it redirects automatically (except if we passe as parameter false
	 * Enable the use of the children method like ForumForm#getForumName()
	 * @return array $fieldName => $fieldValue if valid, never return if invalid but dispatchUrl(referrer)
	 */
	public function checkAndPrepareContent() {
		$errors = $this->validate();
		if( !empty( $errors ) ) {
			
			// in this case there are some errors, so we display the referrerUrl instead of normal action,
			// but this will be done in the controller, we just return false
			$this->frontController->getRequest()->request->set( $this->formName . '_errors_form', $errors );
			$url = $this->frontController->getRequest()->getReferrer();
			// will exit
//			$this->frontController->doRedirectUrl($url);
			//TODO previous code
			$this->frontController->dispatchUrl( $url );
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
	protected function validate() {
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
	public function setupWithArray($withErrors = true, $forceEmptyForm = false) {
		if( $forceEmptyForm ) {
			$args = array();
		} else {
			$request = $this->frontController->getRequest();
			// from $_POST
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
	
	public function hasSecondAction(){
		return false;
	}
	
	/**
	 * Called only if hasSecondAction return true 
	 * @param Specific topic $topic
	 * @return false if the flush fail, and so will generate error
	 */
	public function doSecondAction($topic){
		return true;
	}
}