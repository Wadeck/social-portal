<?php

namespace core\debug;

// TODO en cours d'elaboration, need refactoring
class Tester {
	private static $num_test;
	private static $num_correct;
	public static function startMultiTest() {
		self::$num_correct = 0;
		self::$num_test = 0;
		ob_start();
	}
	public static function endMultiTest($pre_message, $post_message = '') {
		ob_clean();
		if( self::$num_test == self::$num_correct ) {
			// all were correct
			self::displayCorrect( $pre_message . ': Result (Correct):' . self::$num_correct . '/' . self::$num_test . ' ' . $post_message );
		} else {
			self::displayError( $pre_message . ': Result (Failure):' . self::$num_correct . '/' . self::$num_test . ' ' . $post_message );
		}
	}
	
	private static function displayCorrect($message) {
		self::$num_test++;
		self::$num_correct++;
		?>
<div style="border: 2px solid green; margin: 2px;"><?php
		echo "$message";
		?></div>
<?php
	}
	
	private static function displayError($message) {
		self::$num_test++;
		?>
<div style="border: 2px solid red; margin: 2px;"><?php
		echo "$message";
		?></div>
<?php
	}
	
	private static function formatMessage($title, $message, $result, $expected, $type_of_test, $is_correct) {
		$message = "<b>$message</b> |" . ($result ? $result : 'null') . "| $type_of_test " . ($expected ? $expected : 'null') . " . . . $title";
		if( $is_correct ) {
			self::displayCorrect( $message );
		} else {
			self::displayError( $message );
		}
	}
	public static function assertEquals($result, $expected, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( $result == $expected ) {
			self::formatMessage( $title, $true_message, $result, $expected, '==', true );
		} else {
			self::formatMessage( $title, $false_message, $result, $expected, '!=', false );
		}
	}
	public static function assertNotEquals($result, $expected, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( $result != $expected ) {
			self::formatMessage( $title, $true_message, $result, $expected, '!=', true );
		} else {
			self::formatMessage( $title, $false_message, $result, $expected, '==', false );
		}
	}
	public static function assertNull($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( isset( $result ) ) {
			self::formatMessage( $title, $true_message, $result, 'null', 'is', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'null', 'is not', false );
		}
	}
	public static function assertNonNull($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( !isset( $result ) ) {
			self::formatMessage( $title, $true_message, $result, 'null', 'is not', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'null', 'is', false );
		}
	}
	public static function assertIsFalse($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( false === $result ) {
			self::formatMessage( $title, $true_message, $result, 'false', '===', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'false', '!==', false );
		}
	}
	public static function assertIsNotFalse($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( false !== $result ) {
			self::formatMessage( $title, $true_message, $result, 'false', '!==', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'false', '===', false );
		}
	}
	public static function assertIsset($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( isset( $result ) ) {
			self::formatMessage( $title, $true_message, $result, 'set', 'is', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'set', 'is not', false );
		}
	}
	public static function assertIsNotset($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( !isset( $result ) ) {
			self::formatMessage( $title, $true_message, $result, 'set', 'is not', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'set', 'is', false );
		}
	}
	public static function assertValid($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( isset( $result ) && false != $result ) {
			self::formatMessage( $title, $true_message, $result, 'valid', 'is', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'valid', 'is not', false );
		}
	}
	public static function assertGreaterThan($result, $value, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( $result > $value ) {
			self::formatMessage( $title, $true_message, $result, $value, '>', true );
		} else {
			self::formatMessage( $title, $false_message, $result, $value, '<=', false );
		}
	}
	public static function assertLessThan($result, $value, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( $result < $value ) {
			self::formatMessage( $title, $true_message, $result, $value, '<', true );
		} else {
			self::formatMessage( $title, $false_message, $result, $value, '>=', false );
		}
	}
	public static function assertGreaterEqualsThan($result, $value, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( $result >= $value ) {
			self::formatMessage( $title, $true_message, $result, $value, '>=', true );
		} else {
			self::formatMessage( $title, $false_message, $result, $value, '<', false );
		}
	}
	public static function assertLessEqualsThan($result, $value, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( $result <= $value ) {
			self::formatMessage( $title, $true_message, $result, $value, '<=', true );
		} else {
			self::formatMessage( $title, $false_message, $result, $value, '>', false );
		}
	}
	public static function assertNotValid($result, $title = '', $true_message = "Correct", $false_message = "Error") {
		if( !isset( $result ) || false == $result ) {
			self::formatMessage( $title, $true_message, $result, 'valid', 'is not', true );
		} else {
			self::formatMessage( $title, $false_message, $result, 'valid', 'is', false );
		}
	}
}

?>
