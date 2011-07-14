<?php

namespace socialportal\common\form\custom;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;

class ForumForm extends Form {
	protected $globalMode = 1;
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'Forum', $frontController, 'formForumSubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'forum_name', __( 'Name of the forum' ), '', 'text', array( 'mandatory', 'strlen_less-equal_15' ) ) );
		$this->addInputField( new TextAreaField( 'forum_description', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_15', 'strlen_less-than_150' ) ) );
		//		$this->addInputField( new TextField( 'forum_num_posts', __( 'The initial number of post' ), '', 'text', array( 'optional', 'value_less-than_4' ) ) );
		//		$this->addInputField( new TextField( 'forum_num_topics', __( 'The initial number of topic' ), '5', 'text', array( 'optional', 'not-default' ) ) );
		$this->setCss( 'forum-form rounded-box', 'forum_form.css' );
	}
	
	/** Children use only, to build the different forms */
	protected function addInputField(Field $field) {
		parent::addInputField( $field );
		$field->setMode( $this->globalMode );
	}
	
	public function setupWithForum(Forum $forum) {
		$args = array();
		$args['forum_name'] = $forum->getName();
		$args['forum_description'] = $forum->getDescription();
		//		$args['forum_num_posts'] = $forum->getNumPosts();
		//		$args['forum_num_topics'] = $forum->getNumTopics();
		

		$this->fillWithArray( $args );
	}
	
	public function getForumName() {
		return $this->ready ? $this->data['forum_name'] : null;
	}
	
	public function getForumDescription() {
		return $this->ready ? $this->data['forum_description'] : null;
	}

	//	public function getNumPosts() {
//		return $this->ready ? $this->data['forum_num_posts'] : null;
//	}
//	
//	public function getNumTopics() {
//		return $this->ready ? $this->data['forum_num_topics'] : null;
//	}	


}