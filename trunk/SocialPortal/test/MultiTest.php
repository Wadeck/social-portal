<?php

require_once 'PHPUnit\Framework\TestSuite.php';

require_once 'core\ClassLoader.php';
core\ClassLoader::getInstance()->addMatch( 'socialportal' )->addMatch( 'Doctrine', 'lib' )->addMatch( 'core' )->addDefaultMatch( 
		'test' )->setRootDirectory( 
		implode( DIRECTORY_SEPARATOR, array_slice( explode( DIRECTORY_SEPARATOR, getcwd() ), 0, -1 ) ) )->register();

define('DEBUG', true);
		
/**
 * Static test suite.
 */
class MultiTest extends PHPUnit_Framework_TestSuite {
	private $tempArray = array();
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName( get_class( $this ) );
		$this->getAllTest();
	
		//		$this->addTest( new CryptoTest() );
	

	}
	
	private function getAllTest() {
		$current_dir = getcwd();
		$dirname = __DIR__;
		$files = scandir( $dirname );
		chdir( $dirname );
		foreach( $files as $file ) {
			$this->manageFile( $file, $dirname );
		}

		krsort($this->tempArray, SORT_NUMERIC );
		
		foreach ($this->tempArray as $key=>$value){
			$this->addTestSuite($value);
		}
		
		chdir( $current_dir );
	}
	
	function manageFile($file, $dirname) {
		if( false !== ($index = strpos( $file, 'Test.php' )) ) {
			$short_name = substr( $file, 0, $index + 4 );
			if( $this->getName() === $short_name ) {
				return;
			}
			$this->tempArray[filemtime($file)] = $short_name;
		}
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self();
	}
}

