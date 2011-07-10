<?php

namespace socialportal\common\topic\manager;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use core\form\custom\PostStrategyForm;

use core\form\custom\TopicStrategyForm;

use core\templates\StrategyPostTemplate;

use core\templates\StrategyTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class StrategyManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function getTopicTemplate(FrontController $frontController, EntityManager $em, $topic, $permalinkTopic){
		$template = new StrategyTopicTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setTopic($topic);
		return $template;
	}
	
	/** @return AbstractPostTemplate */
	public function getPostTemplate(FrontController $frontController, EntityManager $em, array $posts, $permalink){
		$template = new StrategyPostTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setPosts($posts);
		return $template;
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