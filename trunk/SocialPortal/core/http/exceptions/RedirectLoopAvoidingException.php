<?php

namespace core\http\exceptions;

use core\debug\Logger;

class RedirectLoopAvoidingException extends CustomException{
	public function __construct($url){
		parent::__construct(404, __(
			'The url (%url%) redirection is actually a loop',
			array(
				'%url%' => $url,
			)
		));
	}
}