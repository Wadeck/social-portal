<?php

namespace socialportal\controller;
use core\topics\ForumHeader;

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

class Forum extends AbstractController {
	public function displayFormAction($parameters) {
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$form = new ForumForm( $this->frontController );
		
		$actionUrl = null;
		if( $parameters ) {
			$forum_id = $parameters[0];
			$forum = $this->em->find( 'Forum', $forum_id );
			if( $forum ) {
				$actionUrl = $this->frontController->getViewHelper()->createHref( 'Forum', 'edit', array( $forum_id ) );
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
	public function createAction($parameters) {
		$form = new ForumForm( $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$forum = new ForumEntity();
		$forum->setName( $form->getForumName() );
		$forum->setDescription( $form->getForumDescription() );
		$forum->setNumPosts(0);
		$forum->setNumTopics(0);
		
		$this->em->persist($forum);
		if( !$this->em->flushSafe( $forum ) ) {
			$this->frontController->addMessage( __( 'There is already a forum called %name%', array( '%name%' => $form->getForumName() ) ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'The creation of forum called %name% was a success', array( '%name%' => $form->getForumName() ) ), 'correct' );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editForum)
	 * @Parameters(1) 
	 */
	public function editAction($parameters) {
		if( !isset( $parameters[0] ) ) {
			$this->frontController->addMessage( __( 'No identifier for that forum, edit canceled.' ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		$param_id = $parameters[0];
		
		$form = new ForumForm( $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$forum = $this->em->find( 'Forum', $param_id );
		$forum->setName( $form->getForumName() );
		$forum->setDescription( $form->getForumDescription() );
		
		$this->em->persist($forum);
		if( !$this->em->flushSafe( $forum ) ) {
			$this->frontController->addMessage( __( 'A problem occurred during the edition of forum called %name%', array( '%name%' => $form->getForumName() ) ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'All the modification for the forum called %name% were a success', array( '%name%' => $form->getForumName() ) ),'correct' );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Nonce(deleteForum)
	 */
	public function deleteAction($parameters) {}
	
	/**
	 * @Nonce(moveForum)
	 */
	public function moveAction($parameters) {

	}
	
	public function viewAllAction($parameters) {
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		$this->frontController->getResponse()->setVar( 'forums', $forums );
		$this->frontController->doDisplay( 'Forum', 'viewAll' );
	}
	
	/**
	 * @Paramaters(1)
	 */
	public function displaySingleAction($parameters) {
		$forumId = $parameters[0];
		$get = $this->frontController->getRequest()->query;
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 20 );
		
		$forum = $this->em->getRepository( 'Forum' )->find( $forumId );
		$topics = $this->em->getRepository( 'TopicBase' )->findTopicsFromForum( $forumId, $page_num, $num_per_page );
		
		if( !$forum ) {
			Logger::getInstance()->debug( "The forum with id [$forumId] is not valid" );
			$this->frontController->addMessage( __( 'The given forum is not valid' ), 'error' );
			$this->frontController->doRedirect( 'forum', 'viewAll' );
		}
		
		$max_pages = $forum->getNumTopics();
		$max_pages = ceil($max_pages / $num_per_page);
		if( !$max_pages ) {
			$max_pages = 0;
		}
		$link = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingle', array( $forumId ), array( 'p' => "%#p%", 'n' => "%#n%" ) );
		
		$response = $this->frontController->getResponse();
		
		$pagination = new Paginator();
		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ) );
		$response->setVar( 'pagination', $pagination );
		
		$response->setVar( 'forum', $forum );
		$response->setVar( 'topics', $topics );
		
		$userHelper = new UserHelper();
		$response->setVar( 'userHelper', $userHelper );
		
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
		
		$response->setVar( 'forumHeader', $forumHeader );
		
		//TODO remove, for debug only
		$nonce = $this->frontController->getNonceManager()->createNonce( 'displayTopicForm' );
		$this->frontController->getResponse()->setVar('debug_nonce', $nonce);
		
		$this->frontController->doDisplay( 'forum', 'displaySingleForum' );
	}

}