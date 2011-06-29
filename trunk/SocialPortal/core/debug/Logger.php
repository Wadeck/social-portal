<?php

namespace core\debug;

//TODO give the filename, the maxfilesize from configuration
use core\FrontController;

class Logger {
	/** @var string */
	private $fileName;
	/** @var int */
	private $maxFileSize;
	/** @var Logger */
	private static $instance;
	public static function getInstance() {
		if( !self::$instance ) {
			self::$instance = new Logger();
		}
		return self::$instance;
	}
	
	/** @var resource */
	private $file;
	private function __construct() {
		$this->maxFileSize = 16 * 1024;
		$this->fileName = 'log.txt';
		if( file_exists( $this->fileName ) && filesize( $this->fileName ) > $this->maxFileSize ) {
			// more than 16ko
			$this->file = fopen( $this->fileName, 'w' );
		} else {
			$this->file = fopen( $this->fileName, 'a' );
		}
		if(!chmod($this->fileName, '0777')){
			echo 'Impossible to chmod the file';
			die();
		}
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
		@fputs( $this->file, date( DATE_RFC822 ) . "\t" . $message . "\n" );
	}
	
	public function debug($message) {
		@fputs( $this->file, date( DATE_RFC822 ) . "\t[DEBUG]\t" . $message . "\n" );
	}
	
	public function log_var($message, $var) {
		ob_start();
		var_export( $var );
		$message .= ' = ' . ob_get_clean();
		@fputs( $this->file, date( DATE_RFC822 ) . "\t" . $message . "\n" );
	}
	
	public function debug_var($message, $var) {
		ob_start();
		var_export( $var );
		$message .= ' = ' . ob_get_clean();
		@fputs( $this->file, date( DATE_RFC822 ) . "\t[DEBUG]\t" . $message . "\n" );
	}
}

?>