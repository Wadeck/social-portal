<?php

namespace core\templates;

use core\tools\Utils;

use core\user\UserRoles;

use core\security\TopicToolSecurity;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use core\user\UserHelper;

/**
 * Display only the instruction at the top of a topic form
 */
class InstructionTemplate implements iInsertable {
	/** @var FrontController */
	protected $frontController;
	/** @var boolean */
	protected $isVisible;
	/** @var string translated text */
	protected $instructionContent;
	/** @var string formatted date */
	protected $instructionDate;
	/** @var the cookie name */
	protected $cookieName;
	
	private static $idButtonShow = 'instruction-show';
	private static $idBox = 'instruction-box';
	
	public function __construct(FrontController $frontController, $instruction, $visible, $cookieName){
		$this->frontController = $frontController;
		$this->isVisible = $visible;
		$this->instructionContent = $instruction->getInstructions();
		// DateTime
		$date = $instruction->getLastModification();
		if( null !== $date){
			$date->setTimezone($this->frontController->getDateTimeZone());
			$this->instructionDate = __('Last modification: ') . $date->format('j M Y');
		}else{
			$this->instructionDate = null;
		}
		$this->cookieName = $cookieName;
		
	}
	public function insertButton(){
		$displayHide = __( 'Hide instructions' );
		$displayShow= __( 'Show instructions' );
		if( $this->isVisible ){
			$styleShow = ' style="display: none"';
			$styleHide = '';
		}else{
			$styleShow = '';
			$styleHide = ' style="display: none"';
		}
		$this->frontController->getViewHelper()->addCssFile( 'instruction.css' );
		$this->frontController->getViewHelper()->addJavascriptFile( 'jquery.js' );
		$this->frontController->getViewHelper()->addJavascriptFile( 'jcookie.js' );
		$this->frontController->getViewHelper()->addJavascriptFile( 'instruction_box.js' );
		?>
		<div id="instruction-display">
			<a id="<?php echo self::$idButtonShow; ?>"<?php echo $styleHide; ?> title="<?php echo $displayHide; ?>" href="#" onClick="displayInstruction(this, '<?php echo self::$idBox; ?>', false, '<?php echo $this->cookieName; ?>')">
				<?php echo $displayHide; ?>
			</a>
			<a<?php echo $styleShow; ?> title="<?php echo $displayShow; ?>" href="#" onClick="displayInstruction(this, '<?php echo self::$idBox; ?>', true, '<?php echo $this->cookieName; ?>')">
				<?php echo $displayShow; ?>
			</a>
		</div>
		<?php
	}
	
	public function insert(){
		if( $this->isVisible ){
			$styles = '';
		}else{
			$styles = ' style="display: none;"';
		}
		$this->frontController->getViewHelper()->addCssFile( 'instruction.css' );
		?>
		<div onClick="simulateClick('<?php echo self::$idButtonShow; ?>')" id="<?php echo self::$idBox; ?>"<?php echo $styles; ?>>
			
			<div id="instruction-main">
				<div id="instruction-content">
					<?php echo $this->instructionContent; ?>
				</div>
					<div id="instruction-meta">
						<?php if( null !== $this->instructionDate ): ?>
							<span id="instruction-last-modified">
								<?php echo $this->instructionDate; ?>
							</span>
						<?php endif; ?>
						<span id="instruction-instruction">
							<?php echo __('Click on this box to hide it'); ?>
						</span>
					</div>
			</div>
		</div>
		
		<?php
	}
}