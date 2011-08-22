<?php

namespace socialportal\controller;

use core\Config;

use core\http\GetSettable;

use core\user\UserManager;

use core\user\UserHelper;

use socialportal\model\TopicVoteStats;

use socialportal\model\ReportPost;

use core\FrontController;

use core\AbstractController;

use socialportal\model\ReportTopic ;
use socialportal\common\templates\Paginator;




class ReportAbuse extends AbstractController {

	/**
	 * @Nonce(reportTopic)
	 * @GetAttributes({topicId, forumId})
	 * @RoleAtLeast(fullUser)
	 */
	public function reportTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');		
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ){
			$userId = UserManager::$anonUserId;
		}
		$topicRep=$this->em->getRepository('TopicBase');		
		if ($topicRep->findBaseTopic($topicId) && !$topicRep->findBaseTopic($topicId)->getIsDeleted()){
			$report = new ReportTopic();
			$report->setTopicId($topicId);
			$report->setUserId($userId);
			$report->setDate($now);
			$report->setIsViewed(false);
			$report->setIsTreated(false);
			$report->setIsDeleted(false);
			
			$this->em->persist($report);
			if( !$this->em->flushSafe($report) ){
				$this->frontController->addMessage(__('There was a problem during the report process'), 'error');
			}
			$this->frontController->addMessage(__('Your report was taking into account, thank you'), 'correct');
			$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
		
		}else{
			$this->frontController->addMessage(__('This topic doesn\'t exist anymore '), 'error');
		}
	}
		/**
	 * @Nonce(reportPost)
	 * @GetAttributes({postId, topicId, forumId})
	 * @RoleAtLeast(fullUser)
	 */
	public function reportPostAction() {
		$get = $this->frontController->getRequest()->query;
		$postId = $get->get('postId');
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ){
			$userId = UserManager::$anonUserId;
		}
		$postRep=$this->em->getRepository('PostBase');		
		if ($postRep->findBasePost($postId) && !$postRep->findBasePost($postId)->getIsDeleted()){
			$report = new ReportPost();
			$report->setPostId($postId);
			$report->setUserId($userId);
			$report->setDate($now);
			$report->setIsViewed(false);
			$report->setIsTreated(false);
			$report->setIsDeleted(false);
			
			$this->em->persist($report);
			if( !$this->em->flushSafe($report) ){
				$this->frontController->addMessage(__('There was a problem during the report process'), 'error');
			}
			$this->frontController->addMessage(__('Your report was taking into account, thank you'), 'correct');
			$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
		
		}else{
			$this->frontController->addMessage(__('This topic doesn\'t exist anymore '), 'error');
		}
	}
/**
	 * @Nonce(removeReportTopic)
	 * @GetAttributes({reportId})
	 * @RoleAtLeast(fullUser)
	 */
	public function removeReportTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$reportId = $get->get('reportId');		
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ){
			$userId = UserManager::$anonUserId;
		}
		$topicRep=$this->em->getRepository('ReportTopic');		
		if ($topicRep->findBy(array("id"=>$reportId))){
			$report = $topicRep->findBy(array("id"=>$reportId));		
			$report=$report[0];
			$topicId=$report->getTopicId();
			$report->setIsDeleted(true);			
			$this->em->persist($report);
			if( !$this->em->flushSafe($report) ){
				$this->frontController->addMessage(__('There was a problem during the report process'), 'error');
			}
			$this->frontController->addMessage(__('Your report was removed'), 'correct');
			
			$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
		
		}else{
			$this->frontController->addMessage(__('This topic doesn\'t exist anymore '), 'error');
		}
	}
		/**
	 * @Nonce(removeReportPost)
	 * @GetAttributes({reportId,topicId})
	 * @RoleAtLeast(fullUser)
	 */
	public function removeReportPostAction() {
		$get = $this->frontController->getRequest()->query;
		$reportId = $get->get('reportId');
		$topicId = $get->get('topicId');
				
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ){
			$userId = UserManager::$anonUserId;
		}
		$topicRep=$this->em->getRepository('ReportPost');		
		if ($topicRep->findBy(array("id"=>$reportId))){
			$report = $topicRep->findBy(array("id"=>$reportId));
			$report=$report[0];
			$report->setIsDeleted(true);			
			$this->em->persist($report);
			if( !$this->em->flushSafe($report) ){
				$this->frontController->addMessage(__('There was a problem during the report process'), 'error');
			}
			$this->frontController->addMessage(__('Your report was removed'), 'correct');
			$this->frontController->doRedirect('Topic', 'displaySingleTopic', array('topicId' => $topicId, 'forumId' => $forumId));
		
		}else{
			$this->frontController->addMessage(__('This topic doesn\'t exist anymore '), 'error');
		}
	}

	public function displayManageReportFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId=$get->get('userId');
		$forumId=1;
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 20 );
		$timeTarget = $get->get( 'timeTarget', false );
		$lastPage = $get->get( 'lastPage', false );		
		
		$topicBaseRepo = $this->em->getRepository( 'TopicBase' );		
		$max_pages=10;
		$max_pages = ceil( $max_pages / $num_per_page );
		if( !$max_pages ) {
			$max_pages = 0;
		}
		$getArgs = array( 'forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%" );
		
		$link = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingleForum', $getArgs );		
		$topics = $topicBaseRepo->findReportedTopic();		
		$response = $this->frontController->getResponse();				
		$pagination = new Paginator();
		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ) );		
		$userHelper = new UserHelper( $this->frontController );		
		$response->setVar( 'pagination', $pagination );
		$response->setVar( 'topics', $topics );
		$response->setVar( 'userHelper', $userHelper );
		$response->setVar( 'userId', $userId );				

		$this->frontController->doDisplay( 'report', 'displayManageReportForm' );
	}
	
}