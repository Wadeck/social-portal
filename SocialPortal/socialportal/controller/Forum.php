<?php

namespace socialportal\controller;
use socialportal\common\topic\TypeCenter;

use core\user\UserRoles;

use core\templates\ForumHeader;

use core\user\UserHelper;

use core\tools\Paginator;

use core\debug\Logger;

use socialportal\model\Forum as ForumEntity;

use core\form\custom\ForumForm;

use core\form\Form;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use socialportal\model;

use core\AbstractController;

use DateTime;

class Forum extends AbstractController {
	/**
	 * [forumId(opt for edit)]
	 */
	public function displayFormAction() {
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId', false );
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$form = new ForumForm( $this->frontController );
		
		$actionUrl = null;
		if( false !== $forumId ) {
			$forum = $this->em->find( 'Forum', $forumId );
			if( $forum ) {
				$actionUrl = $this->frontController->getViewHelper()->createHref( 'Forum', 'edit', array( 'forumId' => $forumId ) );
				// fill the form with the forum current values 
				$form->setupWithForum( $forum );
				// then add the value retrieve from POST
				// they could erase the data but actually it's what we want
				$form->setupWithArray( true );
				$form->setNonceAction( 'editForum' );
			}
		}
		if( !$actionUrl ) {
			$actionUrl = $this->frontController->getViewHelper()->createHref( 'Forum', 'create' );
			$form->setupWithArray( true );
			$form->setNonceAction( 'createForum' );
		}
		//		$form->setCss($cssClass)
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'forum', 'displayForm' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createForum)
	 */
	public function createAction() {
		$form = new ForumForm( $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$forum = new ForumEntity();
		$forum->setName( $form->getForumName() );
		$forum->setDescription( $form->getForumDescription() );
		$forum->setNumPosts( 0 );
		$forum->setNumTopics( 0 );
		
		$this->em->persist( $forum );
		if( !$this->em->flushSafe( $forum ) ) {
			$this->frontController->addMessage( __( 'There is already a forum called %name%', array( '%name%' => $form->getForumName() ) ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'The creation of forum called %name% was a success', array( '%name%' => $form->getForumName() ) ), 'correct' );
		// TODO redirect to the created forum !
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editForum)
	 * @GetAttributes(forumId)
	 */
	public function editAction() {
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId' );
		
		$form = new ForumForm( $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$forum = $this->em->find( 'Forum', $forumId );
		$forum->setName( $form->getForumName() );
		$forum->setDescription( $form->getForumDescription() );
		
		$this->em->persist( $forum );
		if( !$this->em->flushSafe( $forum ) ) {
			$this->frontController->addMessage( __( 'A problem occurred during the edition of forum called %name%', array( '%name%' => $form->getForumName() ) ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'All the modification for the forum called %name% were a success', array( '%name%' => $form->getForumName() ) ), 'correct' );
		
		// TODO redirect to the edited forum !
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Nonce(deleteForum)
	 */
	public function deleteAction() {}
	
	/**
	 * @Nonce(moveForum)
	 */
	public function moveAction() {

	}
	
	/**
	 * @deprecated
	 */
	public function viewAllAction() {
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		$this->frontController->getResponse()->setVar( 'forums', $forums );
		$this->frontController->doDisplay( 'Forum', 'viewAll' );
	}
	
	/**
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
		
		//TODO perhaps no more necessary
		$acceptedTopics = $metaRepo->getAcceptableTopics( $forumId );
		array_walk( $acceptedTopics, function (&$item, $key) {
			$item = TypeCenter::getTypeManager($item);
		} );
		
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
		
		if($this->frontController->getViewHelper()->currentUserIs(UserRoles::$admin_role)){
			$getArgs = array( 'n' => $num_per_page, 'p'=>$page_num, 'forumId' => $forumId);
			if(!$withDeleted){
				$getArgs['withDeleted'] = true;
				$response->setVar('isDisplayDeleted', true);
			}else{
				$response->setVar('isDisplayDeleted', false);
			}
			$displayDeletedLink = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingleForum', $getArgs );
		}else{
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
		
		$this->frontController->getViewHelper()->setTitle( $forum->getName() );

		$this->frontController->doDisplay( 'forum', 'displaySingleForum' );
	}

}