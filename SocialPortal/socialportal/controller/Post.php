<?php

namespace socialportal\controller;
use socialportal\common\topic\TypeCenter;

use core\user\UserHelper;

use core\user\UserRoles;

use socialportal\repository\ForumMetaRepository;

use core\user\UserManager;

use core\debug\Logger;

use socialportal\model\PostBase as PostEntity;

use core\form\Form;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use socialportal\model;

use core\AbstractController;

class Post extends AbstractController {
	/**
	 * @Nonce(displayPostForm)
	 * @GetAttributes({typeId, forumId, topicId})
	 * [postId(opt for edit)]
	 */
	public function displayFormAction() {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		$postId = $get->get( 'postId', false );
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$typeManager = TypeCenter::getTypeManager($typeId);
		$form = $typeManager->getPostForm($this->frontController);
		
		if( !$form ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $typeId ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
		}
		
		$module = '';
		$nameAction = '';
		$response = $this->frontController->getResponse();
		
		$getArgs = array( 'typeId' => $typeId, 'forumId' => $forumId, 'topicId' => $topicId );
		// now the form is valid we check if we can already fill it with previous value (from db)
		if( false !== $postId ) {
			// we edit a post, cause we receive a post id
			$postRepo = $this->em->getRepository( 'PostBase' );
			$currentPost = $postRepo->findFullPost( $postId );
			
			// check if the class correspond to what is attempted !
			$customPostClass = $typeManager->getPostClassName();
			if( !$currentPost instanceof $customPostClass ) {
				$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
				$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId ) );
			}
			
