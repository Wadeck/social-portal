<?php

namespace socialportal\common\form\topics;

use socialportal\model\TopicViciousCircle;

use socialportal\model\TopicBase;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class TopicViciousCircleForm extends AbstractTopicForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'ViciousCircle', $frontController, 'formViciousCircleSubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'topic_title', __( 'Short description' ), '', 'text', array( 'mandatory', 'strlen_at-least_10', 'strlen_less-equal_50' ) ) );
		$this->addInputField( new TextAreaField( 'topic_low_self_esteem', __( 'Low Self Esteem' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_potential_solution', __( 'Potential Solution' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_strategy', __( 'Strategy' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_evaluation', __( 'Evaluation' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'topic_ps', __( 'PS' ), '', array( 'optional', 'strlen_at-least_4' ) ) );
	}
	
	public function setupWithTopic($topic) {
		$args = array();
		$args['topic_title'] = $topic->getTopicbase()->getTitle();
		$args['topic_low_self_esteem'] = $topic->getStoryContent();
		$args['topic_potential_solution'] = $topic->getPotentialSolution();
		$args['topic_strategy'] = $topic->getStrategy();
		$args['topic_evaluation'] = $topic->getEvaluation();
		$args['topic_ps'] = $topic->getPs();
		
		$this->fillWithArray( $args );
	}
	
	public function getTopicTitle() {
		return $this->ready ? $this->data['topic_title'] : null;
	}
	
	public function getTopicDescription() {
		return $this->ready ? $this->data['topic_low_self_esteem'] : null;
	}
	
	public function getPotentialSolution() {
		return $this->ready ? $this->data['topic_potential_solution'] : null;
	}
	
	public function getStrategy() {
		return $this->ready ? $this->data['topic_strategy'] : null;
	}
	
	public function getEvaluation() {
		return $this->ready ? $this->data['topic_evaluation'] : null;
	}
	
	public function getPs() {
		return $this->ready ? $this->data['topic_ps'] : null;
	}
	
	public function createSpecificTopic(TopicBase $base, $existing = null) {
		if( $existing ) {
			$topic = $existing;
		} else {
			$topic = new TopicViciousCircle();
		}
		$topic->setTopicbase( $base );
		$topic->setLowSelfEsteem( $this->getTopicDescription() );
		$topic->setPotentialSolution( $this->getPotentialSolution() );
		$topic->setStrategy( $this->getStrategy() );
		$topic->setEvaluation( $this->getEvaluation() );
		$topic->setPs( $this->getPs() );
		return $topic;
	}
}