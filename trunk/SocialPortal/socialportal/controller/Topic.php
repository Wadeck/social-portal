<?php

namespace socialportal\controller;
use core\templates\ModuleInsertTemplate;

use core\form\custom\PostFormFactory;

use core\templates\MessageInsertTemplate;

use core\user\UserRoles;

use core\tools\Paginator;

use socialportal\repository\ForumMetaRepository;

use core\user\UserManager;

use core\form\custom\TopicFormFactory;

use core\tools\TopicType;

use core\debug\Logger;

use socialportal\model\TopicBase as TopicEntity;

use core\form\custom\ForumForm;

use core\form\Form;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use socialportal\model;

use core\AbstractController;
use DateTime;
class Topic extends AbstractController {
	
	/**
	 * 
	 */
	public function chooseTypeAction() {
		// set the nonce to each link that will be created, in an invisible manner for the view
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
	 * @GetAttributes(forumId)
	 */
	public function chooseTypeForumAction() {
		$get = $this->frontController->getRequest()->query;
		$forumId = $get->get( 'forumId' );
		
		// set the nonce to each link that will be created, in an invisible manner for the view
		$forum = $this->em->getRepository( 'Forum' )->find( $forumId );
		$topicsFor = array();
		if( !$forum ) {
			$this->frontController->addMessage( __( 'The desired forum is not accessible, so all forum possibilities are displayed' ) );
			$this->frontController->doRedirect( 'Topic', 'chooseType' );
		}
		
		$metaRepo = $this->em->getRepository( 'ForumMeta' );
		$acceptedTopics = $metaRepo->getAcceptableTopics( $forumId );
		array_walk( $acceptedTopics, function (&$item, $key) {
			$item = TopicType::createById( $item );
		} );
		
		$this->frontController->getResponse()->setVar( 'forum', $forum );
		$this->frontController->getResponse()->setVar( 'acceptedTopics', $acceptedTopics );
		$this->frontController->doDisplay( 'Topic', 'chooseTypeForum' );
	}
	
	/**
	 * @GetAttributes({topicId, forumId})
	 * [p, n, positionTarget(int), timeTarget(timestamp int), lastPage(boolean), withDeleted(boolean)]
	 */
	public function displaySingleTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 10 ); 
		$positionTarget = $get->get( 'positionTarget', false );
		$timeTarget = $get->get( 'timeTarget', false );
		$lastPage = $get->get( 'lastPage', false );
		$withDeleted = $get->get( 'withDeleted', false );
		
		$postBaseRepo = $this->em->getRepository( 'PostBase' );
		$topicBaseRepo = $this->em->getRepository( 'TopicBase' );
		
		$topic = $topicBaseRepo->findFullTopic( $topicId );
		$base = $topic->getTopicbase();
		$typeId = $base->getCustomType();
		
		if( $withDeleted ) {
			$max_pages = $topicBaseRepo->getCountWithDeleted( $topicId );
		} else {
			$max_pages = $base->getNumPosts();
		}
		$max_pages = ceil( $max_pages / $num_per_page );
		if( !$max_pages ) {
			$max_pages = 0;
		}
		
		if( false !== $positionTarget ) {
			// we want to go to a specific topic
			$page_num = $topicBaseRepo->getPostPagePerPosition( $topicId, $typeId, $positionTarget, $num_per_page, $withDeleted );
		} else if( false !== $timeTarget ) {
			// we want to go to a specific topic
			$timeTarget = new DateTime( "@$timeTarget" );
			$page_num = $topicBaseRepo->getPostPagePerTime( $topicId, $typeId, $timeTarget, $num_per_page, $withDeleted );
		} else if( false !== $lastPage ) {
			// we want to go to the last page
			//			$page_num = $topicBaseRepo->getLastPage( $topicId, $typeId, $num_per_page );
			$page_num = max( 1, $max_pages );
		}
		
