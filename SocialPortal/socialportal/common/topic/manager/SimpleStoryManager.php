<?php

namespace socialportal\common\topic\manager;

use socialportal\model\PostSimpleStory;

use socialportal\common\topic\TypeCenter;

use socialportal\common\templates\posts\SimpleStoryPostTemplate;

use socialportal\common\templates\topics\SimpleStoryTopicTemplate;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\form\posts\PostSimpleStoryForm;

use socialportal\common\form\topics\TopicSimpleStoryForm;

use socialportal\common\topic\AbstractTypeManager;

class SimpleStoryManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate(){
		return new SimpleStoryTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate(){
		return new SimpleStoryPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicSimpleStory';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostSimpleStory';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Simple Story');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return TypeCenter::$simpleStoryType;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicSimpleStoryForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostSimpleStoryForm($frontController);
	}
}