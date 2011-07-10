<?php

namespace socialportal\common\topic;

use Doctrine\ORM\EntityManager;

use core\FrontController;

interface TypeManagerInterface{
	/** @return AbstractTopicTemplate */
	function getTopicTemplate(FrontController $front, EntityManager $em, $topic, $permalink);
	/** @return AbstractPostTemplate */
	function getPostTemplate(FrontController $front, EntityManager $em, array $posts, $permalink);
	
	/** @return the name with namespace of the post model related to that type */
	function getPostClassName();
	/** @return the name with namespace of the topic model related to that type */
	function getTopicClassName();
	/** @return string translated simple name of the topic */
	function getSimpleName();
	
	/** @return int the type id */
	function getTypeId();
	
	/** @return iTopicForm */
	function getTopicForm(FrontController $frontController);
	
	/** @return iPostForm */
	function getPostForm(FrontController $frontController);
}