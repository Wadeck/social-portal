<?php

set_time_limit(180);

use Symfony\Component\Console\Output\EclipseOutput;
use Doctrine\ORM\Configuration;
define( 'TARGET', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'socialportal' . DIRECTORY_SEPARATOR . 'model' );

require_once '../core/ClassLoader.php';
core\ClassLoader::getInstance()->addMatch( 'socialportal' )->addMatch( 'Doctrine', 'lib' )->addMatch( 'Symfony', 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' )->addMatch( 'Proxy', DIRECTORY_SEPARATOR . 'socialportal' . DIRECTORY_SEPARATOR . 'proxy' )->addMatch( 'core' )->setRootDirectory( implode( DIRECTORY_SEPARATOR, array_slice( explode( DIRECTORY_SEPARATOR, getcwd() ), 0, -1 ) ) )->register();

$cache = new \Doctrine\Common\Cache\ArrayCache();

$config = new Configuration();
$config->setMetadataDriverImpl( $config->newDefaultAnnotationDriver( TARGET ) );
$config->setMetadataCacheImpl( $cache );
$config->setQueryCacheImpl( $cache );
$config->setProxyDir( __DIR__ .DIRECTORY_SEPARATOR.'socialportal'.DIRECTORY_SEPARATOR.'proxy' );
$config->setProxyNamespace( 'Proxy' );
$config->setAutoGenerateProxyClasses( true );

$connectionOptions = array( 'driver' => 'pdo_mysql', 'user' => 'doctrine_user', 'password' => 'doctrine_s3cr3t', 'dbname' => 'social_portal', 'host' => 'localhost', 'collation' => 'utf8_general_ci' );

$output = new EclipseOutput();

