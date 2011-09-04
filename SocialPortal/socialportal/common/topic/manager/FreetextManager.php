<?php

namespace socialportal\common\topic\manager;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\form\posts\PostFreetextForm;

use socialportal\common\form\topics\TopicFreetextForm;

use socialportal\common\templates\posts\FreetextPostTemplate;

use socialportal\common\templates\topics\FreetextTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class FreetextManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate(){
		return new FreetextTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate(){
		return new FreetextPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicFreetext';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostFreetext';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Freetext');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return TypeCenter::$freetextType;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicFreetextForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostFreetextForm($frontController);
	}
}