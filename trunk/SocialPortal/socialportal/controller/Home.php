<?php

namespace socialportal\controller;
use core;

use core\AbstractController;

class Home extends AbstractController {
	// @Secured({"full","limited"})
	/**
	 */
	public function indexAction($parameters) {
		$this->frontController->doDisplay( 'home' );
	}
}