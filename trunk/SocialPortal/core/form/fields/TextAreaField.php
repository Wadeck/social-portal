<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class TextAreaField extends Field {
	private $cleantText;
	public function __construct($identifier, $description, $value, array $constraints = array(), $cleanText = true) {
		parent::__construct( $identifier, $description, $value, 'textarea', $constraints );
		$this->cleanText = $cleanText;
	}
	
	public function getValue() {
		return Utils::getCleanText( $this->value );
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'textarea' ) );
	}
	
	public function insertField() {
		?>
<textarea id="<?php
		echo $this->identifier;
		?>" name="<?php
		echo $this->identifier;
		?>"
	class="<?php
		echo $this->getConstraintsAsString();
		?>"><?php
		echo $this->getValue();
		?></textarea>
<?php
	}
}