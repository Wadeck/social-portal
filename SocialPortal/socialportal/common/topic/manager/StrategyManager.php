<?php

namespace socialportal\common\topic\manager;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\form\custom\PostStrategyForm;

use socialportal\common\form\custom\TopicStrategyForm;

use socialportal\common\templates\StrategyPostTemplate;

use socialportal\common\templates\StrategyTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class StrategyManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate(){
		return new StrategyTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate(){
		return new StrategyPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicStrategy';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostStrategy';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Strategy');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return 4;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicStrategyForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostStrategyForm($frontController);
	}
}