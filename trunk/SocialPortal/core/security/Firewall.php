<?php

namespace core\security;

use core\tools\ValidableInterface;

use core\user\UserManager;

use core\http\exceptions\AccessDeniedException;

use core\http\exceptions\InvalidMethodException;

use core;

use core\tools;

use core\FrontController;

use core\AbstractController;

use Doctrine\Common\Annotations\AnnotationReader;

class Firewall {
	/** @var FrontController */
	private $frontController;
	/** @var AnnotationRetriever */
	private $annotationRetriever;
	/** @var UserManager */
	private $userManager;

	public function __construct( FrontController $frontController, UserManager $userManager){
		$this->frontController = $frontController;
		$this->annotationRetriever = new tools\AnnotationRetriever(core\ClassLoader::getInstance());
		$this->userManager = $userManager;
	}

	/**
	 * @return User
	 */
	public function getAuthentificatedUser(){
		return $this->userManager->retrieveInformationAboutConnectedUser();
	}

	/**
	 * Considering the given action, this method determines if the current user has the right to use it or not
	 * @param AbstractController $controller
	 * @param string $methodName
	 * @return true iff the user has all the capabilities required
	 */
	public function checkAuthorization( AbstractController $controller, $actionName){
		$annots = $this->annotationRetriever->getAnnotationForMethod( get_class($controller), $actionName );

		foreach($annots as $name => $annot){
			if($annot instanceof ValidableInterface){
				if($annot->isValid()){
					continue;
				}else{
					$this->frontController->generateException(new AccessDeniedException(get_class($controller), $actionName));
				}
			}
		}
		
//		// determine if the method used (get or post) is accepted by the action
//		$method = $this->frontController->getRequest()->getMethod();
//		$isMethodAllowed = (isset($annots['core\tools\Method']) ? $annots['core\tools\Method']->isMethodAllowed($method) : true);
//		if( !$isMethodAllowed ){
//			$this->frontController->generateException(new InvalidMethodException(get_class($controller), $actionName, $method));
//		}
//
//		// determine the capacities necessary for the function
//		$caps = isset($annots['Secured']) ? $annots['Secured']->caps : array();
//		// replace null by the user, accessible from frontController?
//		if( !$this->hasUserAuthorization(null, $caps) ){
//			$this->frontController->generateException(new AccessDeniedException(get_class($controller), $actionName));
//		}
	}

	/**
	 * Determine if the user has the necessary rights
	 * @param User $user
	 * @param array $caps of string
	 * @return true iff the user has all capabilities
	 */
	//TODO remove the null
	private function hasUserAuthorization(User $user=null, array $caps = array()){
		if( empty($caps) ){
			return true;
		}

		return true;
	}
}