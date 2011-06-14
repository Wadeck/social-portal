<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class TextField extends Field {
	private $cleanText;
	public function __construct($identifier, $description, $value, $type, array $constraints = array(), $cleanText=true) {
		parent::__construct( $identifier, $description, $value, $type, $constraints );
		$this->cleanText = $cleanText;
	}
	
	public function getValue(){
		return Utils::getCleanText($this->value);
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'text', 'password' ) );
	}
}