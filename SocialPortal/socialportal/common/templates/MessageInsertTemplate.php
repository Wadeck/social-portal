<?php

namespace socialportal\common\templates;

use core\FrontController;

/**
 * Simple object implementing iInsertable
 */
class MessageInsertTemplate implements iInsertable {
	/** @var FrontController */
	protected $frontController;
	/** @var string */
	protected $message;
	/** @var array */
	protected $ids;
	/** @var array */
	protected $classes;
	
	
	/**
	 * @param FrontController $frontController
	 * @param string $message Message to display in the div, already translated
	 */
	public function __construct(FrontController $frontController, $message, array $ids = array(), array $classes = array()) {
		$this->frontController = $frontController;
		$this->message = $message;
		$this->ids = $ids;
		$this->classes = $classes;
	}
	
	public function insert() {
		$this->frontController->getViewHelper()->addCssFile( 'messages.css' );
		if($this->ids){
			$ids = 'id="' . implode(' ', $this->ids) . '" ';
		}else{
			$ids='';
		}
		if($this->classes){
			$classes = 'class="' . implode(' ', $this->classes) . '" ';
		}else{
			$classes='';
		}
		?>
		<div <?php echo $ids.$classes?>>
			<div class="message info centered">
				<?php echo $this->message; ?>
			</div>
		</div>
<?php
	}

}