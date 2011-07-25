<?php

namespace socialportal\common\templates;

use core\FrontController;

class NoFilterLinkTemplate implements iInsertable{
	/** @var FrontController */
	protected $frontController;
	/** @var string */
	protected $link;
	
	public function __construct(FrontController $frontController, $link){
		$this->frontController = $frontController;
		$this->link = $link;
	}
	
	public function insert(){
		$this->frontController->getViewHelper()->addCssFile( 'no_filter_link.css' );
		$title = __('Display all replies');
		?>
		<div id="no-filter-link">
			<span>
				<?php echo __( 'Only the best replies are displayed... ' );?>
			</span>
			<a href="<?php echo $this->link; ?>" title="<?php echo $title; ?>">
				<?php echo $title; ?>
			</a>
		</div>
	<?php 
	}
}