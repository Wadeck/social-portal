<?php

namespace core;

use Doctrine\ORM\EntityManager;

class AbstractController {
	/** @var FrontController */
	protected $frontController;
	/** @var EntityManager */
	protected $em;
	
	public final function setFrontController(FrontController $front) {
		$this->frontController = $front;
	}
	
	public final function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}
	
	public function actionBefore($action, $parameters) {}
	public function actionAfter($action, $parameters) {}

	// other function with names xxxAction where xxx is the name of the action with [parameters]
}