<?php

namespace socialportal\controller;
use socialportal\repository\ForumMetaRepository;

use socialportal\model\ForumMeta;

use socialportal\repository\TopicBaseRepository;

use socialportal\repository\ForumRepository;

use socialportal\common\templates\HomeBlockTemplate;

use core;

use core\AbstractController;

class Home extends AbstractController {
	// @Secured({"full","limited"})
	/**
	 */
	public function indexAction() {
		//TODO here is the point to manage if we don't find any database
		$forumRepo = $this->em->getRepository('Forum');
		$forums = $forumRepo->findAll();
		if(!$forums){
			$this->frontController->addMessage('There is not forum for the moment, please use the create forum tool', 'info');
			$this->frontController->doRedirect('tool');
		}
		
		$forumMetaRepo = $this->em->getRepository('ForumMeta');
		$topicRepo = $this->em->getRepository('TopicBase');
		$forum = $forums[0];
		//
		//TODO for the moment by date order
		$forumId = $forum->getId();
		$topics = $topicRepo->findTopicsFromForum($forumId, 1, 5);
		$typeId = $forumMetaRepo->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		$name = __('Last Discussions');
		
		$formattedLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic', array('topicId' => '%topicId%', 'forumId' => '%forumId%'));
		$linkDisplayForum = $this->frontController->getViewHelper()->createHref('Forum', 'displaySingleForum', array( 'forumId' => $forumId));
		
		$freetextTemplate = new HomeBlockTemplate($this->frontController, $forum, $topics, $typeId, $name, $formattedLink, $linkDisplayForum);
		//
		//
		$forum = $forums[1];
		//
		//TODO for the moment by date order
		$forumId = $forum->getId();
		$topics = $topicRepo->findTopicsFromForum($forumId, 1, 5);
		$typeId = $forumMetaRepo->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		$name = __('Last Stories');
		
		$formattedLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic', array('topicId' => '%topicId%', 'forumId' => '%forumId%'));
		$linkDisplayForum = $this->frontController->getViewHelper()->createHref('Forum', 'displaySingleForum', array( 'forumId' => $forumId));
		
		$strategyTemplate = new HomeBlockTemplate($this->frontController, $forum, $topics, $typeId, $name, $formattedLink, $linkDisplayForum);
		//
		//
		$forum = $forums[2];
		//
		//TODO for the moment by date order
		$forumId = $forum->getId();
		$topics = $topicRepo->findTopicsFromForum($forumId, 1, 5);
		$typeId = $forumMetaRepo->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		$name = __('Last Strategies');
		
		$formattedLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic', array('topicId' => '%topicId%', 'forumId' => '%forumId%'));
		$linkDisplayForum = $this->frontController->getViewHelper()->createHref('Forum', 'displaySingleForum', array( 'forumId' => $forumId));
		
		$activityTemplate = new HomeBlockTemplate($this->frontController, $forum, $topics, $typeId, $name, $formattedLink, $linkDisplayForum);
		//
		//
		$forum = $forums[3];
		//
		//TODO for the moment by date order
		$forumId = $forum->getId();
		$topics = $topicRepo->findTopicsFromForum($forumId, 1, 5);
		$typeId = $forumMetaRepo->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		$name = __('Last Activities');
		
		$formattedLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic', array('topicId' => '%topicId%', 'forumId' => '%forumId%'));
		$linkDisplayForum = $this->frontController->getViewHelper()->createHref('Forum', 'displaySingleForum', array( 'forumId' => $forumId));
		
		$storyTemplate = new HomeBlockTemplate($this->frontController, $forum, $topics, $typeId, $name, $formattedLink, $linkDisplayForum);
		
		$templates = array($freetextTemplate, $strategyTemplate, $activityTemplate, $storyTemplate);
		$this->frontController->getResponse()->setVar('blocks', $templates);
		$this->frontController->doDisplay( 'home' );
	}
	
	public function index2Action(){
		$this->frontController->doDisplay( 'home2' );
	}
}