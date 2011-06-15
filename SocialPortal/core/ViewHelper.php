<?php

namespace core;

use core\http\exceptions\PageNotFoundException;
use core\FrontController;
use core\Request;
use core\Response;
use core\security\Firewall;

/** Class that will be passed to the views to have a certain set of possible action */
class ViewHelper {
	/** @var string representing the hash of action / user / time to avoid malicious users to hijack session easily */
	private $nonce;
	/** @var core\FrontController*/
	private $frontController;
	/** @var core\Request */
	public $request;
	/** @var core\Response */
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
	
	public function insertMessage() {
		$message = $this->frontController->getRequest()->getSession()->getFlash( 'notice' );
		//TODO remove after debug
		$redirect = $this->frontController->getRequest()->getSession()->getFlash( 'redirectFrom' );
		//		if( $redirect ) {
		//			echo "<p>Redirect from: $redirect</p>";
		//		} else {
		//			echo "<p>No redirect</p>";
		//		}
		if( !$message ) {
			//			echo '<p>No Message pending</p>';
			return;
		}
		$this->addCssFile( 'messages' );
		?><div class="flash_message"><?php
		echo $message;
		?></div><?php
	}
	
	/** Could be used for static view insertion */
	public function insertView($module, $action = '', $parameters = array()) {
		$fileName = FrontController::$VIEW_DIR . $module;
		if( $action ) {
			$fileName .= DIRECTORY_SEPARATOR . $action;
		}
		
		$file = $this->frontController->loader->getFileName( $fileName, '.phtml' );
		if( false === $file ) {
			$this->generateException( new PageNotFoundException( $module, $action ) );
		}
		
		echo $this->frontController->renderFile( $file, $parameters );
	}
	
	/** Could be used for login/pass/avatar template */
	public function insertModule($module, $action = '', $parameters = array()) {
		$tempVars = $this->frontController->getResponse()->removeAllVars();
		$this->frontController->doAction( $module, $action, $parameters );
		$this->frontController->getResponse()->setVars($tempVars);
	}
	
	public function setNonce($nonce) {
		$this->nonce = $nonce;
	}
	
	/**
	 * Generate a specific url for the site
	 * @param string $controllerName
	 * @param string $actionName
	 * @param array $parameters
	 * @param array $GETAttributes
	 */
	public function createHref($controllerName, $actionName = '', $parameters = array(), $GETAttributes = array()) {
		$result = '/' . FrontController::$SITE_NAME . '/' . $controllerName;
		if( $actionName ) {
			$result .= '/' . $actionName;
		}
		if( $actionName && !empty( $parameters ) ) {
			$result .= '/' . implode( '/', $parameters );
		}
		if( $this->nonce ) {
			$GETAttributes['_nonce'] = $this->nonce;
		}
		if( !empty( $GETAttributes ) ) {
			$result .= '?' . implode( '&', array_map( function ($key, $value) {
				return "$key=$value";
			}, array_keys( $GETAttributes ), array_values( $GETAttributes ) ) );
		}
		return $result;
	}
	
	/** @see ViewHelper#createHref */
	public function insertHref($controllerName, $actionName = '', $parameters = array(), $GETAttributes = array()) {
		echo $this->createHref( $controllerName, $actionName, $parameters, $GETAttributes );
	}
	
	/** @return User */
	public function getCurrentUser() {
		return $this->frontController->getCurrentUser();
	}
}