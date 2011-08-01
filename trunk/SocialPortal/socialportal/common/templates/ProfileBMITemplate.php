<?php

namespace socialportal\common\templates;

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
	private $front;
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
	
	public function __construct(FrontController $front, User $user, UserProfile $profile = null) {
		$this->front = $front;
		$this->user = $user;
		$this->profile = $profile;
		$this->currentUser = $this->front->getCurrentUser();
		$this->isModo = $this->front->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role);
		$this->isSelf = ($this->user->getId() === $this->currentUser->getId());
		$this->userHelper = new UserHelper($this->front);
		$this->userHelper->setCurrentUser($this->user);
		$this->userId = $user->getId();
	}
	
	public function insert() {
		$this->front->getViewHelper()->addCssFile('profile_chart_bmi.css');
		$bmiRepo = $this->front->getEntityManager()->getRepository( 'ChartBmi' );
		if( $bmiRepo->hasBmiInfo( $this->userId ) ){
			$this->front->getViewHelper()->addJavascriptFile('chart_functions.js');
			$this->insertChart();
		}else{
			$this->insertNoChart();
		}
		$this->insertAddButton();
	}
	
	private function insertChart(){
		$width = 450;
		$height = 350;
		$url = $this->front->getViewHelper()->createHrefWithNonce( 'getValues', 'Bmi', 'getValues', array( 'userId' => $this->userId ) );
		$baseSwf = Utils::getBaseUrl() . Config::getOrDie( 'swf_dir' );
		$baseJs = Utils::getBaseUrl() . Config::getOrDie( 'js_dir' );
		echo OpenFlashChart::getObject($width, $height, $url, $baseSwf, $baseJs);
	}
	
	private function insertNoChart(){
		$this->front->getViewHelper()->addCssFile( 'messages.css' );
		?>
		<div class="message info clear_both">
			<?php echo __( 'No chart for the moment' ); ?>
		</div>
	<?php
	}
	
	private function insertAddButton(){
		?>
	<?php
	}
}