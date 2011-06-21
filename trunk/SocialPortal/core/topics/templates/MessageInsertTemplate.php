<?php

namespace core\topics\templates;

use core\FrontController;

/**
 *	Simple object implementing iInsertable
 */
class MessageInsertTemplate implements iInsertable {
	/** @var FrontController */
	protected $frontController;
	/** @var string */
	protected $message;
	
	/**
	 * @param FrontController $frontController
	 * @param string $message Message to display in the div, already translated
	 */
	public function __construct(FrontController $frontController, $message) {
		$this->frontController = $frontController;
		$this->message = $message;
	}
	
	public function insert() {
		$this->frontController->getViewHelper()->addCssFile('messages.css');
		?>
		<div class="rounded-box pagged">
			<div class="message info centered"><?php echo $this->message; ?></div>
		</div>
	<?php
	}

}