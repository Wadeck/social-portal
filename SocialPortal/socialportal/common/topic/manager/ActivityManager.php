<?php

namespace socialportal\common\topic\manager;

use socialportal\common\form\topics\TopicActivityForm;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use socialportal\common\form\posts\PostActivityForm;

use core\FrontController;

use socialportal\common\templates\posts\ActivityPostTemplate;

use socialportal\common\templates\topics\ActivityTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class ActivityManager extends AbstractTypeManager {
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate() {
		return new ActivityTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate() {
		return new ActivityPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName() {
		return 'socialportal\model\TopicActivity';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName() {
		return 'socialportal\model\PostActivity';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName() {
		return __( 'Activity' );
	}
	
	/** @return int the type id */
	public function getTypeId() {
		return TypeCenter::$activityType;
	}
	
	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController) {
		return new TopicActivityForm( $frontController );
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController) {
		return new PostActivityForm( $frontController );
	}
}