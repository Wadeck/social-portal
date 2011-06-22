<?php

namespace socialportal\controller;
use core\topics\templates\ModuleInsertTemplate;

use core\form\custom\PostFormFactory;

use core\topics\templates\MessageInsertTemplate;

use core\user\UserRoles;

use core\tools\Paginator;

use socialportal\repository\ForumMetaRepository;

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
	public function chooseTypeAction(){
		// set the nonce to each link that will be created, in an invisible manner for the view
		$forums = $this->em->getRepository( 'Forum' )->findAll();
		$topicsFor = array();
		if ($forums) {
			$metaRepo = $this->em->getRepository( 'ForumMeta' );
			foreach ( $forums as $f ) {
				$acceptedTopics = $metaRepo->getAcceptableTopics( $f->getId() );
				array_walk( $acceptedTopics, function (&$item, $key){
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
	 * @Nonce(displayTopicForm)
	 * @GetAttributes({typeId, forumId})
	 * [topic_id(opt, only for edit)]
	 */
	public function displayFormAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$topicType = $get->get( 'typeId' );
		$forumId = $get->get( 'forumId' );
		
		// check if the forum accept the custom type proposed 
		$forumMeta = $this->em->getRepository( 'ForumMeta' );
		if (! $forumMeta->isAcceptedBy( $forumId, $topicType )) {
			$this->frontController->addMessage( __( 'This forum does not accept the type of topic you passed' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$form = TopicFormFactory::createForm( $topicType, $this->frontController );
		if (! $form) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array('%type%' => $topicType ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'chooseType' );
		}
		$module = '';
		
		$getArgs = array('typeId' => $topicType );
		// now the form is valid we check if we can already fill it with previous value (from db)
		if (count( $parameters ) >= 3) {
			$topic_id = $parameters[2];
			
			$topicRepo = $this->em->getRepository( 'TopicBase' );
			$currentTopic = $topicRepo->findFullTopic( $topic_id );
			
			// check if the class correspond to what is attempted !
			$customClass = TopicType::translateTypeIdToName( $topicType );
			if (! $currentTopic instanceof $customClass) {
				$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
				$this->frontController->doRedirect( 'forum', 'viewAll' );
			}
			
			$form->setupWithTopic( $currentTopic );
			$form->setNonceAction( 'editTopic' );
			$module = 'edit';
			$getArgs['topicId'] = $topic_id;
		} else {
			$form->setNonceAction( 'createTopic' );
			$module = 'create';
			$getArgs['forumId'] = $forumId;
		}
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Topic', $module, array(), $getArgs );
		
		// fill the form with the posted field and errors
		$form->setupWithArray( true );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'topic', 'displayForm' );
	}
	
	/**
	 * @GetAttributes({topicId, forumId})
	 * [p, n]
	 */
	public function displaySingleTopicAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 10 );
		
		$topic = $this->em->getRepository( 'TopicBase' )->findFullTopic( $topicId );
		$base = $topic->getTopicbase();
		$typeId = $base->getCustomType();
		$posts = $this->em->getRepository( 'PostBase' )->findAllFullPosts( $topicId, $typeId, $page_num, $num_per_page );
		$max_pages = $base->getNumPosts();
		$max_pages = ceil( $max_pages / $num_per_page );
		if (! $max_pages) {
			$max_pages = 0;
		}
		$link = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', array(), array('topicId' => $topicId, 'forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%" ) );
		
		$pagination = new Paginator();
		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ) );
		
		// condition to satisfy to be able to write a comment
		if (! $this->frontController->getViewHelper()->currentUserIs( UserRoles::$anonymous_role )) {
			$commentForm = new ModuleInsertTemplate( $this->frontController, 'Post', 'displayForm', array(), array('typeId' => $typeId, 'topicId' => $topicId, 'forumId' => $forumId ), 'displayPostForm' );
		} else {
			$commentForm = new MessageInsertTemplate( $this->frontController, __( 'You do not have the right to add comment' ) );
		}
		
		$this->frontController->getResponse()->setVar( 'commentForm', $commentForm );
		$this->frontController->getResponse()->setVar( 'pagination', $pagination );
		$this->frontController->getResponse()->setVar( 'posts', $posts );
		$this->frontController->getResponse()->setVar( 'topic', $topic );
		$this->frontController->getResponse()->setVar( 'topicTemplate', TopicType::getTopicTemplate( $typeId, $this->frontController, $this->em, $topic ) );
		$this->frontController->getResponse()->setVar( 'postsTemplate', TopicType::getPostTemplate( $typeId, $this->frontController, $this->em, $posts ) );
		
		$this->frontController->doDisplay( 'topic', 'displaySingleTopic' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createTopic)
	 * @GetAttributes({typeId, forumId})
	 */
	public function createAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$forumId = $get->get( 'forumId' );
		
		// check if the forum accept the custom type proposed 
		$forumMeta = $this->em->getRepository( 'ForumMeta' );
		if (! $forumMeta->isAcceptedBy( $forumId, $typeId )) {
			$this->frontController->addMessage( __( 'This forum does not accept the type of topic you passed' ), 'error' );
			$this->frontController->doRedirectUrl( 'home' );
		}
		
		$form = TopicFormFactory::createForm( $typeId, $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$base = new TopicEntity();
		$base->setCustomType( $typeId );
		$base->setForum( $this->em->getReference( 'Forum', $forumId ) );
		$base->setIsDeleted( 0 );
		$base->setIsOpen( 1 );
		$base->setIsSticky( 0 );
		$base->setLastposter( $this->em->getReference( 'User', UserManager::$nullUserId ) );
		$base->setNumPosts( 0 );
		if (! $this->frontController->getCurrentUser()->getId()) {
			$base->setPoster( $this->em->getReference( 'User', UserManager::$anonUserId ) );
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
		if (! $this->em->flushSafe()) {
			$this->frontController->addMessage( __( 'There was a problem during the creation of the topic, try with an other title or in a moment' ), 'error' );
			//TODO problem here the referrer needs authentification that we don't have
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		// increment the number of topic in the forum parent
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId );
		
		//TODO redirection vers le topic en question
		$this->frontController->addMessage( __( 'The creation of the topic was a success' ), 'correct' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array(), array('topicId' => $topic->getId(), 'forumId' => $forumId ) );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editTopic)
	 * @GetAttributes({typeId, topicId})
	 */
	public function editAction($parameters){
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$topicId = $get->get( 'topicId' );
		
		$form = TopicFormFactory::createForm( $typeId, $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$existing = $topicRepo->findFullTopic( $topicId );
		if (! $existing) {
			$this->frontController->addMessage( __( 'The given id for the edition was invalid' ), 'error' );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		
		// check if the forum accept the custom type proposed 
		$customClass = TopicType::translateTypeIdToName( $typeId );
		if (! $existing instanceof $customClass) {
			$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		
		$base = $existing->getTopicbase();
		$base->setTitle( $form->getTopicTitle() );
		
		$existing = $form->createSpecificTopic( $base, $existing );
		$this->em->persist( $base );
		$this->em->persist( $existing );
		if (! $this->em->flushSafe()) {
			$this->frontController->addMessage( __( 'There was a problem during the edition of the topic' ), 'error' );
			//TODO problem here the referrer needs authentification that we don't have
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'The edition of the topic was a success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'viewAll' );
	
	}
	
	/**
	 * Wargning, do no forget to decrease the num topics of the forum
	 * @Nonce(deleteTopic)
	 * @Parameters(1)
	 * Parameters[topicId]
	 */
	public function deleteAction($parameters){
	}
	
	/**
	 * Warning, do not forget to increase the num topics of the forum
	 * @Nonce(undeleteTopic)
	 * @Parameters(1)
	 * Parameters[topicId]
	 */
	public function undeleteAction($parameters){
	}
	
	//	/**
	//	 * @Nonce(moveTopic)
	//	 * @Parameters(1)
	//	 * Parameters[topicId]
	//	 */
	//	public function moveAction($parameters) {
	//
	//	}
	

	/**
	 * @Nonce(stickTopic)
	 * @Parameters(1)
	 * Parameters[topicId]
	 */
	public function stickAction($parameters){
	
	}
	/**
	 * @Nonce(unstickTopic)
	 * @Parameters(1)
	 * Parameters[topicId]
	 */
	public function unstickAction($parameters){
	
	}
	/**
	 * @Nonce(closeTopic)
	 * @Parameters(1)
	 * Parameters[topicId]
	 */
	public function closeAction($parameters){
	
	}
	/**
	 * @Nonce(openTopic)
	 * @Parameters(1)
	 * Parameters[topicId]
	 */
	public function openAction($parameters){
	
	}

	//	public function viewAllAction($parameters) {
//		$forums = $this->em->getRepository( 'Forum' )->findAll();
//		$this->frontController->getResponse()->setVar( 'forums', $forums );
//		$this->frontController->doDisplay( 'Forum', 'viewAll' );
//	}


}