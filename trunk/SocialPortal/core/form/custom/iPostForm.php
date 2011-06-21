<?php

namespace core\form\custom;

use socialportal\model\PostBase;

use socialportal\model\TopicBase;

interface iPostForm {
	/**
	 * Given a specific post, we need to create the form
	 * @param specific post the additionnal information for that $base
	 *	it contains the post base !
	 */
	function setupWithPost($specific);
	
	/** 
	 * Given the parent, we need to construct the specific information
	 * @param PostBase $base
	 * @param specific post $existing null if we want to create a new one, othwerwise we simply modify it
	 * @return specific post linked with the $base
	 */
	function createSpecificPost(PostBase $base, $existing = null);
}