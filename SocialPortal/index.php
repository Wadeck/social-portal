<?php
define('DEBUG', true);

//require 'core/FrontController.php';

require 'core/ClassLoader.php';
core\ClassLoader::getInstance()
->addMatch('core')
->addMatch('socialportal')
->addMatch('Doctrine', 'lib')
->register();

// to compute the time taken to load the page
\core\debug\Benchmark::start();
core\FrontController::getInstance()->dispatch();
