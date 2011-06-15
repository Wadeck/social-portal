<?php

namespace socialportal\controller;
use core;

use core\AbstractController;

class Admin extends AbstractController {
	/**
	 * Would be inserted in the header, to display the different admin links available
	 * TODO add secured annotation with admin capability
	 */
	public function indexAction($parameters) {
		$links = array();
		$links['Tool'] = $this->frontController->getViewHelper()->createHref( 'tool', 'index' );
		$this->frontController->getResponse()->setVar( 'links', $links );
		$this->frontController->doDisplay( 'admin', 'adminPanel' );
	}
}