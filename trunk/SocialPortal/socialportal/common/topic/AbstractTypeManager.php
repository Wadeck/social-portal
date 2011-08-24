<?php

namespace socialportal\common\topic;

use socialportal\model\TopicBase;

use Doctrine\ORM\EntityManager;

use core\FrontController;

abstract class AbstractTypeManager implements TypeManagerInterface{
	/** @return AbstractTopicTemplate */
	public function getTopicTemplate(FrontController $front, EntityManager $em, $topic, $permalink, $highlightTopic){
		$template = $this->_getTopicTemplate();
		$template->setFrontController($front);
		$template->setEntityManager($em);
		$template->setTopic($topic);
		$template->setHighlightTopic($highlightTopic);
		return $template;
	}
	
	protected abstract function _getTopicTemplate();
	
	/** @return AbstractPostTemplate */
	public function getPostTemplate(FrontController $front, EntityManager $em, TopicBase $topicBase, array $posts, $permalink, $highlightPost){
		$template = $this->_getPostTemplate();
		$template->setFrontController($front);
		$template->setEntityManager($em);
		$template->setPosts($posts);
		$template->setTopicBase($topicBase);
		$template->setHighlightPost($highlightPost);
		return $template;
	}
	
	protected abstract function _getPostTemplate();
}