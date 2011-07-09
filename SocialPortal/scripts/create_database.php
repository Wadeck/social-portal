<?php

$realWD = getcwd();
$currentWD = dirname( __FILE__ );
if( $realWD !== $currentWD ) {
	chdir( $currentWD );
}

require_once 'base_script.php';

use Doctrine\ORM\EntityManager;

ini_set( "display_errors", "On" );

if(isset($_SERVER['argv']) && isset($_SERVER['argv'][0])){
	// in cli mode
}else{
	// in webcall
	$_SERVER['argv'][] = __FILE__;
}
//used only to debug directly in eclipse (argument passing is not working)
$_SERVER['argv'][] = 'orm:schema-tool:update';
//$_SERVER['argv'][] = '--dump-sql';
$_SERVER['argv'][] = '--force';
//$_SERVER['argv'][] = 'orm:schema-tool:create';

$entityManager = EntityManager::create( $connectionOptions, $config );

$helperSet = new \Symfony\Component\Console\Helper\HelperSet( array( 'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper( $entityManager ) ) );

\Doctrine\ORM\Tools\Console\ConsoleRunner::run( $helperSet, false, $output, false );
$output->writeln( 'Generation of database done' );