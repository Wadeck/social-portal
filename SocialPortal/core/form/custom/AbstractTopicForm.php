<?php

namespace core\form\custom;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

abstract class AbstractTopicForm extends Form implements iTopicForm {
	protected $globalMode = 2;
	protected function __construct($formName, FrontController $front, $submitName = '', $submitDescription = '') {
		parent::__construct( $formName, $front, $submitName, $submitDescription );
		$this->setCss( 'topic-form', 'topic_form.css' );
	}
	
	protected function setGlobalMode($globalMode = 1) {
		$this->globalMode = $globalMode;
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
}