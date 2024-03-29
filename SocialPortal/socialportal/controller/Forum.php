<?php

namespace socialportal\controller;
use socialportal\common\topic\TypeCenter;

use core\user\UserRoles;

use socialportal\common\templates\ForumHeader;

use core\user\UserHelper;

use socialportal\common\templates\Paginator;

use core\debug\Logger;

use socialportal\model\Forum as ForumEntity;

use socialportal\common\form\custom\ForumForm;

use core\form\Form;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use socialportal\model;

use core\AbstractController;

use DateTime;

class Forum extends AbstractController {
	/**
	 * @RoleAtLeast(fulluser)
	 * @GetAttributes(forumId)
	 * [p, n, timeTarget(timestamp int), lastPage(boolean), withDeleted(boolean)]
	 */
	public function displaySingleForumAction() {
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId' );
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 20 );
		$timeTarget = $get->get( 'timeTarget', false );
		$lastPage = $get->get( 'lastPage', false );
		$withDeleted = $get->get( 'withDeleted', false );
		
		$forumRepo = $this->em->getRepository( 'Forum' );
		$topicBaseRepo = $this->em->getRepository( 'TopicBase' );
		$forum = $forumRepo->find( $forumId );
		
		if( !$forum ) {
			Logger::getInstance()->log( "The forum with id [$forumId] is not valid" );
			
			$defaultForumId = $forumRepo->getFirstId();
			if( false === $defaultForumId ) {
				$this->frontController->addMessage( __( 'There is no forum at the moment' ), 'error' );
				$this->frontController->doRedirect( 'home' );
			} else {
				$this->frontController->addMessage( __( 'The given forum is not valid, redirection to default forum' ), 'info' );
				$this->frontController->doRedirect( 'forum', 'displaySingleForum', array( 'forumId' => $defaultForumId ) );
			}
		}
		if( $withDeleted ) {
			$max_pages = $forumRepo->getCountWithDeleted( $forumId );
		} else {
			$max_pages = $forum->getNumTopics();
		}
		
		$max_pages = ceil( $max_pages / $num_per_page );
		if( !$max_pages ) {
			$max_pages = 0;
		}
		
		if( false !== $timeTarget ) {
			// we want to go to a specific topic given by date
			$timeTarget = new DateTime( "@$timeTarget", $this->frontController->getDateTimeZone() );
			$page_num = $forumRepo->getTopicPageByDate( $forumId, $timeTarget, $num_per_page, $withDeleted );
		} else if( false !== $lastPage ) {
			// we want to go to the last page
			//			$page_num = $forumRepo->getLastPage( $forumId, $num_per_page );
			$page_num = $max_pages;
		}
		
		$topics = $topicBaseRepo->findTopicsFromForum( $forumId, $page_num, $num_per_page, $withDeleted );
		
		if(1 == $page_num){
			// we retrieve the stickies
			$stickyTopics = $topicBaseRepo->findStickyTopicsFromForum($forumId);
		}else{
			$stickyTopics = null;
		}
		
		$response = $this->frontController->getResponse();
		
		$metaRepo = $this->em->getRepository( 'ForumMeta' );
		
		if( $this->frontController->getViewHelper()->currentUserIsAtLeast(UserRoles::$full_user_role) ){
			$title = __('Click here to create a new topic');
			$content = __('New');
			// in case the user has the right, we check how much topic we can create
			$acceptedTopics = $metaRepo->getAcceptableTopics( $forumId );
			array_walk( $acceptedTopics, function (&$item, $key) {
				$item = TypeCenter::getTypeManager($item);
			} );
			$count = count($acceptedTopics);
			if($count >= 2){
				// link to the chooseType
				$newTopicLink = '<a class="button" href="' .
					$this->frontController->getViewHelper()->createHref('Topic', 'chooseTypeForum', array('forumId' => $forumId)) .
					'" title="' . $title . '">' . $content . '</a>';
			}else{
				// link to the topic
				$newTopicLink = '<a class="button" href="' .
					$this->frontController->getViewHelper()->createHrefWithNonce('displayForm', 'Topic', 'displayForm',
						array('forumId' => $forumId, 'typeId' => $acceptedTopics[0]->getTypeId())) .
					'" title="' . $title . '">' . $content . '</a>';
			}
		}else{
			// anonymous/limited users cannot create topics
			// actually the anonymous have not the right to access this page
			$newTopicLink = false;
			$acceptedTopics = array();
		}
		
		$getArgs = array( 'forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%" );
		if( $withDeleted ) {
			$getArgs['withDeleted'] = true;
		}
		$link = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingleForum', $getArgs );
		
		$pagination = new Paginator();
		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ) );
		
		$userHelper = new UserHelper( $this->frontController );
		
		$forumHeader = new ForumHeader();
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		usort( $forums, function (ForumEntity $a, ForumEntity $b) {
			if( $a == $b ) {
				return 0;
			}
			return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
		} );
		$indexSelected = array_search( $forum, $forums );
		$forumHeader->createHeaders( $this->frontController, $forums, $indexSelected );
		
		if($this->frontController->getViewHelper()->currentUserIsAtLeast(UserRoles::$moderator_role)){
			$getArgs = array( 'n' => $num_per_page, 'p'=>$page_num, 'forumId' => $forumId);
			if(!$withDeleted){
				$getArgs['withDeleted'] = true;
				$response->setVar('isDisplayDeleted', true);
			}else{
				$response->setVar('isDisplayDeleted', false);
			}
			$displayDeletedLink = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingleForum', $getArgs );
		}else{
			// only moderator+ can display deleted topics
			$displayDeletedLink = false;
		}
		$response->setVar( 'displayDeletedLink', $displayDeletedLink );
		$response->setVar( 'acceptedTopics', $acceptedTopics );
		$response->setVar( 'pagination', $pagination );
		$response->setVar( 'forum', $forum );
		$response->setVar( 'topics', $topics );
		$response->setVar( 'stickyTopics', $stickyTopics );
		$response->setVar( 'userHelper', $userHelper );
		$response->setVar( 'forumHeader', $forumHeader );
		$response->setVar( 'newTopicLink', $newTopicLink );
		
		$this->frontController->getViewHelper()->setTitle( $forum->getName() );

		$this->frontController->doDisplay( 'forum', 'displaySingleForum' );
	}

}