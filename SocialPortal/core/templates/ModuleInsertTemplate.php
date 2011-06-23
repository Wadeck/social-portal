<?php

namespace core\templates;

use core\FrontController;

/**
 * Simple object implementing iInsertable
 */
class ModuleInsertTemplate implements iInsertable {
	/** @var FrontController */
	protected $frontController;
	/** @var string */
	protected $module;
	/** @var string */
	protected $action;
	/** @var array */
	protected $parameters;
	/** @var array */
	protected $getAttributes;
	/** @var string */
	protected $nonce;
	
	/**
	 * @param FrontController $frontController
	 * @param string $message Message to display in the div, already translated
	 */
	public function __construct(FrontController $frontController, $module, $action, $parameters, $getAttributes, $nonce = false) {
		$this->frontController = $frontController;
		$this->module = $module;
		$this->action = $action;
		$this->parameters = $parameters;
		$this->getAttributes = $getAttributes;
		$this->nonce = $nonce;
	}
	
	public function insert() {
		$this->frontController->getViewHelper()->insertModule( $this->module, $this->action, $this->parameters, $this->getAttributes, $this->nonce );
	}

}