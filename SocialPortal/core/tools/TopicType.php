<?php

namespace core\tools;

use socialportal\common\templates\StrategyPostTemplate;

use socialportal\common\templates\StoryPostTemplate;

use socialportal\common\templates\FreetextPostTemplate;

use socialportal\common\templates\ActivityPostTemplate;

use core\FrontController;

use Doctrine\ORM\EntityManager;

use socialportal\common\templates\StrategyTopicTemplate;

use socialportal\common\templates\StoryTopicTemplate;

use socialportal\common\templates\FreetextTopicTemplate;

use socialportal\common\templates\ActivityTopicTemplate;

/**
 * @author Wadeck
 * @deprecated
 */
class TopicType {
	public static $typeActivity = 1;
	public static $typeFreetext = 2;
	public static $typeStory = 3;
	public static $typeStrategy = 4;
	
	/** @var string */
	private $name;
	/** @var string */
	private $simpleName;
	/** @var string */
	private $id;
	
	public function getName() {
		return $this->name;
	}
	public function getSimpleName() {
		return $this->simpleName;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public static function createById($id) {
		$result = new TopicType();
		$result->id = $id;
		$result->name = self::translateTypeIdToName( $id );
		$result->simpleName = self::translateTypeIdToSimpleName( $id );
		return $result;
	}
	
	public static function createByName($name) {
		$result = new TopicType();
		$result->name = $name;
		$result->id = self::translateNameToTypeId( $name );
		$result->simpleName = self::translateTypeIdToSimpleName( $result->id );
		return $result;
	}
	
	/**
	 * Translate a topic type id into a topic type name
	 * @param string $typeId
	 */
	public static function translateTypeIdToName($typeId) {
		$typeId = intval( $typeId );
		switch ( $typeId ) {
			case 1 :
				return 'socialportal\model\TopicActivity';
			case 2 :
				return 'socialportal\model\TopicFreetext';
			case 3 :
				return 'socialportal\model\TopicStory';
			case 4 :
				return 'socialportal\model\TopicStrategy';
		}
	}
	
	/**
	 * Translate a topic type id into a topic type name
	 * @param string $typeId
	 */
	public static function translateTypeIdToPostName($typeId) {
		$typeId = intval( $typeId );
		switch ( $typeId ) {
			case 1 :
				return 'socialportal\model\PostActivity';
			case 2 :
				return 'socialportal\model\PostFreetext';
			case 3 :
				return 'socialportal\model\PostStory';
			case 4 :
				return 'socialportal\model\PostStrategy';
		}
	}
	
	/**
	 * Translate a topic type id into a topic type name
	 * @param string $typeId
	 */
	public static function translateTypeIdToSimpleName($typeId) {
		$typeId = intval( $typeId );
		switch ( $typeId ) {
			case 1 :
				return 'Activity';
			case 2 :
				return 'Freetext';
			case 3 :
				return 'Story';
			case 4 :
				return 'Strategy';
		}
	}
	/**
	 * Translate a topic type name into topic type id
	 * @param string $typeName
	 */
	public static function translateNameToTypeId($typeName) {
		switch ( $typeName ) {
			case 'socialportal\model\TopicActivity' :
				return 1;
			case 'socialportal\model\TopicFreetext' :
				return 2;
			case 'socialportal\model\TopicStory' :
				return 3;
			case 'socialportal\model\TopicStrategy' :
				return 4;
		}
	}
	
	/**
	 * Translate a topic type name into topic type id
	 * @param string $typeName
	 */
	public static function translateSimpleNameToTypeId($typeName) {
		switch ( $typeName ) {
			case 'Activity' :
				return 1;
			case 'Freetext' :
				return 2;
			case 'Story' :
				return 3;
			case 'Strategy' :
				return 4;
		}
	}
	
	/**
	 * Translate a topic type name into topic type id
	 * @param string $typeName
	 * @return iTopicTemplate
	 */
	public static function getTopicTemplate($typeId, FrontController $front, EntityManager $em, $topic, $permalink) {
		$typeId = intval( $typeId );
		$result = null;
		switch ( $typeId ) {
			case 1 :
				$result = new ActivityTopicTemplate();
				break;
			case 2 :
				$result = new FreetextTopicTemplate();
				break;
			case 3 :
				$result = new StoryTopicTemplate();
				break;
			case 4 :
				$result = new StrategyTopicTemplate();
				break;
		}
		if( $result ) {
			$result->setFrontController( $front );
			$result->setEntityManager( $em );
			$result->setTopic( $topic );
			$result->setPermalink( $permalink );
		}
		return $result;
	}
	
	/**
	 * Translate a topic type name into topic type id
	 * @param string $typeName
	 * @return iTopicTemplate
	 */
	public static function getPostTemplate($typeId, FrontController $front, EntityManager $em, array $posts, $permalink) {
		$typeId = intval( $typeId );
		$result = null;
		switch ( $typeId ) {
			case 1 :
				$result = new ActivityPostTemplate();
				break;
			case 2 :
				$result = new FreetextPostTemplate();
				break;
			case 3 :
				$result = new StoryPostTemplate();
				break;
			case 4 :
				$result = new StrategyPostTemplate();
				break;
		}
		if( $result ) {
			$result->setFrontController( $front );
			$result->setEntityManager( $em );
			$result->setPosts( $posts );
			$result->setPermalink( $permalink );
		}
		return $result;
	}
}