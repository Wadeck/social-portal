<?php

namespace socialportal\controller;
use core\form\custom\PostFormFactory;

use core\topics\templates\MessageInsertTemplate;

use core\user\UserRoles;

use core\tools\Paginator;

use socialportal\repository\ForumMetaRepository;

use core\user\UserManager;

use core\topics\TopicType;

use core\debug\Logger;

use socialportal\model\PostBase as PostEntity;

use core\form\custom\ForumForm;

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
	public function displayFormAction($parameters) {
		$get = $this->frontController->getRequest()->query;
		$topicType = $get->get('typeId');
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');
		$postId = $get->get('postId', false);
		
		$user = $this->frontController->getCurrentUser();
		
		// retrieve information and then pass to the form
		// if existing information, we put as action "edit" instead of create
		$form = PostFormFactory::createForm( $topicType, $this->frontController );
		if( !$form ) {
			$this->frontController->addMessage( __( 'Invalid type of topic, (%type%) is unknown', array( '%type%' => $topicType ) ), 'error' );
			$this->frontController->doRedirect( 'Topic', 'displaySingleTopic',array(), array('topicId'=>$topicId, 'forumId'=>$forumId) );
		}
		$module = '';
		
		$getArgs = array( 'typeId' => $topicType );
		// now the form is valid we check if we can already fill it with previous value (from db)
		if( false !== $postId ) {
			$postRepo = $this->em->getRepository( 'PostBase' );
			$currentPost = $postRepo->findFullPost( $postId );
			
			// check if the class correspond to what is attempted !
			$customClass = TopicType::translateTypeIdToPostName( $topicType );
			if( !$currentPost instanceof $customClass ) {
				$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
				$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array(), array('topicId'=>$topicId, 'forumId'=>$forumId) );
			}
			
			$form->setupWithPost( $currentPost );
			$form->setNonceAction( 'editPost' );
			$module = 'edit';
			$getArgs['postId'] = $postId;
		} else {
			$form->setNonceAction( 'createPost' );
			$module = 'create';
			$getArgs['forumId'] = $forumId;
			$getArgs['topicId'] = $topicId;
		}
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Post', $module, array(), $getArgs );
		
		// fill the form with the posted field and errors
		$form->setupWithArray( true );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'user', $user );
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'post', 'displayForm' );
	}
	
	
	/**
	 * @Method(POST)
	 * @Nonce(createPost)
	 * @GetAttributes({typeId, forumId, topicId})
	 */
	public function createAction($parameters) {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get('typeId');
		$topicId = $get->get('topicId');
		$forumId = $get->get('forumId');
		
		$form = PostFormFactory::createForm( $typeId, $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$base = new PostEntity();
		$base->setCustomType( $typeId );
		$base->setTopic( $this->em->getReference( 'TopicBase', $topicId ) );
		$base->setIsDeleted( 0 );
		$base->setPoster( $this->frontController->getCurrentUser() );
		$base->setPosterIp($this->frontController->getRequest()->getClientIp());
		$now = new \DateTime( '@' . $this->frontController->getRequest()->getRequestTime() );
		$base->setTime( $now );
		$position = $this->em->getRepository('PostBase')->getLastPosition($topicId);
		$base->setPosition($position);
		
		$post = $form->createSpecificPost( $base );
		$this->em->persist( $base );
		$this->em->persist( $post );
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
		$this->frontController->doRedirect( 'Topic', 'displaySingleTopic', array(), array('topicId'=>$topicId, 'forumId'=>$forumId) );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editPost)
	 * @GetAttributes({typeId, postId})
	 */
	public function editAction($parameters) {
		$get = $this->frontController->getRequest()->query;
		$typeId = $get->get('typeId');
		$postId = $get->get('postId');
		
		$form = PostFormFactory::createForm( $typeId, $this->frontController );
		$form->setupWithArray( true );
		$form->checkAndPrepareContent();
		
		$postRepo = $this->em->getRepository( 'PostBase' );
		$existing = $postRepo->findFullPost( $postId );
		if( !$existing ) {
			// TODO redirection
			$this->frontController->addMessage( __( 'The given id for the edition was invalid' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		// check if the forum accept the custom type proposed 
		$customClass = TopicType::translateTypeIdToPostName( $typeId );
		if( !$existing instanceof $customClass ) {
			// TODO redirection
			$this->frontController->addMessage( __( 'The given id does not correspond to the correct topic type' ), 'error' );
			$this->frontController->doRedirect( 'home' );
		}
		
		$base = $existing->getPostbase();
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
			$this->frontController->doRedirect('home');
		}
		
		$this->frontController->addMessage( __( 'The edition of the topic was a success' ), 'correct' );
		$this->frontController->doRedirect( 'Forum', 'viewAll' );
	
	}
	
	/**
	 * Wargning, do no forget to decrease the num topics of the forum
	 * @Nonce(deleteTopic)
	 * @Parameters(1)
	 * Parameters[postId]
	 */
	public function deleteAction($parameters) {}
	
	/**
	 * Warning, do not forget to increase the num topics of the forum
	 * @Nonce(undeleteTopic)
	 * @Parameters(1)
	 * Parameters[postId]
	 */
	public function undeleteAction($parameters) {}
}