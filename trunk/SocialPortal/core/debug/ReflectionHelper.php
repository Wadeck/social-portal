<?php

namespace core\debug;

use core\tools\Utils;

class ReflectionHelper {
	/**
	 * Allow a test to use a method that is normally not accessible
	 * @param string $name The name of the method
	 * @param string|stdObject $obj The name of the class or an instance of the class
	 * @return Method with public access
	 */
	public static function retrieveMethod($instance, $name) {
		if( !is_string( $instance ) ) {
			$instance = get_class( $instance );
		}
		$refl = new \ReflectionClass( $instance );
		$method = $refl->getMethod( $name );
		$method->setAccessible( true );
		return $method;
	}
	
	public static function useMethod($instance, $name, $args = array()) {
		$method = self::retrieveMethod( $instance, $name );
		return Utils::arrayToParamsMethod( $method, $instance, $args );
	}
	
	public static function createInstance($class) {
		if( !is_string( $class ) ) {
			$class = get_class( $class );
		}
		$refl = new \ReflectionClass( $class );
		try {
			$constructor = $refl->getConstructor();
			$instance = $refl->newInstance();
		} catch (\Exception $e ) {
			if( $refl->hasMethod( 'newInstance' ) ) {
				$method = $refl->getMethod( 'newInstance' );
				$method->setAccessible( true );
				if( $method->isStatic() ) {
					// no need $this pointer because the method should be static
					$instance = $method->invoke( false );
				}
			}
		}
		return $instance;
	}
	
	/**
	 * Allow a test to use a method that is normally not accessible
	 * @param string|stdObject $obj The name of the class or an instance of the class
	 * @param string $name The name of the method
	 * @return Method with public access
	 */
	public static function retrieveProperty($obj, $name) {
		if( !is_string( $obj ) ) {
			$obj = get_class( $obj );
		}
		$refl = new \ReflectionClass( $obj );
		$property = $refl->getProperty( $name );
		$property->setAccessible( true );
		return $property;
	}

}