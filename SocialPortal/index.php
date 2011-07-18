<?php
define( 'DEBUG', true );
// do no edit those if condition tags, used in parser to make the page target the maintenance page
// to disable the maintenance page, simply change true to false, without modifying the tags
if(/*%s%*/false/*%e%*/){
	require 'maintenance.php';
}else{
	require 'core/ClassLoader.php';
	core\ClassLoader::getInstance()->addMatch( 'core' )->addMatch( 'socialportal' )->addMatch( 'Doctrine', 'lib' )->register();
	
	// to compute the time taken to load the page
	\core\debug\Benchmark::start();
	core\FrontController::getInstance()->dispatch();
}