<?php

namespace core\tools;

class MathUtils {
	public static function clamp($value, $min, $max){
		if($min > $max){
			throw new \Exception('The clamp min/max are inversed');
		}
		if($value < $min){
			return $min;
		}
		if($value > $max){
			return $max;
		}
		return $value;
	}
}