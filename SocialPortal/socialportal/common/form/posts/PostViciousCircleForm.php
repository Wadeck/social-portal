<?php

namespace socialportal\common\form\posts;

use socialportal\model\PostViciousCircle;

use socialportal\model\PostBase;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class PostViciousCircleForm extends AbstractPostForm {
	public function __construct(FrontController $frontController) {
		parent::__construct( 'ViciousCircleComment', $frontController, 'formViciousCircleSubmit', __( 'Submit' ) );
		$this->addInputField( new TextAreaField( 'post_content', __( 'General Comment' ), '', array( 'mandatory', 'strlen_at-least_10' ) ) );
		$this->addInputField( new TextAreaField( 'post_potential_solution', __( 'Potential Solution' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'post_strategy', __( 'Strategy' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
	}
	
	public function setupWithPost($post) {
		$args = array();
		$args['post_content'] = $post->getContent();
		$args['post_potential_solution'] = $post->getPotentialSolution();
		$args['post_strategy'] = $post->getStrategy();
		
		$this->fillWithArray( $args );
	}
	
	public function getPostContent() {
		return $this->ready ? $this->data['post_content'] : null;
	}
	
	public function getPotentialSolution() {
		return $this->ready ? $this->data['post_potential_solution'] : null;
	}
	
	public function getStrategy() {
		return $this->ready ? $this->data['post_strategy'] : null;
	}
	
	public function createSpecificPost(PostBase $base, $existing = null) {
		if( $existing ) {
			$post = $existing;
		} else {
			$post = new PostViciousCircle();
		}
		$post->setContent( $this->getPostContent() );
		$post->setPostbase( $base );
		$post->setPotentialSolution( $this->getPotentialSolution() );
		$post->setStrategy( $this->getStrategy() );
		return $post;
	}
}