			$form->setupWithPost( $currentPost );
			$form->setNonceAction( 'editPost' );
			$module = 'edit';
			$getArgs['postId'] = $postId;
			$form->setCss( 'post_form post_edit', 'post_form.css' );
			$nameAction = 'displayFormEdit';
		} else {
			// we create a post
			$user = $this->frontController->getCurrentUser();
			$userHelper = new UserHelper( $this->frontController );
			
			$response->setVar( 'userHelper', $userHelper );
			$response->setVar( 'user', $user );
			
			$module = 'create';
			$form->setCss( 'post_form post_create', 'post_form.css' );
			$form->setNonceAction( 'createPost' );
			$nameAction = 'displayFormCreate';
		}
		
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Post', $module, $getArgs );
		
		// fill the form with the posted field and errors
		$form->setupWithArray();
		$form->setTargetUrl( $actionUrl );
		
		$response->setVar( 'form', $form );
		$response->setVar( 'topicId', $topicId );
		$response->setVar( 'forumId', $forumId );
		$this->frontController->doDisplay( 'Post', $nameAction );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createPost)
	 * @GetAttributes({typeId, forumId, topicId})
	 */
	public function createAction() {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$typeManager = TypeCenter::getTypeManager($typeId);
		$form = $typeManager->getPostForm($this->frontController);

		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$topic = $this->em->find( 'TopicBase', $topicId );
		
		$now = $this->frontController->getRequest()->getRequestDateTime();

		$base = new PostEntity();
		$base->setCustomType( $typeId );
		$base->setTopic( $topic );
		$base->setIsDeleted( 0 );
		$base->setPoster( $this->frontController->getCurrentUser() );
		$base->setPosterIp( $this->frontController->getRequest()->getClientIp() );
		$base->setTime( $now );
		$position = $this->em->getRepository( 'PostBase' )->getLastPosition( $topicId );
		$base->setPosition( $position );
		
		// update of the topic
		$topic->setTime( $now );
		$topic->setLastPoster( $this->frontController->getCurrentUser() );
		
		$post = $form->createSpecificPost( $base );
		$this->em->persist( $base );
		$this->em->persist( $post );
		$this->em->persist( $topic );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'There was a problem during the creation of the post, try with an other title or in a moment' ), 'error' );
			//TODO problem here the referrer needs authentification that we don't have
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl( $referrer );
		}
		// increment the number of topic in the forum parent
		$this->em->getRepository( 'Forum' )->incrementPostCount( $forumId );
		$this->em->getRepository( 'TopicBase' )->incrementPostCount( $topicId );
		
		$this->frontController->addMessage( __( 'The creation of the post was a success' ), 'correct' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'lastPage' => true ) );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editPost)
	 * @GetAttributes({typeId, forumId, topicId, postId})
	 */
	public function editAction() {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get( 'typeId' );
		$postId = $get->get( 'postId' );
		$forumId = $get->get( 'forumId' );
		$topicId = $get->get( 'topicId' );
		
		$typeManager = TypeCenter::getTypeManager($typeId);
		$form = $typeManager->getPostForm($this->frontController);

		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$postRepo = $this->em->getRepository( 'PostBase' );
		$existing = $postRepo->findFullPost( $postId );
		if( !$existing ) {
			// TODO redirection
			$this->frontController->addMessage( __( 'The given id for the edition was invalid' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		// check if the forum accept the custom type proposed 
		$customPostClass = $typeManager->getPostClassName();
		if( !$existing instanceof $customPostClass ) {
			// TODO redirection
			$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		$base = $existing->getPostbase();
		$position = $base->getPosition();
		$existing = $form->createSpecificPost( $base, $existing );
		
		//		$this->em->persist( $base );
		$this->em->persist( $existing );
		if( !$this->em->flushSafe() ) {
			//TODO redirection
			$this->frontController->addMessage( __( 'There was a problem during the edition of the post' ), 'error' );
			//TODO problem here the referrer needs authorization that we don't have
			//			$referrer = $this->frontController->getRequest()->getReferrer();
			//			$this->frontController->doRedirectUrl( $referrer );
			// that needs authorization
			$this->frontController->doRedirect( 'home' );
		}
		
		$this->frontController->addMessage( __( 'The edition of the topic was a success' ), 'correct' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'positionTarget' => $position ), "post-$postId" );
	}
	
	/**
	 * Wargning, do no forget to decrease the num post of the forum/topic
	 * @Nonce(deletePost)
	 * @GetAttributes({postId, topicId, forumId})
	 */
	public function deleteAction() {
		$get = $this->frontController->getRequest()->query;
		$postId = $get->get( 'postId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$postRepo = $this->em->getRepository( 'PostBase' );
		
		$post = $postRepo->find( $postId );
		
		if( 0 === $post->getIsDeleted() ) {
			// the topic was already deleted 
			$position = $post->getPosition();
			// redirection with position, cause the post is still there
			$this->frontController->addMessage( __( 'Deletion failed, the post was not deleted' ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'positionTarget' => $position ) );
		}
		// main operation
		$post->setIsDeleted( 1 );
		
		$this->em->persist( $post );
		if( !$this->em->flushSafe() ) {
			// operation fail
			$position = $post->getPosition();
			// redirection with position, cause the post is still there
			$this->frontController->addMessage( __( 'Deletion failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'positionTarget' => $position ) );
		}
		
		$forumRepo = $this->em->getRepository( 'Forum' );
		$forumRepo->incrementPostCount( $forumId, -1 );
		
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topicRepo->incrementPostCount( $topicId, -1 );
		
		// redirection with time, cause the post is no more visible, with time we reach the approximative point where it was
		$time = $post->getTime();
		$time = $time->getTimestamp();
		
		$this->frontController->addMessage( __( 'Deletion success' ), 'correct' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'timeTarget' => $time ) );
	}
	
	/**
	 * Wargning, do no forget to increase the num post of the forum/topic
	 * @Nonce(undeletePost)
	 * @GetAttributes({postId, topicId, forumId})
	 */
	public function undeleteAction() {
		$get = $this->frontController->getRequest()->query;
		$postId = $get->get( 'postId' );
		$topicId = $get->get( 'topicId' );
		$forumId = $get->get( 'forumId' );
		
		$postRepo = $this->em->getRepository( 'PostBase' );
		
		$post = $postRepo->find( $postId );
		
		if( 1 === $post->getIsDeleted() ) {
			// the topic was already deleted 
			// redirection with time, cause the post is no more visible, with time we reach the approximative point where it was
			$time = $post->getTime();
			$time = $time->getTimestamp();
			$this->frontController->addMessage( __( 'Undeletion failed, the post was deleted' ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		// main operation
		$post->setIsDeleted( 0 );
		
		$this->em->persist( $post );
		if( !$this->em->flushSafe() ) {
			// operation fail
			// redirection with time, cause the post is no more visible, with time we reach the approximative point where it was
			$time = $post->getTime();
			$time = $time->getTimestamp();
			$this->frontController->addMessage( __( 'Undeletion failed, please re try in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'timeTarget' => $time ) );
		}
		
		$forumRepo = $this->em->getRepository( 'Forum' );
		$forumRepo->incrementPostCount( $forumId, 1 );
		
		$topicRepo = $this->em->getRepository( 'TopicBase' );
		$topicRepo->incrementPostCount( $topicId, 1 );
		
		// redirection with position, cause the post is still there
		$position = $post->getPosition();
		
		$this->frontController->addMessage( __( 'Undeletion success' ), 'correct' );
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array( 'topicId' => $topicId, 'forumId' => $forumId, 'positionTarget' => $position ) );
	}
}