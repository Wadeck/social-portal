<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;

class StaticListField extends Field {
	private static $fieldStringPlaceholder = 
		'<input type="text" id="%id%" name="%name%" class="%class%" value="%value%" />';
	/** Used to force the non use of javascript */
	private static $withoutJS = 'dynamicListWithoutJavascript';
	
	protected $values;
	protected $canBeReset;
	protected $numberOfInitialRow;
	public function __construct($identifier, $description,  array $values, $numberOfInitialRow = 5, array $constraints = array()) {
		parent::__construct( $identifier, $description, array(), 'list', $constraints );
		$this->values = $values;
		$this->numberOfInitialRow = $numberOfInitialRow;
	}
	
	public function setValue($value) {
		$this->value = $value;
		if($value && is_array($value)){
			$this->values = $value;
		}
	}

	public function getValue() {
		$result = array();
		$i = 1;
		foreach ($this->value as $val){
			if($val && ($clean = Utils::getCleanText($val))){
				$result[$i++] = $clean;
			}
		}
		return $result;
//		return $this->value;
	}
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
				?></div><?php
			}
			$size = count( $this->values );
			if($size < $this->numberOfInitialRow){
				$s = $this->numberOfInitialRow - $size + $lastKey+1;
				for ($i = $lastKey+1; $i < $s ; $i++){
					?>
					<div class="list_item_field" id="item_<?php echo $i; ?>"><?php
						$this->_insertField( $i );
					?></div><?php
				}
			}
		?></div><?php
	}
	
	protected function _insertField($i, $value = false) {
		// '<input type="text" id="%id%" name="%name%" class="%class%" value="%value" />';
		$field = strtr(self::$fieldStringPlaceholder, array(
			'%id%' => $this->identifier . $i,
			'%name%' => $this->identifier."[$i]",
			'%class%' => $this->getConstraintsAsString(),
			'%value%' => $value)) ;
		echo $field;
	}
	
	public function insertField() {}
	
}