<?php

namespace socialportal\common\topic\manager;

use socialportal\common\form\posts\PostProblemForm;

use socialportal\common\form\topics\TopicProblemForm;

use socialportal\common\templates\posts\ProblemPostTemplate;

use socialportal\common\templates\topics\ProblemTopicTemplate;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\topic\AbstractTypeManager;

class ProblemManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate(){
		return new ProblemTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate(){
		return new ProblemPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicProblem';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostProblem';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Problem');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return TypeCenter::$problemType;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicProblemForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostProblemForm($frontController);
	}
}