<?php

namespace socialportal\common\templates;

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
	protected $getAttributes;
	/** @var string not encoded, just the action name */
	protected $nonceAction;
	
	/**
	 * @param FrontController $frontController
	 * @param string $message Message to display in the div, already translated
	 */
	public function __construct(FrontController $frontController, $module, $action, $getAttributes, $nonceAction = false) {
		$this->frontController = $frontController;
		$this->module = $module;
		$this->action = $action;
		$this->getAttributes = $getAttributes;
		$this->nonceAction = $nonceAction;
	}
	
	public function insert() {
		$this->frontController->getViewHelper()->insertModule( $this->module, $this->action, $this->getAttributes, $this->nonceAction );
	}

}