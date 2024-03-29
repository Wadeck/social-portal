<?php

namespace core;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Configuration;

/** Configuration class */
class DoctrineLink {
	//TODO information from configuration file could be better but there could be a problem with directory_separator
	public static function getEntityManager() {
		$config = new Configuration();
		$driverImpl = $config->newDefaultAnnotationDriver( 'socialportal'.DIRECTORY_SEPARATOR.'model' );
		$config->setMetadataDriverImpl( $driverImpl );
		$config->setProxyDir( 'socialportal'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'proxies' );
		$config->setDefaultEntityNamespace( 'socialportal\\model' );
		$config->setProxyNamespace( 'socialportal\\model\\proxies' );
		if( true === DEBUG ) {
			$cache = new \Doctrine\Common\Cache\ArrayCache();
			$config->setAutoGenerateProxyClasses( true );
		} else {
			$cache = new \Doctrine\Common\Cache\ApcCache();
			$config->setAutoGenerateProxyClasses( false );
		}
		$config->setMetadataCacheImpl( $cache );
		$config->setQueryCacheImpl( $cache );
		
		$connectionOptions = array( 'driver' => 'pdo_mysql', 'user' => 'doctrine_user', 'password' => 'doctrine_s3cr3t', 'dbname' => 'social_portal', 'host' => 'localhost', 'collation' => 'utf8_general_ci' );
		
		$em = EntityManager::create( $connectionOptions, $config );
		$em->setDefaultNamespace( 'socialportal\\model\\' );
		$em->setRepositoryNamespace( 'socialportal\\repository\\' );
		return $em;
	}
}