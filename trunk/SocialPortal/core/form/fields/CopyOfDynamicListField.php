<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class DynamicListField extends Field {
	protected $values;
	protected $canBeReset;
	protected $numberOfInitialRow;
	public function __construct($identifier, $description,  array $values, $numberOfInitialRow = 3, array $constraints = array()) {
		parent::__construct( $identifier, $description, array(), 'list', $constraints );
		$this->values = $values;
		$this->numberOfInitialRow = $numberOfInitialRow;
	}
	
//
//	public function setValue($value) {
//		$this->value = $value;
//		if(){
//			
//		}
//	}
//	
//	public function getValue() {
//		return $this->value;
//	}
//	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'list' ) );
	}
	
	public function displayAll() {
		$this->form->addJavascriptFile('jquery.js');
		$this->form->addJavascriptFile('list_field.js');
	?>
		<div class="list_field">
			<div class="label_error"><?php
				$this->insertLabel();
				$this->insertErrorMessage();
			?></div><?php
			
			$size = count( $this->values );
			$secondSize = $this->numberOfInitialRow;
			for( $i = 0; $i < $size; $i++ ) {
				?>
				<div class="list_item_field" id="item_<?php echo $i; ?>"><?php
					$this->_insertField( $i );
					$this->insertRemove( $i );
				?></div><?php
			}
			for( $j = $size; $j < $secondSize; $j++ ) {
				?>
				<div class="list_item_field" id="item_<?php echo $j; ?>"><?php
					$this->_insertField( false );
					$this->insertRemove( $j );
				?></div><?php
			}
			$this->insertAdd();
		?></div><?php
	}
	protected function _insertField($i=false) {
	?>
		<input type="text"
			id="<?php
				echo $this->identifier . $i; ?>"
			name="<?php
				echo $this->identifier; ?>"
			class="<?php
				echo $this->getConstraintsAsString(); ?>"
			value="<?php if(false !== $i) echo $this->values[$i]; ?>" /><?php
	}
	protected function insertRemove($i){
		?>
		<span class="bold_button">
			<a class="remove_button"
				href="#"
				onClick="removeField(this, <?php echo $i; ?>); return false;"
				title="<?php echo __('Remove this field'); ?>"><?php
					echo 'x';
				?></a>
		</span>
		
	<?php
	}
	
	public function insertField() {}
	
	protected function insertAdd(){
		?>
		<div class="bold_button" id="add_next">
			<a class="add_button"
				href="#"
				title="<?php echo __('Add another field'); ?>"
				onClick="addField(this); return false;"><?php
					echo '+';
				?></a>
		</div>
		<?php
	}
}