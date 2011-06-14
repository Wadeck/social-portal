<?php

namespace socialportal\controller;
use core\security\Crypto;

use core\FrontController;

use core\AbstractController;

/**
 * Simple controller to make some administrative task like create password etc
 *
 */
class Tool extends AbstractController {
	public function directCreatePasswordAction($parameters) {
		if( count( $parameters ) >= 2 ) {
			$randomkey = $parameters[0];
			$password = $parameters[1];
			$encoded = Crypto::encodeDBPassword( $randomkey, $password );
		} else {
			$encoded = null;
		}
		$this->frontController->getResponse()->setVar( 'encoded', $encoded );
		$this->frontController->doDisplay( 'tool', 'displayPassword' );
	}
}
