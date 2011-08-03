<?php

namespace socialportal\common\form\custom;

use core\form\fields\DatePickerField;

use core\form\fields\TextField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class AddBmiValueForm extends Form {
	public function __construct(FrontController $frontController) {
		parent::__construct( 'AddBmiValue', $frontController, 'formAddBmiValueSubmit', __( 'Add' ) );
		$this->addInputField( new TextField( 'bmi_height', __( 'Height [cm]' ), '', 'text', array( 'mandatory', 'integer', 'value_greater-equal-than_60', 'value_less-than_250' ) ) );
		$this->addInputField( new TextField( 'bmi_weight', __( 'Weight [kg]' ), '', 'text', array( 'mandatory', 'integer', 'value_greater-equal-than_20', 'value_less-than_500' ) ) );
		$this->addInputField( new DatePickerField( 'bmi_date', __( 'Date of weighing' ), array( 'mandatory' ) ) );
		$this->setCss( 'bmi-add-form', 'bmi_add_form.css' );
	}
	
	/**
	 * @return float
	 */
	public function getBmiValue() {
		if( !$this->ready ){
			return null;
		}
		$height = $this->data['bmi_height'];
		$weight = $this->data['bmi_weight'];
		$height = intval($height);
		$weight = intval($weight);
		$bmi = $weight / ($height * $height / 10000);
		$bmi = (integer)($bmi*10);
		return $bmi;
	}
	
	public function getDate(){
		return $this->ready ? $this->data['bmi_date'] : null;
	}
}