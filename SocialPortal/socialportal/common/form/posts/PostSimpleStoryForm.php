<?php

namespace socialportal\common\form\posts;

use socialportal\model\PostSimpleStory;

use socialportal\model\PostBase;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class PostSimpleStoryForm extends AbstractPostForm {
	public function __construct(FrontController $frontController) {
		parent::__construct( 'SimpleStoryComment', $frontController, 'formSimpleStoryCommentSubmit', __( 'Submit' ) );
		$this->addInputField( new TextAreaField( 'post_content', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_10' ) ) );
	}
	
	public function setupWithPost($post) {
		$args = array();
		$args['post_content'] = $post->getStoryContent();
		
		$this->fillWithArray( $args );
	}
	
	public function getPostContent() {
		return $this->ready ? $this->data['post_content'] : null;
	}
	
	public function createSpecificPost(PostBase $base, $existing = null) {
		if( $existing ) {
			$post = $existing;
		} else {
			$post = new PostSimpleStory();
		}
		$post->setContent( $this->getPostContent() );
		$post->setPostbase( $base );
		return $post;
	}
}