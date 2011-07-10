<?php

namespace socialportal\common\topic\manager;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use core\form\custom\PostFreetextForm;

use core\form\custom\TopicFreetextForm;

use core\templates\FreetextPostTemplate;

use core\templates\FreetextTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class FreetextManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function getTopicTemplate(FrontController $frontController, EntityManager $em, $topic, $permalinkTopic){
		$template = new FreetextTopicTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setTopic($topic);
		return $template;
	}
	
	/** @return AbstractPostTemplate */
	public function getPostTemplate(FrontController $frontController, EntityManager $em, array $posts, $permalink){
		$template = new FreetextPostTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setPosts($posts);
		return $template;
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
		return 2;
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