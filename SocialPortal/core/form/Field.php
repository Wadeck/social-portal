<?php

namespace core\form;

class Field {
	private static $errorMessages;
	
	/** @var array mandatory|optional|not-default|value_greater-equal-than_x|count_less-than_y */
	protected $constraints;
	/** @var string "input"|"password"|"hidden"|"text"|"checkbox"|"radio"|"file" */
	protected $type;
	/** @var string The current value of the field, set in constructor to the default value and then adjust */
	protected $value;
	/** @var string Default value for the field */
	protected $default;
	/** @var string internal use only */
	protected $identifier;
	/** @var string Should be translater */
	protected $description;
	/** @var string Should be translater */
	protected $error;
	
	/** @var different mode of display */
	protected $mode = 1;
	
	/** @var Form that is responsible to that field */
	protected $form;
	
	private static function initMessages() {
		self::$errorMessages = array( 'form_already_sent' => __js( 'This form was already sent to the server, please refresh the page' ), 'mandatory' => __js( 'This field cannot be empty' ), 'optional' => '', 'strlen_lessthan' => __js( 'The length of the answer must be less than %value%' ), 'strlen_lessequal' => __js( 'The length of the answer must be less than %value% or equal' ), 'strlen_atleast' => __js( 'The length of the answer must be at least %value%' ), 'value_lessthan' => __js( 'The value of this field must be less than %value%' ), 'value_greaterequalthan' => __js( 'The value of this field must be greater or equal than %value%' ), 'value_greaterthan' => __js( 'The value of this field must be greater than %value%' ), 'value_notequal' => __js( 'The default (%value%) is not accepted as an answer' ) );
	}
	
	public static function getErrorMessages() {
		if( !self::$errorMessages ) {
			self::initMessages();
		}
		return self::$errorMessages;
	}
	
	public function setMode($mode = 1) {
		$this->mode = $mode;
	}
	
	public function setForm(Form $form) {
		$this->form = $form;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $identifier That is necessary to retrieve after
	 * @param string $description Should be translated !
	 * @param mixed $value
	 * @param "input"|"password"|"hidden"|"text"|"checkbox"|"radio"|"file" $type
	 * @param array of mandatory|optional|not-default|value_greater-equal-than_x|count_less-than_y $constraints
	 * warning not-default add little computation time because it's transformed into value_not-equal_x where x is the default value
	 */
	protected function __construct($identifier, $description, $value, $type, array $constraints = array()) {
		if( !static::isAcceptType( $type ) ) {
			throw new \InvalidArgumentException( "The type $type is not compatible with " . get_class( $this ) );
		}
		$index = array_search( 'not-default', $constraints );
		if( false !== $index ) {
			$constraints[$index] = 'value_not-equal_' . $value;
		}
		$this->identifier = $identifier;
		$this->description = $description;
		$this->value = $value;
		$this->default = $value;
		$this->type = $type;
		$this->constraints = $constraints;
		$this->error = null;
		if( !self::$errorMessages ) {
			self::initMessages();
		}
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array() );
	}
	
	public function getName() {
		return $this->identifier;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function setError($error) {
		$this->error = $error;
	}
	
	public function containsError() {
		$value = $this->getValue();
		foreach( $this->constraints as $c ) {
			$args = explode( '_', $c );
			$base = array_shift( $args );
			//			list( $base, $args ) = explode( '_', $c, 2 );
			//			$args = explode( '_', $args );
			switch ( $base ) {
				case 'mandatory' :
					if( !$value ) {
						return __( 'This field cannot be empty' );
					}
					break;
				case 'optional' :
					break;
				case 'strlen' :
					switch ( $args[0] ) {
						case 'less-than' :
							if( $value && !(strlen( $value ) < $args[1]) ) {
								return __( self::$errorMessages['strlen_lessthan'], array( '%value%' => $args[1] ) );
							}
							break;
						case 'less-equal' :
							if( $value && !(strlen( $value ) <= $args[1]) ) {
								return __( self::$errorMessages['strlen_lessequal'], array( '%value%' => $args[1] ), false );
							}
							break;
						case 'at-least' :
							if( $value && !(strlen( $value ) >= $args[1]) ) {
								return __( self::$errorMessages['strlen_atleast'], array( '%value%' => $args[1] ) );
							}
							break;
					}
					break;
				
				case 'value' :
					switch ( $args[0] ) {
						case 'not-equal' :
							if( $value && ($value == $args[1]) ) {
								return __( self::$errorMessages['value_notequal'], array( '%value%' => $args[1] ) );
							}
							break;
						case 'less-than' :
							if( $value && !($value < $args[1]) ) {
								return __( self::$errorMessages['value_lessthan'], array( '%value%' => $args[1] ) );
							}
							break;
						case 'greater-equal-than' :
							if( $value && !($value >= $args[1]) ) {
								return __( self::$errorMessages['value_greaterequalthan'], array( '%value%' => $args[1] ) );
							}
							break;
						case 'greater-than' :
							if( $value && !($value > $args[1]) ) {
								return __( self::$errorMessages['value_greaterthan'], array( '%value%' => $args[1] ) );
							}
							break;
					}
					break;
			
		// default other word that are not constraints like "raw" to avoid clean text, or labelInInput by example
			}
		}
		return false;
	}
	
	/** 
	 * In order to know if we need to change the accept content of the form
	 * @return true if the field can accept a file
	 */
	public function isFileAccepter() {
		return false;
	}
	
	/**
	 * This is also use to inform the class attribute of the field
	 * @return array of string representing the constraints
	 */
	public function getConstraintsAsString() {
		return implode( $this->constraints, " " );
	}
	
	public function displayAll() {
		$this->insertLabel();
		$this->insertField();
		$this->insertErrorMessage();
	}
	
	public function insertLabel() {
		?><label for="<?php
		echo $this->identifier;
		?>"><?php
		echo $this->description;
		?></label><?php
	}
	
	public function insertErrorMessage() {
		?><span
	class="error_message<?php
		if( $this->error )
			echo ' active'?>"><?php
		echo $this->error;
		?></span><?php
	}
	
	public function insertField() {
		?>
<input type="<?php
		echo $this->type;
		?>"
	id="<?php
		echo $this->identifier;
		?>"
	name="<?php
		echo $this->identifier;
		?>"
	class="<?php
		echo $this->getConstraintsAsString();
		?>"
	title="<?php
		echo $this->description;
		?>"
	value="<?php
		echo $this->getValue();
		?>" />
<?php
	}

}