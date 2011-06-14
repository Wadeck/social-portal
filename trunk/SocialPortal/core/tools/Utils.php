<?php

namespace core\tools;

class Utils{
	public static function getValidDate( $value ){
		if( is_numeric($value) ){
			$timestamp = $value;
		}else{
			$timestamp = strtotime($value);
		}
		if( !$timestamp ){
			$timestamp = time();
		}
		return date('Y-m-d h:i:s', $timestamp);
	}
	
	public static function getCleanText( $text ){
		$temp = $text;
		$text = trim( strip_tags( stripcslashes( $text ) ) );
		return $text;
	}
	
	/**
	 * 
	 * @param method $func
	 * @param array $args of params
	 */
	public static function arrayToParamsMethod($func, $obj, $args=false){
		if( !($func instanceof \ReflectionMethod) ){
			return false;
		}
		if( !$args ){
			return $func->invoke($obj);
		}
		if( !is_array($args) ){
			return $func->invoke($obj, $args);
		}
		$count = count($args);
		switch($count){
			case 1:	return $func->invoke($obj, $args[0]);
			case 2: return $func->invoke($obj, $args[0], $args[1]);
			case 3: return $func->invoke($obj, $args[0], $args[1], $args[2]);
			case 4: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3]);
			case 5: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3], $args[4]);
			case 6: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
			case 7: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
			case 8: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
			case 9: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
			case 10: return $func->invoke($obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
			default: return new \ErrorException('Too many arguments: '.$count);
		}
	}
}