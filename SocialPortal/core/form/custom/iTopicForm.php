<?php

namespace core\form\custom;

use socialportal\model\TopicBase;

interface iTopicForm {
	function setupWithTopic($topic);
	/** @return string */
	function getTopicTitle();
	/** @return Entity */
	function createSpecificTopic(TopicBase $base);
}