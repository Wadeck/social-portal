<?php

namespace core\form\fields;

use core\form\Field;

/** @Tested */
class SingleCheckBoxField extends Field {
	public function __construct($identifier, $description, $defaultValue, array $constraints = array()) {
		parent::__construct( $identifier, $description, $defaultValue, 'checkbox', $constraints );
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'checkbox' ) );
	}
	
	public function displayAll() {
		switch ( $this->mode ) {
			case 1 :
				$this->insertLabel();
				$this->insertField();
				$this->insertErrorMessage();
				break;
			case 2 :
				$this->insertField();
				$this->insertLabel();
				$this->insertErrorMessage();
				break;
		}
	}
	
	public function insertField() {
		?>
<input type="checkbox" id="<?php
		echo $this->identifier;
		?>"
	name="<?php
		echo $this->identifier;
		?>"
	class="<?php
		echo $this->getConstraintsAsString();
		?>"
	value="<?php
		echo $this->value;
		?>" />
<?php
	}
	
	public function insertLabel() {
		?><label for="<?php
		echo $this->identifier;
		?>"><?php
		echo $this->description;
		?></label><?php
	}
}