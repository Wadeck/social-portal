<?php

namespace socialportal\common\form\topics;

use socialportal\model\TopicBase;

use socialportal\model\TopicFreetext;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class TopicFreetextForm extends AbstractTopicForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'Freetext', $frontController, 'formFreetextSubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'topic_title', __( 'Title of the topic' ), '', 'text', array( 'mandatory', 'strlen_at-least_10', 'strlen_less-equal_50' ) ) );
		$this->addInputField( new TextAreaField( 'topic_description', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
	}
	
	public function setupWithTopic($topic) {
		$args = array();
		$args['topic_title'] = $topic->getTopicbase()->getTitle();
		$args['topic_description'] = $topic->getContent();
		
		$this->fillWithArray( $args );
	}
	
	public function getTopicTitle() {
		return $this->ready ? $this->data['topic_title'] : null;
	}
	
	public function getTopicDescription() {
		return $this->ready ? $this->data['topic_description'] : null;
	}
	
	public function createSpecificTopic(TopicBase $base, $existing = null) {
		if( $existing ) {
			$topic = $existing;
		} else {
			$topic = new TopicFreetext();
		}
		$topic->setContent( $this->getTopicDescription() );
		$topic->setTopicbase( $base );
		return $topic;
	}
}