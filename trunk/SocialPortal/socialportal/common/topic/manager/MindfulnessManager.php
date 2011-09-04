<?php

namespace socialportal\common\topic\manager;

use socialportal\common\form\posts\PostMindfulnessForm;

use socialportal\common\form\topics\TopicMindfulnessForm;

use socialportal\common\templates\posts\MindfulnessPostTemplate;

use socialportal\common\templates\topics\MindfulnessTopicTemplate;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\topic\AbstractTypeManager;

class MindfulnessManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate(){
		return new MindfulnessTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate(){
		return new MindfulnessPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicMindfulness';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostMindfulness';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Mindfulness');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return TypeCenter::$mindfulnessType;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicMindfulnessForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostMindfulnessForm($frontController);
	}
}