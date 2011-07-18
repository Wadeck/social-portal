<?php

namespace socialportal\controller;
use socialportal\repository\SubsetTopicRepository;

use core\user\UserRoles;

use socialportal\repository\UserRepository;

use socialportal\repository\ForumMetaRepository;

use socialportal\model\ForumMeta;

use socialportal\repository\TopicBaseRepository;

use socialportal\repository\ForumRepository;

use socialportal\common\templates\HomeBlockTemplate;

use core;

use core\AbstractController;

class Home extends AbstractController {
	/**
	 */
	public function indexAction() {
		//TODO here is the point to manage if we don't find any database
		$forumRepo = $this->em->getRepository('Forum');
		$forums = $forumRepo->findAll();
		if( !$forums ){
			$this->frontController->getRequest()->getSession()->setFlash('no_database', true);
			$this->frontController->addMessage('There is not forum for the moment, please use the create forum tool', 'info');
			$this->frontController->doRedirect('Tool');
		}
		
		$forumMetaRepo = $this->em->getRepository('ForumMeta');
		$topicRepo = $this->em->getRepository('TopicBase');
		
		if( $this->frontController->getViewHelper()->currentUserIsAtLeast(UserRoles::$full_user_role) ){
			$order = 'date';
		}else{
			$order = 'vote';
		}
		
		$freetextTemplate = $this->createBlock($topicRepo, $forumMetaRepo, $forums[0], __('Last Discussions'), $order);
		$storyTemplate = $this->createBlock($topicRepo, $forumMetaRepo, $forums[1], __('Last Stories'), $order);
		$strategyTemplate = $this->createBlock($topicRepo, $forumMetaRepo, $forums[2], __('Last Strategies'), $order);
		$activityTemplate = $this->createBlock($topicRepo, $forumMetaRepo, $forums[3], __('Last Activities'), $order);
		
		$templates = array($freetextTemplate, $strategyTemplate, $activityTemplate, $storyTemplate);
		$this->frontController->getResponse()->setVar('blocks', $templates);
		$this->frontController->doDisplay( 'home' );
	}
	
	private function createBlock(TopicBaseRepository $topicRepo, ForumMetaRepository $forumMetaRepo, $forum, $name, $order = 'date'){
		$forumId = $forum->getId();
		switch($order){
			case 'date': default:
				$topics = $topicRepo->findTopicsFromForum($forumId, 1, 5);
				break;
			case 'vote':
				$subsetTopics = $this->em->getRepository('SubsetTopic')->findTopicFromForum($forumId);
				if(false === $subsetTopics){
					$topics = array();
				}else{
					foreach($subsetTopics as $st){
						$topics[] = $topicRepo->findBaseTopic($st->getTopicId());
					}
				}
				
				break;
		}
		$typeId = $forumMetaRepo->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		
		$formattedLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic', array('topicId' => '%topicId%', 'forumId' => '%forumId%'));
		$linkDisplayForum = $this->frontController->getViewHelper()->createHref('Forum', 'displaySingleForum', array( 'forumId' => $forumId));
		
		$template = new HomeBlockTemplate($this->frontController, $forum, $topics, $typeId, $name, $formattedLink, $linkDisplayForum);
		return $template;
	}
}