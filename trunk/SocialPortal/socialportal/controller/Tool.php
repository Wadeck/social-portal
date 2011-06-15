<?php

namespace socialportal\controller;
use core\user\UserRoles;

use core\topics\TopicType;

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
	public function indexAction($parameters) {
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
		
		$this->frontController->getResponse()->setVar( 'links', $links );
		$this->frontController->doDisplay( 'tool', 'displayAllTools' );
	}
	
	public function directCreatePasswordAction($parameters) {
		if( count( $parameters ) >= 2 ) {
			$randomkey = $parameters[0];
			$password = $parameters[1];
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
	public function createBaseForumAction($parameters) {
		$forumDiscussion = new Forum();
		$forumDiscussion->setName( 'Discussion' );
		$forumDiscussion->setDescription( 'Place where people can speak with others freely' );
		$forumDiscussion->setPosition( 1 );
		$forumDiscussion->setNumPosts(0);
		$forumDiscussion->setNumTopics(0);
		$this->em->persist( $forumDiscussion );
		
		$forumStories = new Forum();
		$forumStories->setName( 'Stories' );
		$forumStories->setDescription( 'Place where people narrate stories and explain their thoughts' );
		$forumStories->setPosition( 2 );
		$forumStories->setNumPosts(0);
		$forumStories->setNumTopics(0);
		$this->em->persist( $forumStories );
		
		$forumStrategies = new Forum();
		$forumStrategies->setName( 'Strategies' );
		$forumStrategies->setDescription( 'Place where people exchange strategies under list format' );
		$forumStrategies->setPosition( 3 );
		$forumStrategies->setNumPosts(0);
		$forumStrategies->setNumTopics(0);
		$this->em->persist( $forumStrategies );
		
		$forumActivities = new Forum();
		$forumActivities->setName( 'Activities' );
		$forumActivities->setDescription( 'Place where people exchange activities and can gather some ideas' );
		$forumActivities->setPosition( 4 );
		$forumActivities->setNumPosts(0);
		$forumActivities->setNumTopics(0);
		$this->em->persist( $forumActivities );
		
		$forumSupport = new Forum();
		$forumSupport->setName( 'Support' );
		$forumSupport->setDescription( 'Place where people can ask for help from others' );
		$forumSupport->setPosition( 10 );
		$forumSupport->setNumPosts(0);
		$forumSupport->setNumTopics(0);
		$this->em->persist( $forumSupport );
		
		if( $this->em->flushSafe() ) {
			$metaRep = $this->em->getRepository( 'ForumMeta' );
			$result = true;
			$result &= $metaRep->setAcceptableTopics( $forumDiscussion->getId(), array( TopicType::$typeFreeText ) );
			$result &= $metaRep->setAcceptableTopics( $forumStories->getId(), array( TopicType::$typeStory ) );
			$result &= $metaRep->setAcceptableTopics( $forumStrategies->getId(), array( TopicType::$typeStrategy ) );
			$result &= $metaRep->setAcceptableTopics( $forumActivities->getId(), array( TopicType::$typeActivity ) );
			$result &= $metaRep->setAcceptableTopics( $forumSupport->getId(), array( TopicType::$typeFreeText ) );
			if( $result ) {
				$this->frontController->addMessage( __( 'Creation of the base forum complete with metadata' ) );
			} else {
				$this->frontController->addMessage( __( 'The metadata were not added/modified' ) );
			}
		} else {
			$this->frontController->addMessage( __( 'Creation of the base forum failed !' ) );
		}
		$this->frontController->doRedirect( 'home', 'index' );
	}
	
	public function createAdminAction($parameters) {
		$user = UserManager::createUser( 'admin', 'admin', 'w.follonier@netunion.com', UserRoles::$admin_role, 0 );
		$this->em->persist( $user );
		if( $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Creation of the admin user complete' ) );
		} else {
			$this->frontController->addMessage( __( 'Creation of the admin user failed !' ) );
		}
		$this->frontController->doRedirect( 'home', 'index' );
	}
}
