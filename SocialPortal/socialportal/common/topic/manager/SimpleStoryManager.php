<?php

namespace socialportal\common\topic\manager;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use core\form\custom\PostStoryForm;

use core\form\custom\TopicStoryForm;

use core\templates\StoryPostTemplate;

use core\templates\StoryTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class SimpleStoryManager extends AbstractTypeManager{
	/** @return AbstractTopicTemplate */
	public function getTopicTemplate(FrontController $frontController, EntityManager $em, $topic, $permalinkTopic){
		$template = new StoryTopicTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setTopic($topic);
		return $template;
	}
	
	/** @return AbstractPostTemplate */
	public function getPostTemplate(FrontController $frontController, EntityManager $em, array $posts, $permalink){
		$template = new StoryPostTemplate();
		$template->setFrontController($frontController);
		$template->setEntityManager($em);
		$template->setPosts($posts);
		return $template;
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName(){
		return 'socialportal\model\TopicStory';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName(){
		return 'socialportal\model\PostStory';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName(){
		return __('Story');
	}
	
	/** @return int the type id */
	public function getTypeId(){
		return 3;
	}

	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController){
		return new TopicStoryForm($frontController);
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController){
		return new PostStoryForm($frontController);
	}
}