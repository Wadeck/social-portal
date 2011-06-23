<?php

namespace core\http\exceptions;

/**
 * Wrap php exception into generic CustomException to be used in generateException in the FrontController
 * @see CustomException
 * @see FrontController
 */
class UnexpectedException extends CustomException {
	public function __construct(\Exception $e) {
		parent::__construct( 500, __( 'A unexpected exception occurred: %exception_message%', array( '%exception_message%' => $e->getMessage() ) ) );
	}
}