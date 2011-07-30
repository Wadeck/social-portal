<?php

namespace core\form\fields;

use core\form\Field;

/** @Untested */
class SliderField extends Field {
	protected $fieldDescriptions;
	
	public function __construct($identifier, $description, array $fieldDescriptions, $defaultValue, $canBeReset = false, array $constraints = array()) {
		parent::__construct( $identifier, $description, $defaultValue, 'slider', $constraints );
		$this->fieldDescriptions = $fieldDescriptions;
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'slider' ) );
	}
	
	public function displayAll() {
		$this->form->addJavascriptFile('jquery.js');
		$this->form->addJavascriptFile('jquery-ui.js');
		$this->form->addJavascriptFile('slider_input.js');
		$this->form->addJavascriptVariable($this->identifier . '_descriptions', $this->fieldDescriptions);
		$this->form->addCssFile('jquery-ui.css');

		// radio_field div is necessary for the reset button
		?>
		<div class="sliderInput">
			<div class="label_error"><?php
				$this->insertLabel();
				$this->insertErrorMessage();?>
			</div>
			<?php
				$this->insertField( );
			?>
		</div>
	<?php
	}
	public function insertField() {
		?>
		<div class="slider default_<?php echo $this->value; ?> min_1 max_<?php echo count($this->fieldDescriptions); ?> step_1">
			<!-- slider will be inserted here by javascript -->
		</div>
		<input type="hidden" class="slider_input" name="<?php echo $this->identifier; ?>" value="0">
		<div class="slider_description">
			<!-- description will be inserted here by javascript -->
		</div>
	<?php
	}
}