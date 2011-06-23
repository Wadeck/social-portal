<?php

$realWD = getcwd();
$currentWD = dirname( __FILE__ );
if( $realWD !== $currentWD ) {
	chdir( $currentWD );
}

require_once 'base_script.php';

use Doctrine\ORM\Tools\EntityGenerator;

use Symfony\Component\Console\Output\EclipseOutput;

ini_set( "display_errors", "On" );

//used only to create annotations from database
$_SERVER['argv'][] = 'orm:convert-mapping';
$_SERVER['argv'][] = '--from-database';
$_SERVER['argv'][] = 'annotation';
$_SERVER['argv'][] = '.\mapping';

$em = \Doctrine\ORM\EntityManager::create( $connectionOptions, $config );
$em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping( 'set', 'string' );
$em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping( 'enum', 'string' );

// fetch metadata
$driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver( $em->getConnection()->getSchemaManager() );

$em->getConfiguration()->setMetadataDriverImpl( $driver );
$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory( 'socialportal\\model' );
$cmf->setEntityManager( $em ); // we must set the EntityManager


$classes = $driver->getAllClassNames();
$metadata = array();
foreach( $classes as $class ) {
	//any unsupported table/schema could be handled here to exclude some classes
	if( true ) {
		$metadata[] = $cmf->getMetadataFor( $class );
	}
}

$generator = new EntityGenerator();
$generator->setUpdateEntityIfExists( true ); // only update if class already exists
$generator->setRegenerateEntityIfExists( true ); // this will overwrite the existing classes
$generator->setGenerateStubMethods( true );
$generator->setGenerateAnnotations( true );
$generator->setRepositoryFolder( 'repository' );
$generator->generate( $metadata, TARGET );
$output->writeln( 'Generation of entities done' );