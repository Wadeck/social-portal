<?php

namespace core\http\exceptions;

class CustomException {
	/** @var int The code of the exception wants to display [403, 404, 500] */
	protected $code;
	/** @var string The message that will be displayed to everyone [Warning] do not give too much information to the user */
	protected $message;
	
	/**
	 * @param int $code
	 * @param string $message
	 */
	public function __construct($code = null, $message = null) {
		$this->code = $code ? $code : 404;
		$this->message = $message;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function getUserMessage() {
		return $this->message;
	}
	
	public function log() {}
	
	public function __toString() {
		return "CE[{$this->getCode()}:" . get_called_class() . "]";
	}
}