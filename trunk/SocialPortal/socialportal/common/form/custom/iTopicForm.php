<?php

namespace socialportal\common\form\custom;

use socialportal\model\TopicBase;

interface iTopicForm {
	/**
	 * Given a specific topic, we need to create the form
	 * @param specific topic the additionnal information for that $base
	 * it contains the topic base !
	 */
	function setupWithTopic($specific);
	
	/** @return string */
	function getTopicTitle();
	
	/** 
	 * Given the parent, we need to construct the specific information
	 * @param TopicBase $base
	 * @param specific topic $existing null if we want to create a new one, othwerwise we simply modify it
	 * @return specific topic linked with the $base
	 */
	function createSpecificTopic(TopicBase $base, $existing = null);
}