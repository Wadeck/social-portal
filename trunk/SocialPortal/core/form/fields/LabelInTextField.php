<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class LabelInTextField extends TextField {
	public function __construct($identifier, $description, $value, $type, array $constraints = array(), $cleanText = true) {
		parent::__construct( $identifier, $description, $value, $type, $constraints, $cleanText );
	}
	
	/**
	 * This is also use to inform the class attribute of the field
	 * @return array of string representing the constraints
	 */
	public function getConstraintsAsString() {
		$classes = parent::getConstraintsAsString();
		$classes .= ' labelInInput';
		return $classes;
	}
	
	/** no label because it is in the input field */
	public function insertLabel() {
		$this->form->addJavascriptFile( 'label_in_input.js' );
	}
}