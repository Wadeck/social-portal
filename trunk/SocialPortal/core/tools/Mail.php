<?php

namespace core\tools;

use core\Config;

class Mail {
	public static function send($to, $subject, $message, array $headers = array()) {
		$headers['From'] = Config::getOrDie('mail_from');
		$headersClean = implode( '\r\n', array_map( function ($key, $value) {
			return "$key:$value";
		}, array_keys( $headers ), array_values( $headers ) ) );
		
		$result = @mail( $to, $subject, $message, $headersClean );
		return $result;
	}
	
	public static function sendHtml($to, $subject, $message, array $headers = array()) {
//		@ini_set( 'SMTP', Config::getOrDie('mail_smtp') );
//		@ini_set( 'sendmail_from', Config::getOrDie('mail_from') );
		
		$headers['From'] = Config::getOrDie('mail_from');
		$headers['Content-type'] = "text/html; charset= iso-8859-1";
		$headers['Return-Path'] = "w.follonier@netunion.com";
		$headersClean = implode( '\r\n', array_map( function ($key, $value) {
			return "$key:$value";
		}, array_keys( $headers ), array_values( $headers ) ) );
		
		$result = @mail( $to, $subject, $message, $headersClean );
		
		@ini_restore( 'sendmail_from' );
		@ini_restore( 'SMTP' );
		
		return $result;
	}
}