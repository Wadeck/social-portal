<?php

namespace socialportal\common\topic\manager;

use socialportal\common\form\topics\TopicStoryForm;

use socialportal\common\form\posts\PostStoryForm;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

use socialportal\common\templates\posts\StoryPostTemplate;

use socialportal\common\templates\topics\StoryTopicTemplate;

use socialportal\common\topic\AbstractTypeManager;

class AutomaticThoughtsManager extends AbstractTypeManager {
	/** @return AbstractTopicTemplate */
	public function _getTopicTemplate() {
		return new StoryTopicTemplate();
	}
	
	/** @return AbstractPostTemplate */
	public function _getPostTemplate() {
		return new StoryPostTemplate();
	}
	
	/** @return the name with namespace of the topic model related to that type */
	public function getTopicClassName() {
		return 'socialportal\model\TopicStory';
	}
	
	/** @return the name with namespace of the post model related to that type */
	public function getPostClassName() {
		return 'socialportal\model\PostStory';
	}
	
	/** @return the name of the topic model related to that type */
	public function getSimpleName() {
		return __( 'Automatic Thoughts' );
	}
	
	/** @return int the type id */
	public function getTypeId() {
		return TypeCenter::$automaticThoughtsType;
	}
	
	/** @return iTopicForm */
	public function getTopicForm(FrontController $frontController) {
		return new TopicStoryForm( $frontController );
	}
	
	/** @return iPostForm */
	public function getPostForm(FrontController $frontController) {
		return new PostStoryForm( $frontController );
	}
}