<?php

namespace core;
use Exception;
class Config{
	/** @var Config */
	public static $instance = null;
	
	public function __construct(){
		if(self::$instance){
			throw new Exception('Configuration already loaded');
		}
		self::$instance = $this;
		
		$this->init();
	}
	
	private function init(){
		$this->CONTROLLER_DIR = 'socialportal' . '\\' . 'controller' . '\\';
		$this->VIEW_DIR = 'socialportal' . '\\' . 'view' . '\\';
//			self::$CONTROLLER_DIR = 'socialportal' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR;
//			self::$VIEW_DIR = 'socialportal' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
		$this->JS_DIR = 'socialportal/resources/js/';
		$this->IMG_DIR = 'socialportal/resources/img/';
		$this->AVATAR_DIR = $this->IMG_DIR . 'avatars/';
		$this->TEMP_DIR = $this->IMG_DIR . 'temp/';
		$this->CSS_DIR = 'socialportal/resources/css/';
	}
	
	// front controller variable
	public $JS_DIR = null;
	public $IMG_DIR = null;
	public $TEMP_DIR = null;
	public $AVATAR_DIR = null;
	public $CSS_DIR = null;
	public $CONTROLLER_DIR = null;
	public $VIEW_DIR = null;
	public $SITE_NAME = 'SocialPortal';
	
	// controller Profile
	public $MAX_AVATAR_FILE_SIZE = 2560000 ; /* 2.5mb */
	
}