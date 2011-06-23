<?php

namespace core\form\custom;

use socialportal\model\PostActivity;

use socialportal\model\PostBase;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class PostActivityForm extends AbstractPostForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'ActivityComment', $frontController, 'formActivityCommentSubmit', __( 'Submit' ) );
		$this->addInputField( new TextAreaField( 'post_content', __( 'Content' ), '', array( 'mandatory', 'strlen_at-least_10' ) ) );
	}
	
	public function setupWithPost($post) {
		$args = array();
		$args['post_content'] = $post->getContent();
		
		$this->fillWithArray( $args );
	}
	
	public function getPostContent() {
		return $this->ready ? $this->data['post_content'] : null;
	}
	
	public function createSpecificPost(PostBase $base, $existing = null) {
		if( $existing ) {
			$post = $existing;
		} else {
			$post = new PostActivity();
		}
		$post->setContent( $this->getPostContent() );
		$post->setPostbase( $base );
		return $post;
	}
}