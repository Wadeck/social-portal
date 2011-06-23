<?php

$realWD = getcwd();
$currentWD = dirname( __FILE__ );
if( $realWD !== $currentWD ) {
	chdir( $currentWD );
}

require_once 'base_script.php';

use Doctrine\ORM\EntityManager;

ini_set( "display_errors", "On" );

//used only to debug directly in eclipse (argument passing is not working)
$_SERVER['argv'][] = 'orm:schema-tool:create';

$entityManager = EntityManager::create( $connectionOptions, $config );

$helperSet = new \Symfony\Component\Console\Helper\HelperSet( array( 'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper( $entityManager ) ) );

\Doctrine\ORM\Tools\Console\ConsoleRunner::run( $helperSet, false, $output, false );
$output->writeln( 'Generation of database done' );