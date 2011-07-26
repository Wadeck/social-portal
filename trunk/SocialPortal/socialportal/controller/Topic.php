<?php

namespace socialportal\controller;

use socialportal\common\templates\NoFilterLinkTemplate;

use core\Config;

use socialportal\repository\InstructionRepository;

use socialportal\common\templates\InstructionTemplate;

use socialportal\common\topic\TypeCenter;

use socialportal\common\templates\ModuleInsertTemplate;

use socialportal\common\templates\MessageInsertTemplate;

use core\user\UserRoles;

use socialportal\common\templates\Paginator;

use socialportal\repository\ForumMetaRepository;

use core\user\UserManager;

use core\debug\Logger;

use socialportal\model\TopicBase as TopicEntity;

use socialportal\common\form\custom\ForumForm;

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
		$forumRepo = $this->em->getRepository( 'Forum' );
		$forums = $forumRepo->findAll();
		$defaultForumId = $forumRepo->getFirstId();
		$topicsFor = array();
		if( $forums ) {
			$metaRepo = $this->em->getRepository( 'ForumMeta' );
			foreach( $forums as $f ) {
				$acceptedTopics = $metaRepo->getAcceptableTopics( $f->getId() );
				array_walk( $acceptedTopics, function (&$item, $key) {
					$item = TypeCenter::getTypeManager( $item );
				} );
				
				$topicsFor[$f->getId()] = $acceptedTopics;
			}
		}
		
		$response = $this->frontController->getResponse();
		$response->setVar( 'defaultForumId', $defaultForumId );
		$response->setVar( 'forums', $forums );
		$response->setVar( 'topicsFor', $topicsFor );
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
			$item = TypeCenter::getTypeManager( $item );
		} );
		
		$this->frontController->getResponse()->setVar( 'forum', $forum );
		$this->frontController->getResponse()->setVar( 'acceptedTopics', $acceptedTopics );
		$this->frontController->doDisplay( 'Topic', 'chooseTypeForum' );
	}
	
	/**
	 * @GetAttributes({topicId, forumId})
	 * [p, n, positionTarget(int), timeTarget(timestamp int), postIdTarget(int), lastPage(boolean), withDeleted(boolean), nofilter(boolean)]
	 */
	public function displaySingleTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$page_num = $get->get( 'p', 1 );
		$num_per_page = $get->get( 'n', 10 ); 
		$positionTarget = $get->get( 'positionTarget', false );
		$timeTarget = $get->get( 'timeTarget', false );
		$postIdTarget = $get->get( 'postIdTarget', false );
		$lastPage = $get->get( 'lastPage', false );
		$withDeleted = $get->get( 'withDeleted', false );
		$nofilter = $get->get( 'nofilter', false );
		
		$postBaseRepo = $this->em->getRepository( 'PostBase' );
		$topicBaseRepo = $this->em->getRepository( 'TopicBase' );
		
		$topic = $topicBaseRepo->findFullTopic( $topicId );
		if(false === $topic){
			$this->frontController->addMessage( __('No topic related to your request') , 'error' );
			$this->frontController->doRedirect( 'Home' );
		}
		$baseTopic = $topic->getTopicbase();
		$typeId = $baseTopic->getCustomType();
		
		if( $withDeleted ) {
			$num_posts = $topicBaseRepo->getCountWithDeleted( $topicId );
		} else {
			$num_posts = $baseTopic->getNumPosts();
		}
		$max_pages = ceil( $num_posts / $num_per_page );
		if( !$max_pages ) {
			$max_pages = 0;
		}
		
		if( false !== $positionTarget ) {
			// we want to go to a specific topic
			$page_num = $topicBaseRepo->getPostPagePerPosition( $topicId, $typeId, $positionTarget, $num_per_page, $withDeleted );
		} else if( false !== $timeTarget ) {
			// we want to go to a specific topic
			$timeTarget = new DateTime( "@$timeTarget", $this->frontController->getDateTimeZone() );
			$page_num = $topicBaseRepo->getPostPagePerTime( $topicId, $typeId, $timeTarget, $num_per_page, $withDeleted );
		} else if( false !== $lastPage ) {
			// we want to go to the last page
			//			$page_num = $topicBaseRepo->getLastPage( $topicId, $typeId, $num_per_page );
			$page_num = max( 1, $max_pages );
		} else if( false !== $postIdTarget ) {
			$post = $this->em->find('PostBase', $postIdTarget);
			if(!$post){
				$position = -1;
			}else{
				$position = $post->getPosition();
			}
			$page_num = $topicBaseRepo->getPostPagePerPosition( $topicId, $typeId, $position, $num_per_page, $withDeleted );
		}
		
		$limit = Config::get('num_posts_limit_filter', 200);
		if( !$nofilter && -1 !== $limit && !$withDeleted && $page_num === 1 && $num_posts >= $limit ){
			$numPostFilter = Config::get('num_posts_limit_display', 10);
			$postVoteStatsRepo = $this->em->getRepository('PostVoteStats');
			$posts = $postVoteStatsRepo->findBestPosts($topicId, $typeId, $numPostFilter);
			$withVoteFilter = true;
		}else{
			$posts = array();
		}
		// if the posts are not retrieve for some reason, like an error or no posts were voted
		if( empty($posts) ){
			$posts = $postBaseRepo->findAllFullPosts( $topicId, $typeId, $page_num, $num_per_page, $withDeleted );
			$withVoteFilter = false;
		}
		
		$getArgs = array( 'topicId' => $topicId, 'forumId' => $forumId, 'p' => "%#p%", 'n' => "%#n%", 'nofilter' => true );
		if( $withDeleted ) {
			$getArgs['withDeleted'] = true;
		}
		
		if( !$withVoteFilter ){
			// with placeholders for p/n
			$link = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', $getArgs );
			// only when we don't use filter, we create the paginator
			$navigation = new Paginator();
			$navigation->paginate( $this->frontController, $page_num, $max_pages, $num_per_page, $link, __( 'First' ), __( 'Last' ), __( 'Previous' ), __( 'Next' ), false, false );
		}else{
			// in case of filtering, we don't use paginator but a link to this function without filtering
			$noFilterLink = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', array('forumId' => $forumId, 'topicId' => $topicId, 'nofilter' => true ) );
			$navigation = new NoFilterLinkTemplate( $this->frontController, $noFilterLink );
		}
		
		// condition to satisfy to be able to write a comment
		if( !$baseTopic->getIsOpen() ){
			$commentForm = new MessageInsertTemplate( $this->frontController, __( 'The topic is closed, no more comment accepted' ), array('topic-no-respond'), array('rounded-box', 'pagged') );
		}else if( !$this->frontController->getViewHelper()->currentUserIsAtLeast( UserRoles::$full_user_role ) ) {
			$commentForm = new MessageInsertTemplate( $this->frontController, __( 'You do not have the right to add comment' ), array('topic-no-respond'), array('rounded-box', 'pagged') );
		} else {
			$commentForm = new ModuleInsertTemplate( $this->frontController, 'Post', 'displayForm', array( 'typeId' => $typeId, 'topicId' => $topicId, 'forumId' => $forumId ), 'displayPostForm' );
		}
		
		$getArgs = array('topicId' => $topicId, 'forumId' => $forumId);
		$permalinkTopic = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', $getArgs );
		$getArgs['n'] = $num_per_page;
		$getArgs['p'] = $page_num;
		$permalinkPost = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', $getArgs );
		
		$typeManager = TypeCenter::getTypeManager( $typeId );
		
		$response = $this->frontController->getResponse();
		$response->setVar( 'commentForm', $commentForm );
		$response->setVar( 'pagination', $navigation );
		$response->setVar( 'posts', $posts );
		$response->setVar( 'topic', $topic );
		$response->setVar( 'forumId', $forumId );
		$response->setVar( 'topicTemplate', $typeManager->getTopicTemplate($this->frontController, $this->em, $topic, $permalinkTopic ) );
		$response->setVar( 'postsTemplate', $typeManager->getPostTemplate($this->frontController, $this->em, $baseTopic, $posts, $permalinkPost ) );
		
		if( $this->frontController->getViewHelper()->currentUserIsAtLeast( UserRoles::$moderator_role ) ) {
			// optimization if we use permalink before
			if(!$withDeleted){
				$getArgs['withDeleted'] = true;
				$response->setVar('isDisplayDeleted', true);
				$displayDeletedLink = $this->frontController->getViewHelper()->createHref( 'Topic', 'displaySingleTopic', $getArgs );
			}else{
				$response->setVar('isDisplayDeleted', false);
				// already computed
				$displayDeletedLink = $permalinkPost;
			}
		} else {
			$displayDeletedLink = false;
		}
		$response->setVar( 'displayDeletedLink', $displayDeletedLink );
		
		$this->frontController->doDisplay( 'topic', 'displaySingleTopic' );
	}
	
	/**
	 * @Nonce(displayForm)
	 * @GetAttributes({typeId, forumId})
	 * [topic_id(opt, only for edit)]
	 */
	public function displayFormAction() {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$forumId = $get->get( 'forumId' );
		$topicId = $get->get( 'topicId', false );
		
		// check if the forum accept the custom type proposed 
		$forumMeta = $this->em->getRepository( 'ForumMeta' );
		if( !$forumMeta->isAcceptedBy( $forumId, $typeId ) ) {
			$this->frontController->addMessage( __( 'This forum does not accept the type of topic you passed' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$typeManager = TypeCenter::getTypeManager( $typeId );
		if( null === $typeManager ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $typeId ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'chooseType' );
		}
		$form = $typeManager->getTopicForm( $this->frontController );
		$module = '';
		
		$getArgs = array( 'typeId' => $typeId, 'forumId' => $forumId );
		
		// now the form is valid we check if we can already fill it with previous value (from db)
		if( false !== $topicId ) {
			$topicRepo = $this->em->getRepository( 'TopicBase' );
			$currentTopic = $topicRepo->findFullTopic( $topicId );
			
			// check if the class correspond to what is attempted !
			$customTopicClass = $typeManager->getTopicClassName();
			if( !$currentTopic instanceof $customTopicClass ) {
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
		$form->setupWithArray();
		$form->setTargetUrl( $actionUrl );
		
		$instrRepo = $this->em->getRepository('Instruction');
		$name = $typeManager->getSimpleName();
		$name = strtolower($name);
		$instruction = $instrRepo->getInstruction($instrRepo::$prefixTopicType, $name);
		$cookies = $this->frontController->getRequest()->cookies;
		$cookieName = 'instruction_' . $name;
		$visible = $cookies->get($cookieName, 'true');
		if( 'true' === $visible ){
			$visible = true;
		}else{
			$visible = false;
		}
		$template = new InstructionTemplate($this->frontController, $instruction, $visible, $cookieName);
		
		$response = $this->frontController->getResponse();
		$response->setVar( 'topicId', $topicId );
		$response->setVar( 'forumId', $forumId );
		$response->setVar( 'form', $form );
		$response->setVar( 'instruction', $template );
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
		
		$typeManager = TypeCenter::getTypeManager($typeId);
		if( null === $typeManager ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $typeId ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'chooseType' );
		}
		$form = $typeManager->getTopicForm( $this->frontController );
		$form->setupWithArray();
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
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$base->setStartTime( $now );
		$base->setTime( $now );
		$base->setTagCount( 0 );
		$base->setTitle( $form->getTopicTitle() );
		$topic = $form->createSpecificTopic( $base );
		$this->em->persist( $base );
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'There was a problem during the creation of the topic, try with an other title or in a moment' ), 'error' );
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		
		if($form->hasSecondAction()){
			if(!$form->doSecondAction($topic)){
				$this->frontController->addMessage( __( 'There was a problem during the creation of the topic, try with an other title or in a moment' ), 'error' );
				$referrer = $this->frontController->getRequest()->getReferrer();
				$this->frontController->doRedirectUrl( $referrer );
			}
		}
		$topicId = $base->getId();
		// increment the number of topic in the forum parent
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId );
		
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
		
		$typeManager = TypeCenter::getTypeManager($typeId);
		if( null === $typeManager ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $typeId ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topidId' => $topicId, 'forumId' => $forumId ) );
		}
		$form = $typeManager->getTopicForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$existing = $topicRepo->findFullTopic( $topicId );
		if( !$existing ) {
			$this->frontController->addMessage( __( 'The given id for the edition was invalid' ), 'error' );
			$this->frontController->doRedirect( 'topic', 'displayForm' );
		}
		
		// check if the forum accept the custom type proposed 
		$customTopicClass = $typeManager->getTopicClassName();
		if( !$existing instanceof $customTopicClass ) {
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
		if($form->hasSecondAction()){
			if(!$form->doSecondAction($existing)){
				$this->frontController->addMessage( __( 'There was a problem during the creation of the topic, try with an other title or in a moment' ), 'error' );
				$referrer = $this->frontController->getRequest()->getReferrer();
				$this->frontController->doRedirectUrl( $referrer );
			}
		}
		
		$this->frontController->addMessage( __( 'The edition of the topic was a success' ), 'correct' );
		//		$this->frontController->doRedirect( 'Forum', 'viewAll' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
	}
	
	/**
	 * Warning, do no forget to decrease the num topics of the forum
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
	
	/**
	 * @Nonce(move)
	 * @GetAttributes({topicId, forumIdFrom, forumIdTo})
	 * @RoleAtLeast(moderator)
	 */
	public function moveAction() {
		$get = $this->frontController->getRequest()->query;
		$topicId = $get->get( 'topicId' );
		$forumIdFrom = $get->get( 'forumIdFrom' );
		$forumIdTo = $get->get( 'forumIdTo' );
		
		if( $forumIdTo === $forumIdFrom ){
			$this->frontController->addMessage( __( 'You cannot move a topic to the same forum' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumIdFrom ) );
		}
		
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topic = $topicRepo->find( $topicId );
		$typeId = $topic->getCustomType();
		
		$forumMetaRepo = $this->em->getRepository( 'ForumMeta' );
		$forumToAcceptables = $forumMetaRepo->getAcceptableTopics($forumIdTo);
		
		if( !in_array($typeId, $forumToAcceptables) ){
			Logger::getInstance()->log_var('Attempt to move a topic to a forum that does not accept it', array('topic' => $topic, 'forumAcceptables' => $forumToAcceptables));
			$this->frontController->addMessage( __( 'Move operation failed' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumIdFrom ) );
		}
		
		// move the topic to the other forum
		$topic->setForum($this->em->getReference('Forum', $forumIdTo) );
		$this->em->persist($topic);
		if( !$this->em->flushSafe() ){
			$this->frontController->addMessage( __( 'Error in persisting the entity' ), 'error' );
			$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumIdFrom ) );
		}
		
		// if success decrease the number of topic of forumFrom, increase of ForumTo
		if( !$topic->getIsDeleted() ){
			$numPost = $topic->getNumPosts();
			
			$forumRepo = $this->em->getRepository( 'Forum' );
			$forumRepo->incrementTopicCount( $forumIdFrom, -1 );
			$forumRepo->incrementPostCount( $forumIdFrom, -$numPost );
			$forumRepo->incrementTopicCount( $forumIdTo, 1 );
			$forumRepo->incrementPostCount( $forumIdTo, $numPost );
		}
		
		$time = $topic->getTime()->getTimestamp();
		
		$this->frontController->addMessage( __( 'Move operation success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'displaySingleForum', array( 'forumId' => $forumIdTo, 'timeTarget' => $time ) );
	}

}