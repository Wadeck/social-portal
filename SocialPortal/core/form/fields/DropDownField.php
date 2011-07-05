<?php

namespace core\form\fields;

use core\form\Field;

/** @UnTested */
class DropDownField extends Field {
	// protected $value inherit from Field contains the value of the currently selected item
	protected $values;
	protected $fieldDescriptions;
	
	public function __construct($identifier, $description, array $fieldDescriptions, array $values, $defaultValue, array $constraints = array()) {
		parent::__construct( $identifier, $description, $defaultValue, 'select', $constraints );
		if( count( $fieldDescriptions ) != count( $values ) ) {
			throw new \InvalidArgumentException( 'The select fields must have same number of descriptions and values' );
		}
		$this->fieldDescriptions = $fieldDescriptions;
		$this->values = $values;
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'select' ) );
	}
	
	public function displayAll() {
		// radio_field div is necessary for the reset button
	?>
		<div class="dropdown_field">
		<div class="label_error"><?php
		$this->insertLabel();
		$this->insertErrorMessage();
		?></div>
		<select name="<?php echo $this->identifier; ?>"
			id="<?php echo $this->identifier; ?>">
			<?php
			$size = count( $this->fieldDescriptions );
			for( $i = 0; $i < $size; $i++ ) {
				$this->_insertField( $i );
			}
			?>
		</select>
		</div>
	<?php
	}
	protected function _insertField($i) {
		?>
		<option
			class="<?php echo $this->getConstraintsAsString(); ?>"
			value="<?php echo $this->values[$i]; ?>"
		<?php // to determine the initial value
			if( $this->values[$i] == $this->value ) echo 'selected'; ?>>
		<?php echo $this->fieldDescriptions[$i]; ?>
			</option>
<?php
	}
	public function insertField() {}
	
	protected function insertReset(){
		$this->form->addJavascriptFile('jquery.js');
		$this->form->addJavascriptFile('radio_field_reset.js');
		?>
		<span class="radio_reset"><a href="#" onClick="radio_reset(this); return false;"><?php echo __('Clear'); ?></a></span>
		<?php
	}
}