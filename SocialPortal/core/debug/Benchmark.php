<?php

namespace core\debug;

class Benchmark {
	private static $prev = false;
	public static function set_no_timeout() {
		set_time_limit( 0 );
	}
	public static function start() {
		self::$prev = self::_getMicrotime();
	}
	public static function stop() {
		if( self::$prev === false ) {
			return;
		}
		$total_time = round( (self::_getMicrotime() - self::$prev) * 1000 );
		self::$prev = false;
		return $total_time;
	}
	
	//source: http://www.lepotlatch.org/2007/03/67-php-benchmark-sql/
	private static function _getMicrotime() {
		// découpe le tableau de microsecondes selon les espaces
		list( $usec, $sec ) = explode( " ", microtime() );
		// replace dans l'ordre
		return (( float ) $usec + ( float ) $sec);
	}
}
?>