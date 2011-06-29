<?php

namespace core\tools;

use core\FrontController;

class Utils {
	public static function getValidDate($value) {
		if( is_numeric( $value ) ) {
			$timestamp = $value;
		} else {
			$timestamp = strtotime( $value );
		}
		if( !$timestamp ) {
			$timestamp = time();
		}
		return date( 'Y-m-d h:i:s', $timestamp );
	}
	
	public static function getCleanText($text) {
		$temp = $text;
		$text = trim( strip_tags( stripcslashes( $text ) ) );
		return $text;
	}
	
	/**
	 * 
	 * @param method $func
	 * @param array $args of params
	 */
	public static function arrayToParamsMethod($func, $obj, $args = false) {
		if( !($func instanceof \ReflectionMethod) ) {
			return false;
		}
		if( !$args ) {
			return $func->invoke( $obj );
		}
		if( !is_array( $args ) ) {
			return $func->invoke( $obj, $args );
		}
		$count = count( $args );
		switch ( $count ) {
			case 1 :
				return $func->invoke( $obj, $args[0] );
			case 2 :
				return $func->invoke( $obj, $args[0], $args[1] );
			case 3 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2] );
			case 4 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3] );
			case 5 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3], $args[4] );
			case 6 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5] );
			case 7 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6] );
			case 8 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7] );
			case 9 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8] );
			case 10 :
				return $func->invoke( $obj, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9] );
			default :
				return new \ErrorException( 'Too many arguments: ' . $count );
		}
	}
	
	/**
	 * bp_core_time_since()
	 *
	 * Based on function created by Dunstan Orchard - http://1976design.com
	 *
	 * This function will return an English representation of the time elapsed
	 * since a given date.
	 * eg: 2 hours and 50 minutes
	 * eg: 4 days
	 * eg: 4 weeks and 6 days
	 *
	 * @package BuddyPress Core
	 * @param $older_date int Unix timestamp of date you want to calculate the time since for
	 * @param $newer_date int Unix timestamp of date to compare older date to. Default false (current time).
	 * @return str The time since.
	 */
	public static function getDataSince($older_date, $newer_date = null, $singleChunk = true) {
		if( $older_date instanceof \DateTime ) {
			$older_date = $older_date->getTimestamp();
		}
		// array of time period chunks
		$chunks = array( array( 60 * 60 * 24 * 365, __( 'year' ), __( 'years' ) ), array( 60 * 60 * 24 * 30, __( 'month' ), __( 'months' ) ), array( 60 * 60 * 24 * 7, __( 'week' ), __( 'weeks' ) ), array( 60 * 60 * 24, __( 'day' ), __( 'days' ) ), array( 60 * 60, __( 'hour' ), __( 'hours' ) ), array( 60, __( 'minute' ), __( 'minutes' ) ), array( 1, __( 'second' ), __( 'seconds' ) ) );
		
		if( !is_numeric( $older_date ) ) {
			$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
			$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
			
			$older_date = gmmktime( ( int ) $time_chunks[1], ( int ) $time_chunks[2], ( int ) $time_chunks[3], ( int ) $date_chunks[1], ( int ) $date_chunks[2], ( int ) $date_chunks[0] );
		}
		
		$newer_date = $newer_date ? $newer_date : FrontController::getInstance()->getRequest()->getRequestTime();
		/* Difference in seconds */
		$since = $newer_date - $older_date;
		
		/* Something went wrong with date calculation and we ended up with a negative date. */
		if( 0 > $since ) {
			return __( 'sometime' );
		} else if( 0 == $since ) {
			return __( 'now' );
		}
		/**
		 * We only want to output two chunks of time here, eg:
		 * x years, xx months
		 * x days, xx hours
		 * so there's only two bits of calculation below:
		 */
		
		/* Step one: the first chunk */
		for( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
			$seconds = $chunks[$i][0];
			
			/* Finding the biggest chunk (if the chunk fits, break) */
			if( ($count = floor( $since / $seconds )) != 0 )
				break;
		}
		
		/* Set output var */
		$output = (1 == $count) ? '1 ' . $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
		
		if( !$singleChunk ) {
			/* Step two: the second chunk */
			if( $i + 2 < $j ) {
				$seconds2 = $chunks[$i + 1][0];
				$name2 = $chunks[$i + 1][1];
				
				if( ($count2 = floor( ($since - ($seconds * $count)) / $seconds2 )) != 0 ) {
					/* Add to output var */
					//				$output .= (1 == $count2) ? _x( ',', 'Separator in time since', 'buddypress' ) . ' 1 ' . $chunks[$i + 1][1] : _x( ',', 'Separator in time since', 'buddypress' ) . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
					$output .= (1 == $count2) ? ', 1 ' . $chunks[$i + 1][1] : ', ' . $count2 . ' ' . $chunks[$i + 1][2];
				}
			}
		}
		if( !( int ) trim( $output ) )
			$output = '0 ' . __( 'seconds' );
		
		return $output;
	}
	
	public static function createExcerpt($text, $size) {
		if( strlen( $text ) > $size + 2 ) {
			$text = substr( $text, 0, $size ) . '...';
		}
		return $text;
	}
	
	/**
	 * @return true iff the protocol used is HTTPS, otherwise false
	 */
	public static function isSSL() {
		if( isset( $_SERVER['HTTPS'] ) ) {
			if( 'on' == strtolower( $_SERVER['HTTPS'] ) )
				return true;
			if( '1' == $_SERVER['HTTPS'] )
				return true;
		} elseif( isset( $_SERVER['SERVER_PORT'] ) && ('443' == $_SERVER['SERVER_PORT']) ) {
			return true;
		}
		return false;
	}

}