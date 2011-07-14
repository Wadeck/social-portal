<?php

namespace socialportal\common\form\custom;

use socialportal\model\TopicBase;

use socialportal\model\TopicStory;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class TopicStoryForm extends AbstractTopicForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'Story', $frontController, 'formStorySubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'topic_title', __( 'Title of the topic' ), '', 'text', array( 'mandatory', 'strlen_at-least_10', 'strlen_less-equal_50' ) ) );
		$this->addInputField( new TextAreaField( 'topic_description', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_automatic_thoughts', __( 'Automatic Thoughts' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_alternative_thoughts', __( 'Alternative Thoughts' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_realistic_thoughts', __( 'Realistic Thoughts' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_ps', __( 'PS' ), '', array( 'optional', 'strlen_at-least_4' ) ) );
	}
	
	public function setupWithTopic($topic) {
		$args = array();
		$args['topic_title'] = $topic->getTopicbase()->getTitle();
		$args['topic_description'] = $topic->getStoryContent();
		$args['topic_automatic_thoughts'] = $topic->getAutomaticThoughts();
		$args['topic_alternative_thoughts'] = $topic->getAlternativeThoughts();
		$args['topic_realistic_thoughts'] = $topic->getRealisticThoughts();
		$args['topic_ps'] = $topic->getPs();
		
		$this->fillWithArray( $args );
	}
	
	public function getTopicTitle() {
		return $this->ready ? $this->data['topic_title'] : null;
	}
	
	public function getTopicDescription() {
		return $this->ready ? $this->data['topic_description'] : null;
	}
	
	public function getAutomaticThoughts() {
		return $this->ready ? $this->data['topic_automatic_thoughts'] : null;
	}
	
	public function getAlternativeThoughts() {
		return $this->ready ? $this->data['topic_alternative_thoughts'] : null;
	}
	
	public function getRealisticThoughts() {
		return $this->ready ? $this->data['topic_realistic_thoughts'] : null;
	}
	
	public function getPs() {
		return $this->ready ? $this->data['topic_ps'] : null;
	}
	
	public function createSpecificTopic(TopicBase $base, $existing = null) {
		if( $existing ) {
			$topic = $existing;
		} else {
			$topic = new TopicStory();
		}
		$topic->setStoryContent( $this->getTopicDescription() );
		$topic->setTopicbase( $base );
		$topic->setAlternativeThoughts( $this->getAlternativeThoughts() );
		$topic->setAutomaticThoughts( $this->getAutomaticThoughts() );
		$topic->setRealisticThoughts( $this->getRealisticThoughts() );
		$topic->setPs( $this->getPs() );
		return $topic;
	}
}