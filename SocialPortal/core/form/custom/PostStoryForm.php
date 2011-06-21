<?php

namespace core\form\custom;

use socialportal\model\PostStory;

use socialportal\model\PostStrategy;

use socialportal\model\PostBase;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class PostStoryForm extends AbstractPostForm {
	public function __construct(FrontController $frontController) {
		parent::__construct( 'StoryComment', $frontController, 'formStoryCommentSubmit', __( 'Submit' ) );
		$this->addInputField( new TextAreaField( 'post_content', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_10' ) ) );
		$this->addInputField( new TextAreaField( 'post_automatic_thoughts', __( 'Automatic Thoughts' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'post_alternative_thoughts', __( 'Alternative Thoughts' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
		$this->addInputField( new TextAreaField( 'post_realistic_thoughts', __( 'Realistic Thoughts' ), '', array( 'optional', 'strlen_at-least_25' ) ) );
	}
	
	public function setupWithPost($post) {
		$args = array();
		$args['post_content'] = $post->getStoryContent();
		$args['post_automatic_thoughts'] = $post->getAutomaticThoughts();
		$args['post_alternative_thoughts'] = $post->getAlternativeThoughts();
		$args['post_realistic_thoughts'] = $post->getRealisticThoughts();
		
		$this->fillWithArray( $args );
	}
	
	public function getPostContent() {
		return $this->ready ? $this->data['post_content'] : null;
	}

	public function getAutomaticThoughts() {
		return $this->ready ? $this->data['post_automatic_thoughts'] : null;
	}
	
	public function getAlternativeThoughts() {
		return $this->ready ? $this->data['post_alternative_thoughts'] : null;
	}
	
	public function getRealisticThoughts() {
		return $this->ready ? $this->data['post_realistic_thoughts'] : null;
	}
	
	public function createSpecificPost(PostBase $base, $existing = null) {
		if( $existing ) {
			$post = $existing;
		} else {
			$post = new PostStory();
		}
		$post->setContent( $this->getPostContent() );
		$post->setPostbase( $base );
		$post->setAlternativeThoughts( $this->getAlternativeThoughts() );
		$post->setAutomaticThoughts( $this->getAutomaticThoughts() );
		$post->setRealisticThoughts( $this->getRealisticThoughts() );
		return $post;
	}
}