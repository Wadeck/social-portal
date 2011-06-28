<?php

namespace socialportal\controller;
use socialportal\model\PostFreetext;

use socialportal\model\PostBase;

use socialportal\model\TopicFreetext;

use socialportal\model\TopicBase;

use core\user\UserRoles;

use core\tools\TopicType;

use core\user\UserManager;

use socialportal\model\User;

use socialportal\model\Forum;

use core\security\Crypto;

use core\FrontController;

use core\AbstractController;

/**
 * Simple controller to make some administrative task like create password etc
 *
 */
class Tool extends AbstractController {
	public function indexAction() {
		$refl = new \ReflectionClass( $this );
		$currentClass = $refl->getShortName();
		$parentClass = $refl->getParentClass();
		$links = array();
		$methods = $refl->getMethods();
		$parentMethods = $parentClass->getMethods();
		array_walk( $parentMethods, function (&$item, $key) {
			$item = $item->getName();
		} );
		foreach( $methods as $m ) {
			$name = $m->getName();
			if( in_array( $name, $parentMethods ) ) {
				continue;
			}
			$name = str_replace( 'Action', '', $name );
			$url = $this->frontController->getViewHelper()->createHref( $currentClass, $name );
			$links[$name] = $url;
		}
		$links['Back to home'] = $this->frontController->getViewHelper()->createHref( 'home' );
		$this->frontController->getResponse()->setVar( 'links', $links );
		$this->frontController->doDisplay( 'tool', 'displayAllTools' );
	}
	
