<?php

namespace core\form\custom;

use core\tools\Utils;

use socialportal\model\TopicStrategyItem;

use core\form\fields\StaticListField;

use core\form\fields\DynamicListField;

use socialportal\model\TopicBase;

use socialportal\model\TopicStrategy;

use core\form\fields\RadioField;

use socialportal\model\Forum;

use core\form\fields\TextField;

use core\form\fields\TextAreaField;

use core\form\Field;

use core\FrontController;

use core\form\Form;
use DateTime;
class TopicStrategyForm extends AbstractTopicForm {
	
	public function __construct(FrontController $frontController) {
		parent::__construct( 'Strategy', $frontController, 'formStrategySubmit', __( 'Submit' ) );
		$this->addInputField( new TextField( 'topic_title', __( 'Title of the topic' ), '', 'text', array( 'mandatory', 'strlen_at-least_10', 'strlen_less-equal_50' ) ) );
		$this->addInputField( new TextAreaField( 'topic_description', __( 'Description' ), '', array( 'mandatory', 'strlen_at-least_25' ) ) );
		$this->addInputField( new StaticListField('topic_list', __('Your list'), array(), 5, array( '' )) );
	}
	
	public function setupWithTopic($topic) {
		$em = $this->frontController->getEntityManager();
		$listRepo = $em->getRepository('TopicStrategyItem');
		// use the strategy topic id, not the base one
		$listTemp = $listRepo->findAllItems($topic->getId());
		
		$list = array();
		foreach ($listTemp as $value) {
			$list[$value->getPosition()] = $value->getContent();
		}
		
		$args = array();
		$args['topic_title'] = $topic->getTopicbase()->getTitle();
		$args['topic_description'] = $topic->getDescription();
		$args['topic_list'] = $list;
		
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
			$topic = new TopicStrategy();
		}
		$topic->setDescription( $this->getTopicDescription() );
		$topic->setTopicbase( $base );
		
		return $topic;
	}
	
	public function hasSecondAction(){
		return true;
	}
	
	public function doSecondAction($topic){
		if(!$this->ready){
			return false;
		}
		$em = $this->frontController->getEntityManager();
		$listRepo = $em->getRepository('TopicStrategyItem');
		// use the strategy topic id, not the base one
		$listTemp = $listRepo->findAllItems($topic->getId());
		$list = array();
		foreach ($listTemp as $value) {
			$list[$value->getPosition()] = $value;
		}
		
		$now = $this->frontController->getRequest()->getRequestDateTime();
		
		$addList = $this->data['topic_list'];
		$addList = array_map(function($val){return Utils::getCleanText($val);}, $addList);
		$item = null;
		// mettre a jour les clefs deja existante
		$keyFromExisting = array_intersect_key($addList, $list);
		foreach ($keyFromExisting as $key => $value) {
			$item = $list[$key];
			if('' == $value){
				$item->setIsDeleted(1);
			}else{
				$item->setContent($value);
			}
			$em->persist($item);
		}
		// supprimer les clefs qui ne sont plus la, for the moment, never used
		$keyRemoved = array_diff_key($list, $addList);
		foreach ($keyRemoved as $key => $value) {
			$item = $list[$key];
			$item->setIsDeleted(1);
			$em->persist($item);
		}
		// ajouter les nouvelles clefs
		$keyAdded = array_diff_key($addList, $list);
		foreach ($keyAdded as $key => $value) {
			if(!$value){
				continue;
			}
			$item = new TopicStrategyItem();
			$item->setTopic($topic);
			$item->setContent($value);
			$item->setPosition($key);
			$item->setCreationTime($now);
			$item->setAuthor($this->frontController->getCurrentUser()->getId());
			$item->setIsDeleted(0);
			$em->persist($item);
		}
		
		return $em->flushSafe();
	}
	
}