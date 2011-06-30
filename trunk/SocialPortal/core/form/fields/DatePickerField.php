<?php

namespace core\form\fields;

use core\tools\Utils;

use core\form\Field;
use DateTime;
class DatePickerField extends Field {
	// value is a timestamp that we convert
	

	/**
	 * @param string $identifier 
	 * @param string $description Translated description
	 * @param array $constraints
	 */
	public function __construct($identifier, $description, array $constraints = array()) {
		parent::__construct( $identifier, $description, 0, 'datepicker', $constraints );
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'datepicker' ) );
	}
	
	public function displayAll() {
		?>
<div class="datebox">
<div class="label_error"><?php
		$this->insertLabel();
		$this->insertErrorMessage();
		?></div><?php
		$this->insertField();
		?></div><?php
	
	}
	
	public function insertLabel() {
		?><label for="<?php
		echo $this->identifier;
		?>_day"><?php
		echo $this->description?></label><?php
	}
	
	public function getValue() {
		if( !$this->value ) {
			return 0;
		}
		$date = mktime( 0, 0, 0, intval( $this->value['month'] ), intval( $this->value['day'] ), intval( $this->value['year'] ) );
		
		return $date;
	}
	
	public function insertField() {
		$timestamp = $this->value;
		$startYear = 1900;
		if( !$timestamp ) {
			$selected = array( 0, 0, 0 );
		} else {
			if( $timestamp instanceof DateTime ) {
				$timestamp = $timestamp->getTimestamp();
			}
			$timestamp = intval( $timestamp );
			// DD
			$selected[] = date( 'j', $timestamp );
			// MM
			$selected[] = date( 'n', $timestamp );
			// YYYY
			$selected[] = intval( date( 'Y', $timestamp ) ) - $startYear + 1;
		}
		$dayId = $this->identifier . '_day';
		$monthId = $this->identifier . '_month';
		$yearId = $this->identifier . '_year';
		$dayName = $this->identifier . '[day]';
		$monthName = $this->identifier . '[month]';
		$yearName = $this->identifier . '[year]';
		//		$dayId = $this->identifier . '_day';
		//		$monthId = $this->identifier . '_month';
		//		$yearId = $this->identifier . '_year';
		// index problem, the ---- is 0
		$startYear -= 1;
		$months = array( __( 'January' ), __( 'February' ), __( 'March' ), __( 'April' ), __( 'May' ), __( 'June' ), __( 'July' ), __( 'August' ), __( 'September' ), __( 'October' ), __( 'November' ), __( 'December' ) );
		?>
<select id="<?php
		echo $dayId?>" name="<?php
		echo $dayName?>" class="<?php
		echo $this->getConstraintsAsString();
		?>">
	<option <?php
		if( 0 == $selected[0] )
			echo 'selected '?> value="">--</option>
			<?php
		for( $i = 1; $i <= 31; $i++ ) :
			?>
				<option <?php
			if( $i == $selected[0] )
				echo 'selected '?> value="<?php
			echo $i;
			?>"><?php
			echo $i;
			?></option>
			<?php
		endfor
		;
		?></select>
<select id="<?php
		echo $monthId?>" name="<?php
		echo $monthName?>"
	class="<?php
		echo $this->getConstraintsAsString();
		?>">
	<option <?php
		if( 0 == $selected[1] )
			echo 'selected '?> value="">------</option>
			<?php
		for( $i = 1; $i <= 12; $i++ ) :
			?>
				<option <?php
			if( $i == $selected[1] )
				echo 'selected '?> value="<?php
			echo $i;
			?>"><?php
			echo $months[$i - 1];
			?></option>
			<?php
		endfor
		;
		?></select>
<select id="<?php
		echo $yearId?>" name="<?php
		echo $yearName?>" class="<?php
		echo $this->getConstraintsAsString();
		?>">
	<option <?php
		if( 0 == $selected[2] )
			echo 'selected '?> value="">----</option>
			<?php
		for( $i = 112; $i >= 1; $i-- ) :
			?>
				<option <?php
			if( $i == $selected[2] )
				echo 'selected '?> value="<?php
			echo ($startYear + $i);
			?>"><?php
			echo ($startYear + $i);
			?></option>
			<?php
		endfor
		;
		?></select>
<?php
	}
	
	public function containsError() {
		$date = $this->getValue();
		$value = $this->value;
		foreach( $this->constraints as $c ) {
			$args = explode( '_', $c );
			$base = array_shift( $args );
			//			list( $base, $args ) = explode( '_', $c, 2 );
			//			$args = explode( '_', $args );
			switch ( $base ) {
				case 'mandatory' :
					if( !((!isset( $value['day'] ) || $value['day']) && (!isset( $value['month'] ) || $value['month']) && (!isset( $value['year'] ) || $value['year'])) ) {
						return __( parent::$errorMessages['mandatory'] );
					}
					break;
			}
		}
		return false;
	}
}	
