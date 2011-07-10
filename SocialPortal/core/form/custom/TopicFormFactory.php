<?php

namespace core\form\custom;

use core\tools\TopicType;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

/**
 * 
 * Enter description here ...
 * @author Wadeck
 * @deprecated
 */
class TopicFormFactory {
	/** @return iTopicForm corresponding to the topic type passed as argument */
	public static function createForm($typeId, FrontController $frontController) {
		switch ( $typeId ) {
			// TopicType::$typeActivity
			case 1 :
				return new TopicActivityForm( $frontController );
			
			// TopicType::$typeFreeText
			case 2 :
				return new TopicFreetextForm( $frontController );
			
			// TopicType::$typeStory
			case 3 :
				return new TopicStoryForm( $frontController );
			
			// TopicType::$typeStrategy
			case 4 :
				return new TopicStrategyForm( $frontController );
			
			default :
				return null;
		
		}
	}
}