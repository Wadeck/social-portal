<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class DynamicListField extends Field {
	private static $fieldStringPlaceholder = 
		'<input type="text" id="%id%" name="%name%" class="%class%" value="%value%" />';
	/** Used to force the non use of javascript */
	private static $withoutJS = 'dynamicListWithoutJavascript';
	
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
			
			$lastKey = false;
			foreach ($this->values as $key => $value){
				$lastKey = $key;
				?>
				<div class="list_item_field" id="item_<?php echo $key; ?>"><?php
					$this->_insertField( $key, $value );
					$this->insertRemove( $key );
				?></div><?php
			}
			$size = count( $this->values );
			if($size < $this->numberOfInitialRow){
				$s = $this->numberOfInitialRow - $size + $lastKey+1;
				for ($i = $lastKey+1; $i < $s ; $i++){
					?>
					<div class="list_item_field" id="item_<?php echo $i; ?>"><?php
						$this->_insertField( $i );
						$this->insertRemove( $i );
					?></div><?php
				}
			}
			$this->insertAdd( $i + 1 );
		?></div><?php
	}
	
	protected function _insertField($i, $value = false) {
		// '<input type="text" id="%id%" name="%name%" class="%class%" value="%value" />';
		$field = strtr(self::$fieldStringPlaceholder, array(
			'%id%' => $this->identifier . $i,
			'%name%' => $this->identifier,
			'%class%' => $this->getConstraintsAsString(),
			'%value%' => $value)) ;
		echo $field;
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
	
	protected function insertAdd($next){
		$field = strtr(self::$fieldStringPlaceholder, array(
			'%id%' => $this->identifier . '%next%',
			'%name%' => $this->identifier,
			'%class%' => $this->getConstraintsAsString(),
			'%value%' => '')) ;
		?>
		<div class="bold_button" id="add_next">
			<a id="<?php echo $next;?>" class="add_button"
				href="#"
				title="<?php echo __('Add another field'); ?>"
				onClick="addField(this, '<?php echo $field; ?>'); return false;"><?php
					echo '+';
				?></a>
		</div>
		<?php
	}
	
}