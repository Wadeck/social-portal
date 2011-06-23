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
	/**
	 * [forumId(opt for edit)]
	 */
	public function displayFormAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId', false );
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$form = new ForumForm( $this->frontController );
		
		$actionUrl = null;
		if (false !== $forumId) {
			$forum = $this->em->find( 'Forum', $forumId );
			if ($forum) {
				$actionUrl = $this->frontController->getViewHelper()->createHref( 'Forum', 'edit', array(), array('forumId' => $forumId ) );
				// fill the form with the forum current values 
				$form->setupWithForum( $forum );
				// then add the value retrieve from POST
				// they could erase the data but actually it's what we want
				$form->setupWithArray( true );
				$form->setNonceAction( 'editForum' );
			}
		}
		if (! $actionUrl) {
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
	public function createAction($parameters){
		$form = new ForumForm( $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$forum = new ForumEntity();
		$forum->setName( $form->getForumName() );
		$forum->setDescription( $form->getForumDescription() );
		$forum->setNumPosts( 0 );
		$forum->setNumTopics( 0 );
		
		$this->em->persist( $forum );
		if (! $this->em->flushSafe( $forum )) {
			$this->frontController->addMessage( __( 'There is already a forum called %name%', array('%name%' => $form->getForumName() ) ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'The creation of forum called %name% was a success', array('%name%' => $form->getForumName() ) ), 'correct' );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editForum)
	 * @GetAttributes(forumId)
	 */
	public function editAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId' );
		
		$form = new ForumForm( $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$forum = $this->em->find( 'Forum', $forumId );
		$forum->setName( $form->getForumName() );
		$forum->setDescription( $form->getForumDescription() );
		
		$this->em->persist( $forum );
		if (! $this->em->flushSafe( $forum )) {
			$this->frontController->addMessage( __( 'A problem occurred during the edition of forum called %name%', array('%name%' => $form->getForumName() ) ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'All the modification for the forum called %name% were a success', array('%name%' => $form->getForumName() ) ), 'correct' );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Nonce(deleteForum)
	 */
	public function deleteAction($parameters){
	}
	
	/**
	 * @Nonce(moveForum)
	 */
	public function moveAction($parameters){
	
	}
	
	public function viewAllAction($parameters){
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		$this->frontController->getResponse()->setVar( 'forums', $forums );
		$this->frontController->doDisplay( 'Forum', 'viewAll' );
	}
	
	/**
	 * @GetAttributes(forumId)
	 */
	public function displaySingleAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId' );
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 20 );
		
		$forum = $this->em->getRepository( 'Forum' )->find( $forumId );
		$topics = $this->em->getRepository( 'TopicBase' )->findTopicsFromForum( $forumId, $page_num, $num_per_page );
		
		if (! $forum) {
			Logger::getInstance()->debug( "The forum with id [$forumId] is not valid" );
			$this->frontController->addMessage( __( 'The given forum is not valid' ), 'error' );
			$this->frontController->doRedirect( 'forum', 'viewAll' );
		}
		
		$max_pages = $forum->getNumTopics();
		$max_pages = ceil( $max_pages / $num_per_page );
		if (! $max_pages) {
			$max_pages = 0;
		}
		$link = $this->frontController->getViewHelper()->createHref( 'Forum', 'displaySingle', array(), array('forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%" ) );
		
		$response = $this->frontController->getResponse();
		
		$pagination = new Paginator();
		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ) );
		$response->setVar( 'pagination', $pagination );
		
		$response->setVar( 'forum', $forum );
		$response->setVar( 'topics', $topics );
		
		$userHelper = new UserHelper($this->frontController);
		$response->setVar( 'userHelper', $userHelper );
		
		$forumHeader = new ForumHeader();
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		usort( $forums, function (ForumEntity $a, ForumEntity $b){
			if ($a == $b) {
				return 0;
			}
			return ($a->getPosition() < $b->getPosition()) ? - 1 : 1;
		} );
		$indexSelected = array_search( $forum, $forums );
		$forumHeader->createHeaders( $this->frontController, $forums, $indexSelected );
		
		$response->setVar( 'forumHeader', $forumHeader );
		
		$this->frontController->doDisplay( 'forum', 'displaySingleForum' );
	}

}