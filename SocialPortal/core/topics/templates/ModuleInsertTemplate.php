<?php

namespace core\topics\templates;

use core\FrontController;

/**
 *	Simple object implementing iInsertable
 */
class ModuleInsertTemplate implements iInsertable {
	/** @var FrontController */
	protected $frontController;
	/** @var string */
	protected $module;
	/** @var string */
	protected $action;
	/** @var string */
	protected $parameters;
	/** @var string */
	protected $nonce;
	
	/**
	 * @param FrontController $frontController
	 * @param string $message Message to display in the div, already translated
	 */
	public function __construct(FrontController $frontController, $module, $action, $parameters, $nonce=false) {
		$this->frontController = $frontController;
		$this->module = $module;
		$this->action = $action;
		$this->parameters = $parameters;
		$this->nonce = $nonce;
	}
	
	public function insert() {
		$this->frontController->getViewHelper()->insertModule($this->module, $this->action, $this->parameters, $this->nonce);
	}

}