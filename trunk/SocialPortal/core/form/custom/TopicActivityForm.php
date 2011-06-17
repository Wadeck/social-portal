<?php

namespace core\form\custom;

use socialportal\model\TopicActivity;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class TopicActivityForm extends AbstractTopicForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'Activity', $frontController, 'formActivitySubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'topic_title', __( 'Title of the topic' ), '', 'text', array( 'mandatory', 'strlen_less-equal_100' ) ) );
		$this->addInputField( new TextAreaField( 'topic_description', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
	}
	
	public function setupWithTopic($topic) {
		$args = array();
		$args['topic_title'] = $topic->getName();
		$args['topic_description'] = $topic->getDescription();
		
		$this->fillWithArray( $args );
	}
	
	public function getTopicTitle() {
		return $this->ready ? $this->data['topic_title'] : null;
	}
	
	public function getTopicDescription() {
		return $this->ready ? $this->data['topic_description'] : null;
	}
	public function createSpecificTopic(TopicBase $base) {
		$topic = new TopicActivity();
		$topic->setContent( $this->getTopicDescription() );
		$topic->setTopicbase( $base );
		return $topic;
	}
}