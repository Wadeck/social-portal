<?php

namespace core\form\fields;

use core\form\Field;

class RadioField extends Field {
	protected $values;
	protected $fieldDescriptions;
	protected $canBeReset;
	public function __construct($identifier, $description, array $fieldDescriptions, $defaultValue, array $values, $canBeReset = false, array $constraints = array()) {
		parent::__construct( $identifier, $description, $defaultValue, 'radio', $constraints );
		if( count( $fieldDescriptions ) != count( $values ) ) {
			throw new \InvalidArgumentException( 'The radio fields must have same number of descriptions and values' );
		}
		$this->fieldDescriptions = $fieldDescriptions;
		$this->canBeReset = $canBeReset;
		$this->values = $values;
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'radio' ) );
	}
	
	public function displayAll() {
		// radio_field div is necessary for the reset button
	?>	<div class="radio_field">
		<div class="label_error"><?php
		$this->insertLabel();
		$this->insertErrorMessage();
		?></div><?php
		
		$size = count( $this->fieldDescriptions );
		for( $i = 0; $i < $size; $i++ ) {
			$this->_insertField( $i );
			$this->_insertLabel( $i );
		}
		if($this->canBeReset){
			$this->insertReset();
		}
		?></div><?php
	}
	protected function _insertField($i) {
		?>
<input type="radio" id="<?php
		echo $this->identifier . $i;
		?>" name="<?php
		echo $this->identifier;
		?>"
	class="<?php
		echo $this->getConstraintsAsString();
		?>" value="<?php
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
		echo $this->fieldDescriptions[$i];
		?></label><?php
	}
	
	protected function insertReset(){
		$this->form->addJavascriptFile('jquery.js');
		$this->form->addJavascriptFile('radio_field_reset.js');
		?>
		<span class="radio_reset"><a href="#" onClick="radio_reset(this); return false;"><?php echo __('Clear'); ?></a></span>
		<?php
	}
}