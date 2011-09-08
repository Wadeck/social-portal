<?php

namespace socialportal\controller;

use core\tools\Utils;

use socialportal\model\ReportTopic;

use socialportal\model\ReportPost;

use socialportal\common\templates\Paginator;

use socialportal\model\ReportItem;

use core\Config;

use core\http\GetSettable;

use core\user\UserManager;

use core\user\UserHelper;

use socialportal\model\TopicVoteStats;

use socialportal\model\VotePost;

use core\FrontController;

use core\AbstractController;
use socialportal\model\VoteTopic;

use stdClass;
class Report extends AbstractController {
	/**
	 * @Nonce(reportTopic)
	 * @GetAttributes({topicId, forumId})
	 * @RoleAtLeast(fullUser)
	 */
	public function reportTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		
		$topicRep = $this->em->getRepository( 'TopicBase' );
		$topicBase = $topicRep->findBaseTopic( $topicId );
		if( null != $topicBase && !$topicBase->getIsDeleted() ) {
			$report = new ReportTopic();
			$report->setTopicId( $topicId );
			$report->setUserId( $userId );
			$report->setDate( $now );
			//			$report->setIsViewed( false );
			$report->setIsTreated( false );
			$report->setIsDeleted( false );
			
			$this->em->persist( $report );
			if( false === $this->em->flushSafe( $report ) ) {
				$this->frontController->addMessage( __( 'There was a problem during the report process' ), 'error' );
			} else {
				$this->frontController->addMessage( __( 'Your report was taking into account, thank you' ), 'correct' );
			}
		} else {
			$this->frontController->addMessage( __( 'This topic does not exist anymore ' ), 'error' );
		}
		
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * @Nonce(reportPost)
	 * @GetAttributes({postId, topicId, forumId})
	 * @RoleAtLeast(fullUser)
	 */
	public function reportPostAction() {
		$get = $this->frontController->getRequest()->query;
		$postId = $get->get( 'postId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		
		$postRep = $this->em->getRepository( 'PostBase' );
		$postBase = $postRep->findBasePost( $postId );
		if( null != $postBase && !$postBase->getIsDeleted() ) {
			$report = new ReportPost();
			$report->setPostId( $postId );
			$report->setUserId( $userId );
			$report->setDate( $now );
			//			$report->setIsViewed( false );
			$report->setIsTreated( false );
			$report->setIsDeleted( false );
			
			$this->em->persist( $report );
			if( false === $this->em->flushSafe( $report ) ) {
				$this->frontController->addMessage( __( 'There was a problem during the report process' ), 'error' );
			} else {
				$this->frontController->addMessage( __( 'Your report was taking into account, thank you' ), 'correct' );
			}
		} else {
			$this->frontController->addMessage( __( 'This topic does not exist anymore' ), 'error' );
		}
		
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * @Nonce(removeReportTopic)
	 * @GetAttributes({reportId, topicId, forumId})
	 * @RoleAtLeast(fullUser)
	 */
	public function removeReportTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$reportId = $get->get( 'reportId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ) {
			$userId = UserManager::$anonUserId;
		}
		
		$topicRep = $this->em->getRepository( 'ReportTopic' );
		
		$report = $topicRep->findOneBy( array( "id" => $reportId ) );
		if( null != $report ) {
			$report->setIsDeleted( true );
			$this->em->persist( $report );
			if( false === $this->em->flushSafe( $report ) ) {
				$this->frontController->addMessage( __( 'There was a problem during the report process' ), 'error' );
			} else {
				$this->frontController->addMessage( __( 'Your report was removed' ), 'correct' );
			}
		} else {
			$this->frontController->addMessage( __( 'This topic does not exist anymore ' ), 'error' );
		}
		
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * @Nonce(removeReportPost)
	 * @GetAttributes({reportId, topicId, forumId})
	 * @RoleAtLeast(fullUser)
	 */
	public function removeReportPostAction() {
		$get = $this->frontController->getRequest()->query;
		$reportId = $get->get( 'reportId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		if( !$userId ) {
			$userId = UserManager::$anonUserId;
		}
		
		$topicRep = $this->em->getRepository( 'ReportPost' );
		
		$report = $topicRep->findOneBy( array( "id" => $reportId ) );
		if( null != $report ) {
			$report->setIsDeleted( true );
			$this->em->persist( $report );
			if( false === $this->em->flushSafe( $report ) ) {
				$this->frontController->addMessage( __( 'There was a problem during the report process' ), 'error' );
			} else {
				$this->frontController->addMessage( __( 'Your report was removed' ), 'correct' );
			}
		} else {
			$this->frontController->addMessage( __( 'This topic does not exist anymore ' ), 'error' );
		}
		
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * @Nonce(displayManageReportForm)
	 * @GetAttributes({userId})
	 * @RoleAtLeast(moderator)
	 */
	public function displayManageReportFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		$forumId = $get->get( 'forumId' );
		
		//		$page_num = $get->get( 'p', 1 );
		//		$num_per_page = $get->get( 'n', 20 );
		//		$timeTarget = $get->get( 'timeTarget', false );
		//		$lastPage = $get->get( 'lastPage', false );
		//		
		//		$max_pages = 10;
		//		$max_pages = ceil( $max_pages / $num_per_page );
		//		if( !$max_pages ) {
		//			$max_pages = 0;
		//		}
		//		$getArgs = array( 'forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%" );
		

		//		$link = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingleForum', $getArgs );
		//		$topicBaseRepo = $this->em->getRepository( 'TopicBase' );
		//		$topics = $topicBaseRepo->findReportedTopic();
		$topicReports = $this->em->getRepository( 'ReportTopic' )->findBy( array( 'isDeleted' => 0 ) );
		$postReports = $this->em->getRepository( 'ReportPost' )->findBy( array( 'isDeleted' => 0 ) );
		
		$reports = $this->transformReports( $topicReports, $postReports );
		
		//		$pagination = new Paginator();
		//		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ) );
		//		$userHelper = new UserHelper( $this->frontController );
		

		$response = $this->frontController->getResponse();
		$response->setVar( 'reports', $reports );
		$response->setVar( 'userId', $userId );
		
		$this->frontController->doDisplay( 'report', 'displayManageReportForm' );
	}
	
	/**
	 * @param array $topicReports
	 * @param array $postReports
	 * @return array('itemId' => int, 'isTopic' => bool, 'link' => string, 'title' => string, 'complete' => string, 'count' => int, 'report' => [Report])
	 */
	private function transformReports(array $topicReports, array $postReports) {
		$vh = $this->frontController->getViewHelper();
		
		$topicLink = $vh->createHrefWithNonce( 'manageReport', 'Report', 'manageReport', 
			array( 'reportIds' => '%report_ids%', 'itemId' => '%item_id%', 'isTopic' => 1 ) );
		$postLink = $vh->createHrefWithNonce( 'manageReport', 'Report', 'manageReport', 
			array( 'reportIds' => '%report_ids%', 'itemId' => '%item_id%', 'isTopic' => 0 ) );
		$removeTopicLink = $vh->createHrefWithNonce( 'deleteReport', 'Report', 'deleteReport', 
			array( 'reportIds' => '%report_ids%', 'isTopic' => 1 ) );
		$removePostLink = $vh->createHrefWithNonce( 'deleteReport', 'Report', 'deleteReport', 
			array( 'reportIds' => '%report_ids%', 'isTopic' => 0 ) );
		
		$resultsTopics = array();
		$resultsPosts = array();
		
		// TODO remove duplicated, to gather them into a single item
		// put only the last date of report, increment count
		if( $topicReports ) {
			$topicRepo = $this->em->getRepository( 'TopicBase' );
			while( $topicReports ) {
				$item = array_pop( $topicReports );
				$lastDate = $item->getDate();
				$itemId = $item->getTopicId();
				$count = 1;
				$isTreated = $item->getIsTreated();
				$reportIds = array( $item->getId() );
				
				// before gathering information, we check if there is already a report against this item
				for($i = count( $topicReports ) - 1; $i >= 0; $i--) {
					$tempReport = $topicReports[$i];
					$tempDate = $tempReport->getDate();
					if( $itemId == $tempReport->getTopicId() && $isTreated === $tempReport->getIsTreated() ) {
						if( $lastDate < $tempDate ) {
							$lastDate = $tempDate;
						}
						$reportIds[] = $tempReport->getId();
						$count++;
						array_splice( $topicReports, $i, 1 );
					}
				}
				
				$topic = $topicRepo->find( $itemId );
				$text = $topic->getTitle();
				$excerpt = Utils::createExcerpt( $text, 32 );
				
				$temp = new stdClass();
				$temp->title = $excerpt;
				$temp->complete = $text;
				$temp->count = $count;
				$temp->itemId = $itemId;
				$temp->isTopic = true;
				$reportIds = serialize( $reportIds );
				$reportIds = urlencode( $reportIds );
				$temp->link = strtr( $topicLink, array( '%item_id%' => $itemId, '%report_ids%' => $reportIds ) );
				$temp->deleteLink = strtr( $removeTopicLink, array( '%report_ids%' => $reportIds ) );
				$temp->date = $lastDate;
				$temp->treated = $isTreated;
				//				$temp->report[] = $tempReports;
				$resultsTopics[] = $temp;
			}
		}
		
		if( $postReports ) {
			$postRepo = $this->em->getRepository( 'PostBase' );
			while( $postReports ) {
				$item = array_pop( $postReports );
				$lastDate = $item->getDate();
				$itemId = $item->getPostId();
				$count = 1;
				$isTreated = $item->getIsTreated();
				$reportIds = array( $item->getId() );
				
				// before gathering information, we check if there is already a report against this item
				for($i = count( $postReports ) - 1; $i >= 0; $i--) {
					$tempReport = $postReports[$i];
					$tempDate = $tempReport->getDate();
					if( $itemId == $tempReport->getPostId() && $isTreated === $tempReport->getIsTreated() ) {
						if( $lastDate < $tempDate ) {
							$lastDate = $tempDate;
						}
						$reportIds[] = $tempReport->getId();
						$count++;
						array_splice( $postReports, $i, 1 );
					}
				}
				
				$post = $postRepo->find( $itemId );
				$topic = $post->getTopic();
				$text = $topic->getTitle();
				$excerpt = Utils::createExcerpt( $text, 25 ) . ' [Post]';
				
				$temp = new stdClass();
				$temp->title = $excerpt;
				$temp->complete = $text . ' [Post]';
				$temp->count = $count;
				$temp->itemId = $itemId;
				$temp->isTopic = true;
				$reportIds = serialize( $reportIds );
				$reportIds = urlencode( $reportIds );
				$temp->link = strtr( $postLink, array( '%item_id%' => $itemId, '%report_ids%' => $reportIds ) );
				$temp->deleteLink = strtr( $removePostLink, array( '%report_ids%' => $reportIds ) );
				$temp->date = $lastDate;
				$temp->treated = $isTreated;
				//				$temp->report[] = $tempReports;
				$resultsTopics[] = $temp;
			}
		}
		
		$results = array_merge( $resultsTopics, $resultsPosts );
		return $results;
	}
	
	/**
	 * @Nonce(manageReport)
	 * @GetAttributes({isTopic, reportIds, itemId})
	 * @RoleAtLeast(moderator)
	 * Called when the moderator wants to reach the item to moderate. It redirects to that item but put the report as treated
	 * Avoid asking the database for all report when listed
	 */
	public function manageReportAction() {
		$get = $this->frontController->getRequest()->query;
		$isTopic = $get->get( 'isTopic' );
		$itemId = $get->get( 'itemId' );
		$reportIds = $get->get( 'reportIds' );
		$reportIds = urldecode( $reportIds );
		$reportIds = unserialize( $reportIds );
		
		if( $isTopic ) {
			$repo = $this->em->getRepository( 'ReportTopic' );
			$topic = $this->em->getRepository('TopicBase')->find($itemId);
			$forumId = $topic->getForum()->getId();
			$redirectLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic',
				array( 'topicId' => $itemId, 'forumId' => $forumId, 'highlightTopic' => true ) );
		} else {
			$post = $this->em->getRepository('PostBase')->find($itemId);
			$topic = $post->getTopic();
			$topicId = $topic->getId();
			$forumId = $topic->getForum()->getId();
			$repo = $this->em->getRepository( 'ReportPost' );
			$redirectLink = $this->frontController->getViewHelper()->createHref('Topic', 'displaySingleTopic',
				array( 'topicId' => $topicId, 'forumId' => $forumId, 'postIdTarget' => $itemId, 'highlightPost' => $itemId ) );
		}
		
		foreach( $reportIds as $id ) {
			$report = $repo->find( $id );
			if( null !== $report ) {
				if( false === $report->getIsTreated() ) {
					$report->setIsTreated( true );
					$this->em->persist( $report );
				}
			}
		}
		
		if( false === $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'The reports treatment failed' ), 'error' );
		} else {
			$this->frontController->addMessage( __( 'The reports treatment completed' ), 'correct' );
		}
		
