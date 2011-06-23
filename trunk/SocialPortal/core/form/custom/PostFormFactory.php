<?php

namespace core\form\custom;

use core\tools\TopicType;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class PostFormFactory {
	/** @return iTopicForm corresponding to the topic type passed as argument */
	public static function createForm($typeId, FrontController $frontController) {
		switch ( $typeId ) {
			// TopicType::$typeActivity
			case 1 :
				return new PostActivityForm( $frontController );
			
			// TopicType::$typeFreeText
			case 2 :
				return new PostFreetextForm( $frontController );
			
			// TopicType::$typeStory
			case 3 :
				return new PostStoryForm( $frontController );
			
			// TopicType::$typeStrategy
			case 4 :
				return new PostStrategyForm( $frontController );
			
			default :
				return null;
		
		}
	}
}