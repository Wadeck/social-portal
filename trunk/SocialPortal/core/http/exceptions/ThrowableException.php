<?php

namespace core\http\exceptions;

/**
 * Wrap custom exception to enable them to be thrown
 */
class ThrowableException extends \Exception {
	/** @var CustomException */
	private $customException;
	
	public function __construct(CustomException $customException) {
		parent::__construct( $customException->getUserMessage(), $customException->getCode(), null );
		$this->customException = $customException;
	}
	
	/** @return CustomException */
	public function getCustomException() {
		return $this->customException;
	}
	
	public function __toString() {
		return "ThrowableException::[{$this->getCustomException()}]";
	}
}