	public function generateErrorMessageAction() {
		$this->frontController->addMessage( 'Test error', 'error' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	public function generateCorrectMessageAction() {
		$this->frontController->addMessage( 'Test correct', 'correct' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	public function generateInfoMessageAction() {
		$this->frontController->addMessage( 'Test info' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @_GetAttributes({key, password})
	 */
	public function directCreatePasswordAction() {
		$get = $this->frontController->getRequest()->query;
		$randomkey = $get->get( 'key', null );
		$password = $get->get( 'password', null );
		if( $randomkey && $password ) {
			$encoded = Crypto::encodeDBPassword( $randomkey, $password );
		} else {
			$encoded = null;
		}
		$this->frontController->getResponse()->setVar( 'encoded', $encoded );
		$this->frontController->doDisplay( 'tool', 'displayPassword' );
	}
	
	/**
	 * Could be long as we want, cause it is called only once per installation
	 */
	public function createBaseForumAction() {
		$forumDiscussion = new Forum();
		$forumDiscussion->setName( 'Discussion' );
		$forumDiscussion->setDescription( 'Place where people can speak with others freely' );
		$forumDiscussion->setPosition( 1 );
		$forumDiscussion->setNumPosts( 0 );
		$forumDiscussion->setNumTopics( 0 );
		$this->em->persist( $forumDiscussion );
		
		$forumStories = new Forum();
		$forumStories->setName( 'Stories' );
		$forumStories->setDescription( 'Place where people narrate stories and explain their thoughts' );
		$forumStories->setPosition( 2 );
		$forumStories->setNumPosts( 0 );
		$forumStories->setNumTopics( 0 );
		$this->em->persist( $forumStories );
		
		$forumStrategies = new Forum();
		$forumStrategies->setName( 'Strategies' );
		$forumStrategies->setDescription( 'Place where people exchange strategies under list format' );
		$forumStrategies->setPosition( 3 );
		$forumStrategies->setNumPosts( 0 );
		$forumStrategies->setNumTopics( 0 );
		$this->em->persist( $forumStrategies );
		
		$forumActivities = new Forum();
		$forumActivities->setName( 'Activities' );
		$forumActivities->setDescription( 'Place where people exchange activities and can gather some ideas' );
		$forumActivities->setPosition( 4 );
		$forumActivities->setNumPosts( 0 );
		$forumActivities->setNumTopics( 0 );
		$this->em->persist( $forumActivities );
		
		$forumSupport = new Forum();
		$forumSupport->setName( 'Support' );
		$forumSupport->setDescription( 'Place where people can ask for help from others' );
		$forumSupport->setPosition( 10 );
		$forumSupport->setNumPosts( 0 );
		$forumSupport->setNumTopics( 0 );
		$this->em->persist( $forumSupport );
		
		if( $this->em->flushSafe() ) {
			$metaRep = $this->em->getRepository( 'ForumMeta' );
			$result = true;
			$result &= $metaRep->setAcceptableTopics( $forumDiscussion->getId(), array( TopicType::$typeFreetext ) );
			$result &= $metaRep->setAcceptableTopics( $forumStories->getId(), array( TopicType::$typeStory ) );
			$result &= $metaRep->setAcceptableTopics( $forumStrategies->getId(), array( TopicType::$typeStrategy ) );
			$result &= $metaRep->setAcceptableTopics( $forumActivities->getId(), array( TopicType::$typeActivity ) );
			$result &= $metaRep->setAcceptableTopics( $forumSupport->getId(), array( TopicType::$typeFreetext ) );
			if( $result ) {
				$this->frontController->addMessage( __( 'Creation of the base forum complete with metadata' ), 'correct' );
			} else {
				$this->frontController->addMessage( __( 'The metadata were not added/modified' ), 'info' );
			}
		} else {
			$this->frontController->addMessage( __( 'Creation of the base forum failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	public function createBaseUserAction() {
		$anonUser = UserManager::getAnonymousUser();
		$nullUser = UserManager::getNullUser();
		$admin = UserManager::createUser( 'admin', 'admin', 'w.follonier@netunion.com', UserRoles::$admin_role, 0 );
		$this->em->persist( $nullUser );
		$this->em->persist( $anonUser );
		$this->em->persist( $admin );
		if( $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Creation of the default users complete' ), 'correct' );
		} else {
			$this->frontController->addMessage( __( 'Creation of the default users failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	public function createFloodFirstForumAction() {
		$get = $this->frontController->getRequest()->query;
		$numFlood = $get->get( 'num', 10 );
		
		$forumId = $this->em->getRepository( 'Forum' )->getFirstId();
		$typeId = $this->em->getRepository('ForumMeta')->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		
		for( $i = 1; $i <= $numFlood; $i++ ) {
			$base = new TopicBase();
			$topic = new TopicFreetext();
			
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
			$now = new \DateTime( '@' . ($this->frontController->getRequest()->getRequestTime()+$i) );
			$base->setStartTime( $now );
			$base->setTime( $now );
			$base->setTagCount( 0 );
			$base->setTitle( "flooooooooooood($i / $numFlood)" );
			
			$topic->setTopicbase( $base );
			$topic->setContent( 'floooooooooo oooooooooo ooooooooooo ooooooooooo ooooooooood' );
			
			$this->em->persist( $base );
			$this->em->persist( $topic );
		
		}
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( 'Forum flood failed', 'error' );
			$this->frontController->doRedirect( 'tool', 'index' );
		}
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId, $numFlood );
		$this->frontController->addMessage( "Forum flood success: $numFlood topics created", 'correct' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	public function createFloodFirstTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$numFlood = $get->get( 'num', 1000 );
		
		$forumId = $this->em->getRepository( 'Forum' )->getFirstId();
		$typeId = $this->em->getRepository('ForumMeta')->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		
			$baseTopic = new TopicBase();
			$topic = new TopicFreetext();
			
			$baseTopic->setCustomType( $typeId );
			$baseTopic->setForum( $this->em->getReference( 'Forum', $forumId ) );
			$baseTopic->setIsDeleted( 0 );
			$baseTopic->setIsOpen( 1 );
			$baseTopic->setIsSticky( 0 );
			$baseTopic->setNumPosts( 0 );
			if( !$this->frontController->getCurrentUser()->getId() ) {
				$baseTopic->setPoster( $this->em->getReference( 'User', UserManager::$anonUserId ) );
				$baseTopic->setLastposter( $this->em->getReference( 'User', UserManager::$anonUserId ) );
			} else {
				$baseTopic->setPoster( $this->frontController->getCurrentUser() );
				$baseTopic->setLastposter( $this->frontController->getCurrentUser() );
			}
			$now = new \DateTime( '@' . $this->frontController->getRequest()->getRequestTime() );
			$baseTopic->setStartTime( $now );
			$baseTopic->setTime( $now );
			$baseTopic->setTagCount( 0 );
			$baseTopic->setTitle( "flood receiver" );
			
			$topic->setTopicbase( $baseTopic );
			$topic->setContent( 'floood receiver,.........' );
			
			$this->em->persist( $baseTopic );
			$this->em->persist( $topic );
		
		for( $i = 1; $i <= $numFlood; $i++ ) {
			$basePost = new PostBase();
			$post = new PostFreetext();
			$basePost->setTopic($baseTopic);
			$basePost->setCustomType( $typeId );
			$basePost->setPosition( $i );
			$basePost->setPosterIp( $this->frontController->getRequest()->getClientIp() );
			$basePost->setIsDeleted( 0 );
			if( !$this->frontController->getCurrentUser()->getId() ) {
				$basePost->setPoster( $this->em->getReference( 'User', UserManager::$anonUserId ) );
			} else {
				$basePost->setPoster( $this->frontController->getCurrentUser() );
			}
			$now = new \DateTime( '@' . ($this->frontController->getRequest()->getRequestTime()+$i) );
			$basePost->setTime( $now );
			
			$post->setPostbase( $basePost );
			$post->setContent( "$i ) floooooooooo oooooooooo ooooooooooo ooooooooooo ooooooooood" );
			
			$this->em->persist( $basePost );
			$this->em->persist( $post );
		
		}
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( 'Topic flood failed', 'error' );
			$this->frontController->doRedirect( 'tool', 'index' );
		}
		$topicId = $baseTopic->getId();
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId, 1 );
		$this->em->getRepository( 'Forum' )->incrementPostCount( $forumId, $numFlood );
		$this->em->getRepository( 'TopicBase' )->incrementPostCount( $topicId, $numFlood );
		$this->frontController->addMessage( "Topic flood success: $numFlood post created", 'correct' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	public function recountAllTopicsAction() {
		$forumRep = $this->em->getRepository( 'Forum' );
		$forums = $forumRep->findAll();
		$total = 0;
		foreach( $forums as $f ) {
			$count = $forumRep->recountAllTopics( $f->getId() );
			$total += $count;
			$f->setNumTopics( $count );
			$this->em->persist( $f );
		}
		
		if( $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Recount topics complete total=%total%', array( '%total%' => $total ) ), 'info' );
		} else {
			$this->frontController->addMessage( __( 'Recount failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	public function recountAllPostsAction() {
		$forumRep = $this->em->getRepository( 'Forum' );
		$forums = $forumRep->findAll();
		$total = 0;
		$error = false;
		foreach( $forums as $f ) {
			$count = $forumRep->recountAllPosts( $f->getId() );
			if( false === $count ) {
				$error = true;
				break;
			}
			$total += $count;
			$f->setNumPosts( $count );
			$this->em->persist( $f );
		}
		
		if( !$error && $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Recount posts complete total=%total%', array( '%total%' => $total ) ), 'info' );
		} else {
			$this->frontController->addMessage( __( 'Recount failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
}
