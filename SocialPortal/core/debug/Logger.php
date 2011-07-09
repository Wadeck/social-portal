<?php

namespace core\debug;

use core\Config;

use core\FrontController;

use DateTimeZone;
class Logger {
	/** @var string */
	private $timeFormat;
	/** @var string */
	private $fileName;
	/** @var int */
	private $maxFileSize;
	/** @var DateTimeZone */
	private $dateTimeZone;
	/** @var resource */
	private $file;
	/** @var string that will be added to the date to avoid using new date everytime */
	private $timeZoneModification;
	
	/** @var Logger */
	private static $instance;
	
	public static function getInstance() {
		if( !self::$instance ) {
			self::$instance = new Logger();
		}
		return self::$instance;
	}
	
	private function __construct($filename = false) {
		$this->maxFileSize = Config::get('log_file_max_size', 16 * 1024);//16ko
		if(false === $filename){
			$filename = Config::get('log_file_name', 'log.txt');
		}
		$this->fileName = $filename;
		$this->timeFormat = Config::get('log_time_format', DATE_RFC822);
		
		$dateTimeZone = Config::get('timezone', 'Europe/Zurich');
		$this->dateTimeZone = new \DateTimeZone($dateTimeZone);
//		timezone_open($dateTimeZone)
		
		if( file_exists( $this->fileName ) && filesize( $this->fileName ) > $this->maxFileSize ) {
			// more than size
			$this->file = fopen( $this->fileName, 'w' );
		} else {
			$this->file = fopen( $this->fileName, 'a' );
		}
		
		$testDate = new \DateTime('now', $this->dateTimeZone);
		$this->timeZoneModification = $testDate->format('P');

		$this->log( 'start of session ' . $_SERVER['HTTP_USER_AGENT'] );
	}
	
	public function __destruct() {
		if( false !== $this->file ) {
			$this->debug( 'end of session' . "\n====================================\n" );
			fclose( $this->file );
			$this->file = false;
		}
	}
	
	public function log($message) {
		@fputs( $this->file, date( $this->timeFormat ) . $this->timeZoneModification . "\t" . $message . "\n" );
	}
	
	public function debug($message) {
		@fputs( $this->file, date( $this->timeFormat ) . $this->timeZoneModification . "\t[DEBUG]\t" . $message . "\n" );
	}
	
	public function log_var($message, $var) {
		ob_start();
		var_export( $var );
		$message .= ' = ' . ob_get_clean();
		@fputs( $this->file, date( $this->timeFormat ) . $this->timeZoneModification . "\t" . $message . "\n" );
	}
	
	public function debug_var($message, $var) {
		ob_start();
		var_export( $var );
		$message .= ' = ' . ob_get_clean();
		@fputs( $this->file, date( $this->timeFormat ) . $this->timeZoneModification . "\t[DEBUG]\t" . $message . "\n" );
	}
}

?>