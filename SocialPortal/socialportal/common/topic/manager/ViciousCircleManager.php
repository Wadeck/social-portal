<?php

namespace socialportal\common\topic\manager;

use socialportal\common\form\posts\PostViciousCircleForm;

use socialportal\common\form\topics\TopicViciousCircleForm;

use socialportal\common\templates\posts\ViciousCirclePostTemplate;

use socialportal\common\templates\topics\ViciousCircleTopicTemplate;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\topic\AbstractTypeManager;

class ViciousCircleManager extends AbstractTypeManager {
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate() {
		return new ViciousCircleTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate() {
		return new ViciousCirclePostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName() {
		return 'socialportal\model\TopicViciousCircle';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName() {
		return 'socialportal\model\PostViciousCircle';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName() {
		return __( 'Vicious Circle' );
	}
	
	/** @return int the type id */
	public function getTypeId() {
		return TypeCenter::$viciousCircleType;
	}
	
	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController) {
		return new TopicViciousCircleForm( $frontController );
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController) {
		return new PostViciousCircleForm( $frontController );
	}
}