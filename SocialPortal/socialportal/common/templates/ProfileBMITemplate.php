<?php

namespace socialportal\common\templates;

use socialportal\common\form\custom\AddBmiValueForm;

use core\Config;

use OFC\OpenFlashChart;

use core\tools\Utils;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\model\UserProfile;

use socialportal\model\User;

use core\FrontController;

class ProfileBMITemplate implements iInsertable {
	/** @var FrontController */
	private $frontController;
	/** @var User */
	private $user;
	/** @var UserProfile */
	private $profile;
	/** @var User */
	private $currentUser;
	/** @var boolean */
	private $isModo;
	/** @var boolean */
	private $isSelf;
	/** @var UserHelper */
	private $userHelper;
	/** @var int */
	private $userId;
	
	public function __construct(FrontController $frontController, User $user, UserProfile $profile = null) {
		$this->frontController = $frontController;
		$this->user = $user;
		$this->profile = $profile;
		$this->currentUser = $this->frontController->getCurrentUser();
		$this->isModo = $this->frontController->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role);
		$this->isSelf = ($this->user->getId() === $this->currentUser->getId());
		$this->userHelper = new UserHelper($this->frontController);
		$this->userHelper->setCurrentUser($this->user);
		$this->userId = $user->getId();
	}
	
	public function insert() {
		$this->frontController->getViewHelper()->addCssFile('profile_chart_bmi.css');
		$bmiRepo = $this->frontController->getEntityManager()->getRepository( 'ChartBmi' );
		if( $bmiRepo->hasEnoughBmiInfo( $this->userId ) ){
			$this->frontController->getViewHelper()->addJavascriptFile('chart_functions.js');
			$this->insertChart();
		}else{
			$this->insertNoChart();
		}
		$this->insertAddButton();
	}
	
	private function insertChart(){
		$width = 450;
		$height = 350;
		$url = $this->frontController->getViewHelper()->createHrefWithNonce( 'getValues', 'Bmi', 'getValues', array( 'userId' => $this->userId ) );
		$baseSwf = Utils::getBaseUrl() . Config::getOrDie( 'swf_dir' );
		$baseJs = Utils::getBaseUrl() . Config::getOrDie( 'js_dir' );
		?>
		<span class="remove-info"><?php echo __('Click on an element to remove it');?></span>
		<?php
		echo OpenFlashChart::getObject($width, $height, $url, $baseSwf, $baseJs);
	}
	
	private function insertNoChart(){
		$this->frontController->getViewHelper()->addCssFile( 'messages.css' );
		?>
		<div class="message info clear_both">
			<?php echo __( 'Not enough data for the moment,<br>at least two entries are necessary' ); ?>
		</div>
	<?php
	}
	
	private function insertAddButton(){
		$form = new AddBmiValueForm( $this->frontController );
		$form->setNonceAction( 'addValue' );
		$form->setupWithArray();
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Bmi', 'addValue', array('userId' => $this->userId) );
		$form->setTargetUrl( $actionUrl );

		?>
		<h3 class="form-title">
			<?php echo __('New value'); ?>
		</h3>
	<?php
		$form->insert();
	}
}