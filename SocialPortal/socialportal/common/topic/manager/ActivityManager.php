<?php

namespace socialportal\common\topic\manager;

use Doctrine\ORM\EntityManager;

use core\form\custom\TopicActivityForm;

use core\FrontController;

use core\form\custom\PostActivityForm;

use core\templates\ActivityPostTemplate;

use core\templates\ActivityTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class ActivityManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function getTopicTemplate(FrontController $frontController, EntityManager $em, $topic, $permalinkTopic){
		$template = new ActivityTopicTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setTopic($topic);
		return $template;
	}
	
	/** @return AbstractPostTemplate */
	public function getPostTemplate(FrontController $frontController, EntityManager $em, array $posts, $permalink){
		$template = new ActivityPostTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setPosts($posts);
		return $template;
	}

	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicActivity';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostActivity';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Activity');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return 1;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicActivityForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostActivityForm($frontController);
	}
}