		$this->frontController->doRedirectUrl($redirectLink);
	}
	/**
	 * @Nonce(deleteReport)
	 * @GetAttributes({isTopic, reportIds})
	 * @RoleAtLeast(moderator)
	 */
	public function deleteReportAction() {
		$get = $this->frontController->getRequest()->query;
		$isTopic = $get->get( 'isTopic' );
		$reportIds = $get->get( 'reportIds' );
		$reportIds = urldecode( $reportIds );
		$reportIds = unserialize( $reportIds );
		
		if( $isTopic ) {
			$repo = $this->em->getRepository( 'ReportTopic' );
		} else {
			$repo = $this->em->getRepository( 'ReportPost' );
		}
		
		foreach( $reportIds as $id ) {
			$report = $repo->find( $id );
			if( null !== $report ) {
				if( false === $report->getIsDeleted() ) {
					$report->setIsDeleted( true );
					$this->em->persist( $report );
				}
			}
		}
		
		if( false === $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'The reports deletion failed' ), 'error' );
		} else {
			$this->frontController->addMessage( __( 'The reports deletion completed' ), 'correct' );
		}
		
		$user = $this->frontController->getCurrentUser();
		$userId = $user->getId();
		
		$this->frontController->doRedirectWithNonce( 'displayManageReportForm', 'Report', 'displayManageReportForm', array( 'userId' => $userId ) );
	}
}