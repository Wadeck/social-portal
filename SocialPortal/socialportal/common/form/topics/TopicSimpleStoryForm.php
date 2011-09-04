<?php

namespace socialportal\common\form\topics;

use socialportal\model\TopicSimpleStory;

use socialportal\model\TopicBase;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class TopicSimpleStoryForm extends AbstractTopicForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'SimpleStory', $frontController, 'formSimpleStorySubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'topic_title', __( 'Short description' ), '', 'text', array( 'mandatory', 'strlen_at-least_10', 'strlen_less-equal_50' ) ) );
		$this->addInputField( new TextAreaField( 'topic_description', __( 'Content' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_ps', __( 'PS' ), '', array( 'optional', 'strlen_at-least_4' ) ) );
	}
	
	public function setupWithTopic($topic) {
		$args = array();
		$args['topic_title'] = $topic->getTopicbase()->getTitle();
		$args['topic_description'] = $topic->getStoryContent();
		$args['topic_ps'] = $topic->getPs();
		
		$this->fillWithArray( $args );
	}
	
	public function getTopicTitle() {
		return $this->ready ? $this->data['topic_title'] : null;
	}
	
	public function getTopicDescription() {
		return $this->ready ? $this->data['topic_description'] : null;
	}
	
	public function getPs() {
		return $this->ready ? $this->data['topic_ps'] : null;
	}
	
	public function createSpecificTopic(TopicBase $base, $existing = null) {
		if( $existing ) {
			$topic = $existing;
		} else {
			$topic = new TopicSimpleStory();
		}
		$topic->setStoryContent( $this->getTopicDescription() );
		$topic->setTopicbase( $base );
		$topic->setPs( $this->getPs() );
		return $topic;
	}
}