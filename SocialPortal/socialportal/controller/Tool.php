<?php

namespace socialportal\controller;
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
			$result &= $metaRep->setAcceptableTopics( $forumSupport->getId(), array( TopicType::$typeFreeText ) );
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
