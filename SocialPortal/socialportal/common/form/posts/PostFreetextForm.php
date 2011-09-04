<?php

namespace socialportal\common\form\posts;

use socialportal\model\PostFreetext;

use socialportal\model\PostBase;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class PostFreetextForm extends AbstractPostForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'FreetextComment', $frontController, 'formFreetextCommentSubmit', __( 'Submit' ) );
//		$this->addInputField( new TextAreaField( 'post_content', '', '', array( 'mandatory', 'strlen_at-least_10' ) ) );
		// no label desired on this form
		$this->addInputField( new TextAreaField( 'post_content', __( 'Comment' ), '', array( 'mandatory', 'strlen_at-least_10' ) ) );
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
			$post = new PostFreetext();
		}
		$post->setContent( $this->getPostContent() );
		$post->setPostbase( $base );
		return $post;
	}
}