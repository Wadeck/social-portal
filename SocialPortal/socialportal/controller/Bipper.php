<?php

namespace socialportal\controller;
use core\AbstractController;

class Bipper extends AbstractController {
	public function homeAction($parameters) {
		$this->frontController->addMessage( 'BipBip' );
		//echo 'bipper/home';
		$this->frontController->doRedirect( 'home' );
	}
	public function indexAction($parameters) {
		$this->frontController->addMessage( 'BipIndex' );
		//		$this->frontController->doRedirect('bipper', 'test');TODO remove
		echo 'bipper/index';
	}
	
	public function testAction($parameters) {
		$this->frontController->addMessage( 'BipTest' );
		$this->frontController->doDisplay( 'test' );
	}
	public function testChildAction($parameters) {
		//		$this->frontController->addMessage('BipChild');
		$this->frontController->doDisplay( 'test', 'child' );
	}
	
	public function testLoopAction($parameters) {
		$this->frontController->addMessage( 'Loopppppp' );
		$this->frontController->doRedirect( 'Bipper', 'testLoop' );
	}
	
	/**[message, redirect]*/
	public function testMessageAction($parameters) {
		if( isset( $_GET['message'] ) ) {
			$this->frontController->addMessage( $_GET['message'] );
		}
		if( isset( $_GET['redirect']) && $_GET['redirect'] ) {
			$this->frontController->doRedirect( 'Bipper', 'testMessage', array(), array(), true );
		} else {
			$this->frontController->doDisplay( 'testMessage' );
		}
	}
	
	/**
	 * @Method(POST)
	 */
	public function testPostAction($parameters) {
		$this->frontController->addMessage( 'BipTestPost' );
		$this->frontController->doDisplay( 'test' );
	}
	
	/**
	 * @Method(GeT)
	 */
	public function testGetAction($parameters) {
		$this->frontController->addMessage( 'BipTestGet' );
		$this->frontController->doDisplay( 'test' );
	}
}