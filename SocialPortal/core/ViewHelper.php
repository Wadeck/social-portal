<?php

namespace core;

use core\http\Response;

use core\http\Request;

use core\http\exceptions\PageNotFoundException;
use core\FrontController;
use core\security\Firewall;

/** Class that will be passed to the views to have a certain set of possible action */
class ViewHelper {
	/** @var array of nonce that are stored for the routines */
	private $nonceStack = array();
	/** @var FrontController*/
	private $frontController;
	/** @var Request */
	public $request;
	/** @var Response */
	public $response;
	
	public function __construct(FrontController $controller, Request $request, Response $response) {
		$this->frontController = $controller;
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * Add a css file to the list of files that will be added when we'll build the response
	 * @param string $cssFile could either be default.css or forum/table.css
	 */
	public function addCssFile($cssName) {
		$this->response->addCssFile( $cssName );
	}
	
	/**
	 * Add a js file to the list of files that will be added when we'll build the response
	 * @param string $cssFile could either be default.js or forum/table.js
	 */
	public function addJavascriptFile($scriptName) {
		$this->response->addJsFile( $scriptName );
	}
	
	/**
	 * Add a js var to the response
	 */
	public function addJavascriptVar($name, $assoc) {
		$this->response->addJsVar( $name, $assoc );
	}
	
	/**
	 * Display the message that are stored in flash session
	 */
	public function insertMessage() {
		$session = $this->frontController->getRequest()->getSession();
		
		// error prevails information
		$message = $session->getFlash( 'notice_error' );
		$class = 'flash_message error';
		if( !$message ) {
			$message = $session->getFlash( 'notice_correct' );
			$class = 'flash_message correct';
			
			if( !$message ) {
				$message = $session->getFlash( 'notice_info' );
				$class = 'flash_message info';
			}
		}
		
		$redirect = $session->getFlash( 'redirectFrom' );
		if( !$message ) {
			// no Message pending
			return;
		}
		$this->addJavascriptFile( 'jquery.js' );
		$this->addJavascriptFile( 'message_click.js' );
		$this->addCssFile( 'messages.css' );
		?><div class="<?php
		echo $class;
		?>"><?php
		echo $message;
		?></div><?php
	}
	
	/** Could be used for static view insertion */
	public function insertView($module, $action = '', $addVars = array()) {
		$fileName = Config::getOrDie('view_dir') . $module;
		if( $action ) {
			$fileName .= DIRECTORY_SEPARATOR . $action;
		}
		
		$file = $this->frontController->loader->getFileName( $fileName, '.phtml' );
		if( false === $file ) {
			$this->generateException( new PageNotFoundException( $module, $action ) );
		}
		
		echo $this->frontController->renderFile( $file, $addVars );
	}
	
	/**
	 * 
	 * Could be used for login/pass/avatar template
	 * @param string $module
	 * @param string $action
	 * @param array $gets assoc array containing key=>value
	 * @param string $nonceAction The name of the action we want to "nonce", not already hashed !
	 */
	public function insertModule($module, $action = '', array $gets = array(), $nonceAction = false) {
		// store the nonce to avoid giving bad nonce to the createHref method
		if( false !== $nonceAction ) {
			$nonce = $this->frontController->getNonceManager()->createNonce( $nonceAction );
			$gets['_nonce'] = $nonce;
		}
		
		$tempVars = $this->frontController->getResponse()->removeAllVars();
		// like a stack
		$tempGet = $this->frontController->getRequest()->query->all();
		$this->frontController->getRequest()->query->replace( $gets );
		
		$this->frontController->doAction( $module, $action );
		
		// reset the previously set GET values
		$this->frontController->getRequest()->query->replace( $tempGet );
		$this->frontController->getResponse()->setVars( $tempVars );
	}

	/**
	 * Generate a specific url for the site
	 * @param string $controllerName
	 * @param string $actionName
	 * @param array $GETAttributes
	 */
	public function createHref($controllerName, $actionName = '', array $gets = array(), $targetId = false) {
		$result = '/' . Config::getOrDie('site_name') . '/' . $controllerName;
		if( $actionName ) {
			$result .= '/' . $actionName;
		}
		if( !empty( $gets ) ) {
			$result .= '?' . implode( '&', array_map( function ($key, $value) {
				return "$key=$value";
			}, array_keys( $gets ), array_values( $gets ) ) );
		}
		// to force the browser to go to the given id
		if( false !== $targetId ) {
			$result .= "#$targetId";
		}
		return $result;
	}
	
	public function createHrefWithNonce($nonce, $controllerName, $actionName = '', array $gets = array(), $targetId = false) {
		if( $nonce ) {
			$nonceHash = $this->frontController->getNonceManager()->createNonce( $nonce );
			$gets['_nonce'] = $nonceHash;
			if(defined('DEBUG') && DEBUG){
				//TODO remove after debug
				$gets['_nonce_clear'] = $nonce;
			}				
		}
		$href = $this->createHref( $controllerName, $actionName, $gets );
		return $href;
	}
	
	/** @see ViewHelper#createHref */
	public function insertHref($controllerName, $actionName = '', array $gets = array(), $targetId = false) {
		echo $this->createHref( $controllerName, $actionName, $gets, $targetId );
	}
	
	/** @see ViewHelper#createHrefWithNonce */
	public function insertHrefWithNonce($nonce, $controllerName, $actionName = '', array $gets = array(), $targetId = false) {
		echo $this->createHrefWithNonce( $nonce, $controllerName, $actionName, $gets, $targetId );
	}
	
	/**
	 * @param $message Translated message that will be shown
	 */
	public function insertConfirmLink($message) {
		$this->addJavascriptFile( 'confirm_link.js' );
		echo ' onclick="return confirmLink(this, \'' . $message . '\')"';
	}
	
	/** @return User */
	public function getCurrentUser() {
		return $this->frontController->getCurrentUser();
	}
	
	public function setContainerClass($class) {
		$this->frontController->getResponse()->setContainerClass( $class );
	}
	
	public function setTitle($title) {
		$this->frontController->getResponse()->setTitle( $title );
	}
	
	// TODO refactor to put in security 
	/** @var array of roles, cache version */
	private $cacheRoles;
	public function currentUserIs($role) {
		if( !$this->cacheRoles ) {
			$this->cacheRoles = $this->frontController->getCurrentUser()->getRoles();
		}
		return ($this->cacheRoles & $role) == $role;
	}
	
	/** @var array of capabilities, cache version */
	private $cacheCapabilities;
	public function currentUserCan($capability) {
		//		if(!$this->cacheCapabilities){
		//			$this->cacheCapabilities = $this->frontController->getCurrentUser()->getRoles();
		//		}
		//		return ($this->cacheRoles & $role) == $role;
		if( $capability === false ) {
			return false;
		}
		return true;
	}
}