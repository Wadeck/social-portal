<?php

namespace socialportal\common\topic;

use socialportal\common\topic\manager\MindfulnessManager;

use socialportal\common\topic\manager\ViciousCircleManager;

use socialportal\common\topic\manager\ProblemManager;

use socialportal\common\topic\manager\AutomaticThoughtsManager;

use socialportal\common\topic\manager\StrategyManager;

use socialportal\common\topic\manager\SimpleStoryManager;

use socialportal\common\topic\manager\FreetextManager;

use socialportal\common\topic\manager\ActivityManager;

class TypeCenter {
	public static $activityType = 1;
	public static $freetextType = 2;
	public static $automaticThoughtsType = 3;
	public static $strategyType = 4;
	public static $simpleStoryType = 5;
	public static $problemType = 6;
	public static $viciousCircleType = 7;
	public static $mindfulnessType = 8;
	
	/**
	 * Used to retrieve the manager of the given type of topic
	 * @param int $typeId 1: activity, 2: freetext, 3: story, 4: strategy
	 * @return TypeManagerInterface	
	 */
	public static function getTypeManager($typeId) {
		$typeManager = null;
		switch ($typeId) {
			case self::$activityType :
				$typeManager = new ActivityManager();
				break;
			case self::$freetextType :
				$typeManager = new FreetextManager();
				break;
			case self::$automaticThoughtsType :
				$typeManager = new AutomaticThoughtsManager();
				break;
			case self::$strategyType :
				$typeManager = new StrategyManager();
				break;
			case self::$simpleStoryType :
				$typeManager = new SimpleStoryManager();
				break;
			case self::$problemType :
				$typeManager = new ProblemManager();
				break;
			case self::$viciousCircleType :
				$typeManager = new ViciousCircleManager();
				break;
			case self::$mindfulnessType :
				$typeManager = new MindfulnessManager();
				break;
			default :
				return null;
		}
		
		return $typeManager;
	}
}