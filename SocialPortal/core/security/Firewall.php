<?php

namespace core\security;

use core\ClassLoader;

use core\annotations\AnnotationRetriever;

use core\annotations\ValidableInterface;

use core\user\UserManager;

use core\http\exceptions\AccessDeniedException;

use core\http\exceptions\InvalidMethodException;

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
	
	public function __construct(FrontController $frontController, UserManager $userManager) {
		$this->frontController = $frontController;
		$this->annotationRetriever = new AnnotationRetriever( ClassLoader::getInstance() );
		$this->userManager = $userManager;
	}
	
	/**
	 * @return User
	 */
	public function getAuthentificatedUser() {
		return $this->userManager->retrieveInformationAboutConnectedUser();
	}
	
	/**
	 * Considering the given action, this method determines if the current user has the right to use it or not
	 * @param AbstractController $controller
	 * @param string $methodName
	 * @return true iff the user has all the capabilities required
	 */
	public function checkAuthorization(AbstractController $controller, $actionName) {
		$annots = $this->annotationRetriever->getAnnotationForMethod( get_class( $controller ), $actionName );
		// object oriented approach
		foreach( $annots as $name => $annot ) {
			if( $annot instanceof ValidableInterface ) {
				if( $annot->isValid() ) {
					continue;
				} else {
					$this->frontController->generateException( new AccessDeniedException( get_class( $controller ), $actionName ) );
				}
			}
		}
	}
}