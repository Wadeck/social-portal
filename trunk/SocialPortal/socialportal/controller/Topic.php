<?php

namespace socialportal\controller;
use core\user\UserManager;

use core\form\custom\TopicFormFactory;

use core\topics\TopicType;

use core\debug\Logger;

use socialportal\model\TopicBase as TopicEntity;

use core\form\custom\ForumForm;

use core\form\Form;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use socialportal\model;

use core\AbstractController;

class Topic extends AbstractController {
	
	/**
	 * 
	 */
	public function chooseTypeAction() {
		// set the nonce to each link that will be created, in an invisible manner for the view
		$nonce = $this->frontController->getNonceManager()->createNonce( 'displayTopicForm' );
		$this->frontController->getViewHelper()->setNonce( $nonce );
		
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		$topicsFor = array();
		if( $forums ) {
			$metaRepo = $this->em->getRepository( 'ForumMeta' );
			foreach( $forums as $f ) {
				$acceptedTopics = $metaRepo->getAcceptableTopics( $f->getId() );
				array_walk( $acceptedTopics, function (&$item, $key) {
					$item = TopicType::createById( $item );
				} );
				$topicsFor[$f->getId()] = $acceptedTopics;
			}
		}
		
		$this->frontController->getResponse()->setVar( 'forums', $forums );
		$this->frontController->getResponse()->setVar( 'topicsFor', $topicsFor );
		$this->frontController->doDisplay( 'Topic', 'chooseType' );
	}
	
	/**
	 * Paramaters = [topic_type_id, forum_id]
	 * @Nonce(displayTopicForm)
	 */
	public function displayFormAction($parameters) {
		// displayForm/idType/(opt)idTopic(to edit)
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$actionUrl = null;
		if( count( $parameters ) < 2 ) {
			$this->frontController->addMessage( __( 'You should add a topic type id and a forum id to your request' ) );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		
		$topicType = $parameters[0];
		$form = TopicFormFactory::createForm( $topicType, $this->frontController );
		if( !$form ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $topicType ) ) );
			$this->frontController->doRedirect( 'topic', 'chooseType' );
		}
		$module = '';
		$forumId = $parameters[1];
		
		// now the form is valid we check if we can already fill it with previous value (from db)
		if( count( $parameters ) >= 3 ) {
			$topic_id = $parameters[2];
			$topicRepo = $this->em->getRepository( 'TopicBase' );
			$currentTopic = $topicRepo->findFullTopic( $topic_id );
			$form->setupWithTopic( $currentTopic );
			$form->setNonceAction( 'editTopic' );
			$module = 'edit';
		} else {
			$form->setNonceAction( 'createTopic' );
			$module = 'create';
		}
		
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Topic', $module, array( $topicType, $forumId ) );
		
		// fill the form with the posted field and errors
		$form->setupWithArray( true );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'topic', 'displayForm' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createTopic)
	 */
	public function createAction($parameters) {
		if( count( $parameters ) < 2 ) {
			$this->frontController->addMessage( __( 'You should add a topic type id to your request' ) );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		$typeId = $parameters[0];
		$forumId = $parameters[1];
		
		$form = TopicFormFactory::createForm( $typeId, $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$base = new TopicEntity();
		$base->setCustomType( $typeId );
		$base->setForum( $this->em->getPartialReference( 'Forum', $forumId ) );
		$base->setIsDeleted( 0 );
		$base->setIsOpen( 1 );
		$base->setIsSticky( 0 );
		$base->setLastposter( $this->em->getPartialReference( 'User', UserManager::$nullUserId ) );
		$base->setNumPosts( 0 );
		if( !$this->frontController->getCurrentUser()->getId() ) {
			$base->setPoster( $this->em->getPartialReference( 'User', UserManager::$anonUserId ) );
		} else {
			$base->setPoster( $this->frontController->getCurrentUser() );
		}
		$now = new \DateTime( '@' . $this->frontController->getRequest()->getRequestTime() );
		$base->setStartTime( $now );
		$base->setTime( $now );
		$base->setTagCount( 0 );
		$base->setTitle( $form->getTopicTitle() );
		$topic = $form->createSpecificTopic( $base );
		$this->em->persist( $base );
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'There was a problem during the creation of the topic, try with an other title or in a moment' ) );
			//TODO problem here the referrer needs authentification that we don't have
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		// increment the number of topic in the forum parent
		$this->em->getRepository('Forum')->incrementTopicCount($forumId);
		
		$this->frontController->addMessage( __( 'The creation of the topic was a success' ) );
		$this->frontController->doRedirect( 'Forum', 'viewAll' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editTopic)
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
		
		$this->em->persist( $forum );
		try {
			$this->em->flush( $forum );
		} catch (\Exception $e ) {
			Logger::getInstance()->debug_var( 'Exception from Forum#edit', $e );
			// not ok
			$this->frontController->addMessage( __( 'A problem occurred during the edition of forum called %name%', array( '%name%' => $form->getForumName() ) ) );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'All the modification for the forum called %name% were a success', array( '%name%' => $form->getForumName() ) ) );
		$this->frontController->doRedirect( 'forum', 'viewAll' );
	}
	
	/**
	 * @Nonce(deleteTopic)
	 */
	public function deleteAction($parameters) {}
	
	/**
	 * @Nonce(moveTopic)
	 */
	public function moveAction($parameters) {

	}
	/**
	 * @Nonce(stickTopic)
	 */
	public function stickAction($parameters) {

	}
	/**
	 * @Nonce(unstickTopic)
	 */
	public function unstickAction($parameters) {

	}
	/**
	 * @Nonce(closeTopic)
	 */
	public function closeAction($parameters) {

	}
	/**
	 * @Nonce(openTopic)
	 */
	public function openAction($parameters) {

	}

	//	public function viewAllAction($parameters) {
//		$forums = $this->em->getRepository( 'Forum' )->findAll();
//		$this->frontController->getResponse()->setVar( 'forums', $forums );
//		$this->frontController->doDisplay( 'Forum', 'viewAll' );
//	}


}