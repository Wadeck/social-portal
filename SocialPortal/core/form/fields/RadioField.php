<?php

namespace core\form\fields;

use core\form\Field;

class RadioField extends Field {
	protected $values;
	protected $canBeReset;
	public function __construct($identifier, array $descriptions, $defaultValue, array $values, $canBeReset=false, array $constraints = array()) {
		parent::__construct( $identifier, $descriptions, $defaultValue, 'radio', $constraints );
		if( count( $descriptions ) != count( $values ) ) {
			throw new \InvalidArgumentException( 'The radio fields must have same number of descriptions and values' );
		}
		$this->canBeReset = $canBeReset;
		$this->values = $values;
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'radio' ) );
	}
	
	public function displayAll() {
		$size = count( $this->description );
		$this->insertErrorMessage();
		for( $i = 0; $i < $size; $i++ ) {
			$this->_insertField( $i );
			$this->_insertLabel( $i );
		}
	}
	protected function _insertField($i) {
		?>
<input type="radio" id="<?php
		echo $this->identifier . $i;
		?>"
	name="<?php
		echo $this->identifier;
		?>"
	class="<?php
		echo $this->getConstraintsAsString();
		?>"
	value="<?php
		echo $this->values[$i];
		?>"
	<?php // to determine the initial value
		if( $this->values[$i] == $this->value )
			echo 'checked';
		?> />
<?php
	}
	public function insertField() {}
	
	protected function _insertLabel($i) {
		?><label for="<?php
		echo $this->identifier . $i;
		?>"><?php
		echo $this->description[$i];
		?></label><?php
	}
	public function insertLabel() {}
}