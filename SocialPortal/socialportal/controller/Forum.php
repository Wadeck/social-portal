<?php

namespace socialportal\controller;
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
		if( !$this->em->persistAndFlush( $forum ) ) {
			$this->frontController->addMessage( __( 'There is already a forum called %name%', array( '%name%' => $form->getForumName() ) ) );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'The creation of forum called %name% was a success', array( '%name%' => $form->getForumName() ) ) );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editForum)
	 */
	public function editAction($parameters) {
		if( !isset( $parameters[0] ) ) {
			$this->frontController->addMessage( __( 'No identifier for that forum, edit canceled.' ) );
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
		
		if( !$this->em->persistAndFlush( $forum ) ) {
			$this->frontController->addMessage( __( 'A problem occurred during the edition of forum called %name%', array( '%name%' => $form->getForumName() ) ) );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'All the modification for the forum called %name% were a success', array( '%name%' => $form->getForumName() ) ) );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 */
	public function deleteAction($parameters) {}
	
	public function viewAllAction($parameters) {
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		$this->frontController->getResponse()->setVar( 'forums', $forums );
		$this->frontController->doDisplay( 'Forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 */
	public function moveAction($parameters) {

	}
}