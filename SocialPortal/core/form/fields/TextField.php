<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class TextField extends Field {
	private $cleanText;
	public function __construct($identifier, $description, $value, $type, array $constraints = array(), $cleanText = true) {
		parent::__construct( $identifier, $description, $value, $type, $constraints );
		$this->cleanText = $cleanText;
	}
	
	public function getValue() {
		return Utils::getCleanText( $this->value );
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'text', 'password' ) );
	}
	
	public function displayAll() {
		switch ( $this->mode ) {
			case 1 :
				$this->insertLabel();
				$this->insertField();
				$this->insertErrorMessage();
				break;
			case 2 :
				?><div class="label_error"><?php
				$this->insertLabel();
				$this->insertErrorMessage();
				?></div><?php
				$this->insertField();
				break;
		}
	}
	
	/**
	 * 1)
	 * label
	 * input
	 * error
	 * 
	 * 2)
	 * label
	 * error
	 * input
	 */
	public function setMode($mode = 1) {
		$this->mode = $mode;
		return $this;
	}
}