		$posts = $postBaseRepo->findAllFullPosts( $topicId, $typeId, $page_num, $num_per_page, $withDeleted );
		$getArgs = array( 'topicId' => $topicId, 'forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%" );
		if( $withDeleted ) {
			$getArgs['withDeleted'] = true;
		}
		$link = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', $getArgs );
		
		$pagination = new Paginator();
		$pagination->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ), false, false );
		
		// condition to satisfy to be able to write a comment
		if(!$base->getIsOpen()){
			$commentForm = new MessageInsertTemplate( $this->frontController, __( 'The topic is closed, no more comment accepted' ) );
		}else if( $this->frontController->getViewHelper()->currentUserIs( UserRoles::$anonymous_role ) ) {
			$commentForm = new MessageInsertTemplate( $this->frontController, __( 'You do not have the right to add comment' ) );
		} else {
			$commentForm = new ModuleInsertTemplate( $this->frontController, 'Post', 'displayForm', array( 'typeId' => $typeId, 'topicId' => $topicId, 'forumId' => $forumId ), 'displayPostForm' );
		}
		
		
		$response = $this->frontController->getResponse();
		$response->setVar( 'commentForm', $commentForm );
		$response->setVar( 'pagination', $pagination );
		$response->setVar( 'posts', $posts );
		$response->setVar( 'topic', $topic );
		$response->setVar( 'forumId', $forumId );
		$response->setVar( 'topicTemplate', TopicType::getTopicTemplate( $typeId, $this->frontController, $this->em, $topic ) );
		$response->setVar( 'postsTemplate', TopicType::getPostTemplate( $typeId, $this->frontController, $this->em, $posts ) );
		
		if( $this->frontController->getViewHelper()->currentUserIs( UserRoles::$admin_role ) ) {
			$getArgs = array( 'n' => $num_per_page, 'p'=>$page_num, 'topicId' => $topicId, 'forumId' => $forumId);
			if(!$withDeleted){
				$getArgs['withDeleted'] = true;
				$response->setVar('isDisplayDeleted', true);
			}else{
				$response->setVar('isDisplayDeleted', false);
			}
			$displayDeletedLink = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', $getArgs );
		} else {
			$displayDeletedLink = false;
		}
		$response->setVar( 'displayDeletedLink', $displayDeletedLink );
		
		$this->frontController->doDisplay( 'topic', 'displaySingleTopic' );
	}
	
	/**
	 * @Nonce(displayTopicForm)
	 * @GetAttributes({typeId, forumId})
	 * [topic_id(opt, only for edit)]
	 */
	public function displayFormAction() {
		$get = $this->frontController->getRequest()->query;
		$topicType = $get->get( 'typeId' );
		$forumId = $get->get( 'forumId' );
		$topicId = $get->get( 'topicId', false );
		
		// check if the forum accept the custom type proposed 
		$forumMeta = $this->em->getRepository( 'ForumMeta' );
		if( !$forumMeta->isAcceptedBy( $forumId, $topicType ) ) {
			$this->frontController->addMessage( __( 'This forum does not accept the type of topic you passed' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$form = TopicFormFactory::createForm( $topicType, $this->frontController );
		if( !$form ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $topicType ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'chooseType' );
		}
		$module = '';
		
		$getArgs = array( 'typeId' => $topicType, 'forumId' => $forumId );
		
		// now the form is valid we check if we can already fill it with previous value (from db)
		if( false !== $topicId ) {
			$topicRepo = $this->em->getRepository( 'TopicBase' );
			$currentTopic = $topicRepo->findFullTopic( $topicId );
			
			// check if the class correspond to what is attempted !
			$customClass = TopicType::translateTypeIdToName( $topicType );
			if( !$currentTopic instanceof $customClass ) {
				$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
				$this->frontController->doRedirect( 'forum', 'viewAll' );
			}
			
			$form->setupWithTopic( $currentTopic );
			$form->setNonceAction( 'editTopic' );
			$module = 'edit';
			$getArgs['topicId'] = $topicId;
		} else {
			$form->setNonceAction( 'createTopic' );
			$module = 'create';
		}
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Topic', $module, $getArgs );
		
		// fill the form with the posted field and errors
		$form->setupWithArray( true );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'forumId', $forumId );
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'topic', 'displayForm' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createTopic)
	 * @GetAttributes({typeId, forumId})
	 */
	public function createAction() {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$forumId = $get->get( 'forumId' );
		
		// check if the forum accept the custom type proposed 
		$forumMeta = $this->em->getRepository( 'ForumMeta' );
		if( !$forumMeta->isAcceptedBy( $forumId, $typeId ) ) {
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
		if( !$this->frontController->getCurrentUser()->getId() ) {
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
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'There was a problem during the creation of the topic, try with an other title or in a moment' ), 'error' );
			//TODO problem here the referrer needs authentification that we don't have
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$topicId = $base->getId();
		// increment the number of topic in the forum parent
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId );
		
		//TODO redirection vers le topic en question
		$this->frontController->addMessage( __( 'The creation of the topic was a success' ), 'correct' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editTopic)
	 * @GetAttributes({typeId, forumId, topicId})
	 */
	public function editAction() {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$form = TopicFormFactory::createForm( $typeId, $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$existing = $topicRepo->findFullTopic( $topicId );
		if( !$existing ) {
			$this->frontController->addMessage( __( 'The given id for the edition was invalid' ), 'error' );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		
		// check if the forum accept the custom type proposed 
		$customClass = TopicType::translateTypeIdToName( $typeId );
		if( !$existing instanceof $customClass ) {
			$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		
		$base = $existing->getTopicbase();
		$base->setTitle( $form->getTopicTitle() );
		
		$existing = $form->createSpecificTopic( $base, $existing );
		$this->em->persist( $base );
		$this->em->persist( $existing );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'There was a problem during the edition of the topic' ), 'error' );
			//TODO problem here the referrer needs authentification that we don't have
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		$this->frontController->addMessage( __( 'The edition of the topic was a success' ), 'correct' );
		//		$this->frontController->doRedirect( 'Forum', 'viewAll' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * Warning, do no forget to decrease the num topics of the forum
	 * @Method(GET)
	 * @Nonce(deleteTopic)
	 * @GetAttributes({topicId, forumId})
	 */
	public function deleteAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		
		$time = $topic->getTime();
		// time is a DateTime
		$time = $time->getTimestamp();
		
		if( 0 === $topic->getIsDeleted() ) {
			// the topic was already deleted 
			$this->frontController->addMessage( __( 'Deletion operation failed, the topic is already deleted' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$topic->setIsDeleted( 1 );
		
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$this->frontController->addMessage( __( 'Deletion operation failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$numPost = $topic->getNumPosts();
		
		$forumRepo = $this->em->getRepository( 'Forum' );
		$forumRepo->incrementTopicCount( $forumId, -1 );
		$forumRepo->incrementPostCount( $forumId, -$numPost );
		
		$this->frontController->addMessage( __( 'Deletion operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
	}
	
	/**
	 * Warning, do not forget to increase the num topics of the forum
	 * @Method(GET)
	 * @GetAttributes({topicId, forumId})
	 * @Nonce(undeleteTopic)
	 */
	public function undeleteAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		
		$time = $topic->getTime();
		// time is a DateTime
		$time = $time->getTimestamp();
		
		if( 1 === $topic->getIsDeleted() ) {
			// the topic was already deleted 
			$this->frontController->addMessage( __( 'Undeletion operation failed, the topic was not deleted' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$topic->setIsDeleted( 0 );
		
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$this->frontController->addMessage( __( 'Undeletion operation failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$numPost = $topic->getNumPosts();
		
		$forumRepo = $this->em->getRepository( 'Forum' );
		$forumRepo->incrementTopicCount( $forumId, 1 );
		$forumRepo->incrementPostCount( $forumId, $numPost );
		
		$this->frontController->addMessage( __( 'Deletion operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
	}
	
	//	/**
	//	 * @Nonce(moveTopic)
	//	 * Parameters[topicId]
	//	 */
	//	public function moveAction() {
	//
	//	}
	

	/**
	 * @Nonce(stickTopic)
	 * @Method(GET)
	 * @GetAttributes({topicId, forumId})
	 */
	public function stickAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		
		$time = $topic->getTime();
		// time is a DateTime
		$time = $time->getTimestamp();
		
		if( 0 === $topic->getIsSticky() ) {
			// the topic was already deleted 
			$this->frontController->addMessage( __( 'Stick operation failed, the topic is already stuck' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$topic->setIsSticky( 1 );
		
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$this->frontController->addMessage( __( 'Stick operation failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$this->frontController->addMessage( __( 'Stick operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
	
	}
	/**
	 * @Nonce(unstickTopic)
	 * @Method(GET)
	 * @GetAttributes({topicId, forumId})
	 */
	public function unstickAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		
		$time = $topic->getTime();
		// time is a DateTime
		$time = $time->getTimestamp();
		
		if( 1 === $topic->getIsSticky() ) {
			// the topic was already deleted 
			$this->frontController->addMessage( __( 'Unstick operation failed, the topic is not stuck' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$topic->setIsSticky( 0 );
		
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$this->frontController->addMessage( __( 'Unstick operation failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$this->frontController->addMessage( __( 'Unstick operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
	
	}
	/**
	 * @Nonce(closeTopic)
	 * @Method(GET)
	 * @GetAttributes({topicId, forumId})
	 */
	public function closeAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		
		$time = $topic->getTime();
		// time is a DateTime
		$time = $time->getTimestamp();
		
		if( 1 === $topic->getIsOpen() ) {
			// the topic was already deleted 
			$this->frontController->addMessage( __( 'Close operation failed, the topic is already closed' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$topic->setIsOpen( 0 );
		
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$this->frontController->addMessage( __( 'Close operation failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$this->frontController->addMessage( __( 'Close operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
	}
	/**
	 * @Nonce(openTopic)
	 * @Method(GET)
	 * @GetAttributes({topicId, forumId})
	 */
	public function openAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		
		$time = $topic->getTime();
		// time is a DateTime
		$time = $time->getTimestamp();
		
		if( 0 === $topic->getIsOpen() ) {
			// the topic was already deleted 
			$this->frontController->addMessage( __( 'Open operation failed, the topic is already opened' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$topic->setIsOpen( 1 );
		
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$this->frontController->addMessage( __( 'Open operation failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$this->frontController->addMessage( __( 'Open operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumId, 'timeTarget' => $time ) );
	}

	//	public function viewAllAction($parameters) {
//		$forums = $this->em->getRepository( 'Forum' )->findAll();
//		$this->frontController->getResponse()->setVar( 'forums', $forums );
//		$this->frontController->doDisplay( 'Forum', 'viewAll' );
//	}


}