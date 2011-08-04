<?php
chdir("C:\wamp\www\SocialPortal");
//Linux
//chdir("/var/www/SocialPortal");

use core\DoctrineLink;
use socialPortal\model\SubsetTopic;

//debug constant (don't setup to false because of cache use)
define('DEBUG',TRUE);

//Linux: require_once 'core/ClassLoader.php';
require_once 'core\ClassLoader.php';
core\ClassLoader::getInstance()->addMatch( 'socialportal' )->addMatch( 'Doctrine', 'lib' )->addMatch( 'Symfony', 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' )->addMatch( 'Proxy', DIRECTORY_SEPARATOR . 'socialportal' . DIRECTORY_SEPARATOR . 'proxy' )->addMatch( 'core' )->setRootDirectory( getcwd() )->register();

$em=DoctrineLink::getEntityManager();
$result = $em->getRepository('TopicVoteStats')->relativeReductionAll();

