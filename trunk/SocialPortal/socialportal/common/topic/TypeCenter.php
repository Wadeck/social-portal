<?php

namespace socialportal\common\topic;

use socialportal\common\topic\manager\StrategyManager;

use socialportal\common\topic\manager\SimpleStoryManager;

use socialportal\common\topic\manager\FreetextManager;

use socialportal\common\topic\manager\ActivityManager;

class TypeCenter{
	public static $activityType = 1;
	public static $freetextType = 2;
	public static $simpleStoryType = 3;
	public static $strategyType = 4;
	/**
	 * Used to retrieve the manager of the given type of topic
	 * @param int $typeId 1: activity, 2: freetext, 3: story, 4: strategy
	 * @return TypeManagerInterface	
	 */
	public static function getTypeManager($typeId){
		$typeManager = null;
		switch($typeId){
			case 1:
				$typeManager = new ActivityManager();
				break;
			case 2:
				$typeManager = new FreetextManager();
				break;
			case 3:
				$typeManager = new SimpleStoryManager();
				break;
			case 4:
				$typeManager = new StrategyManager();
				break;
			default:
				return null;
		}
		
		return $typeManager;
	}
}