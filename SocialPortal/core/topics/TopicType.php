<?php

namespace core\topics;

class TopicType {
	private $name;
	private $id;
	
	public function getName() {
		return $this->name;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public static function createById($id){
		$result = new TopicType();
		$result->id = $id;
		$result->name = self::translateTypeIdToName($id);
		return $result;
	} 
	
	public static function createByName($name){
		$result = new TopicType();
		$result->name = $$name;
		$result->id = self::translateNameToTypeId($name);
		return $result;
	} 
	
	/**
	 * Translate a topic type id into a topic type name
	 * @param string $typeId
	 */
	public static function translateTypeIdToName($typeId){
		$typeId = intval($typeId);
		switch($typeId){
			case 1:	return 'socialportal\model\TopicActivity';
			case 2:	return 'socialportal\model\TopicFreeText';
			case 3:	return 'socialportal\model\TopicStory';
			case 4:	return 'socialportal\model\TopicStrategy';
		}
	}
	/**
	 * Translate a topic type name into topic type id
	 * @param string $typeName
	 */
	public static function translateNameToTypeId($typeName){
		switch($typeName){
			case 'socialportal\model\TopicActivity': return 1;
			case 'socialportal\model\TopicFreeText': return 2;
			case 'socialportal\model\TopicStory': return 3;
			case 'socialportal\model\TopicStrategy': return 4;
		}
	}
}