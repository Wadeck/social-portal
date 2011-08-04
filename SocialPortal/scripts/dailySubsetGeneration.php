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
		
$now = new \DateTime('now');
$em=DoctrineLink::getEntityManager();		
$subsetRepo = $em->getRepository( 'SubsetTopic' );
$forumRep = $em->getRepository( 'Forum' );		
$forums = $forumRep->findAll();
foreach($forums as $f){
	list($total, $topics) = simulateVoteForForum( $f->getId() );
	$selectedTopics = chooseRandomly($topics, $total, 5);
	
	$previouslySelected = $subsetRepo->findTopicFromForum( $f->getId() );
	if (!$previouslySelected){continue;}
	foreach($previouslySelected as $ps){
		$em->remove($ps);
	}
	foreach($selectedTopics as $st){
		$subset = new SubsetTopic();
		$subset->setTopicId($st->getId());
		$subset->setForumId($f->getId());
		$subset->setExpirationDate($now);
		$em->persist($subset);
	}
}
$result = $em->flushSafe();

/***************Functions*******************************************/
/**
	 * @return array [weight, item]
	 */
	function simulateVoteForForum($forumId){
		$em=DoctrineLink::getEntityManager();
		$allTopics = $em->getRepository('TopicBase')->findTopicsFromForum($forumId);
		$voteRepo = $em->getRepository('TopicVoteStats');
		
		$total = 0;
		// array of [voteRelative, $topic]
		$topics = array();
		
		foreach($allTopics as $topic){
			$voteRelative = $voteRepo->getRelativeVote($topic->getId());
			$total += $voteRelative;
			$topics[] = array('weight' => $voteRelative, 'item' => $topic);
		}

		return array($total, $topics);
	}
	/**
	 * Choose randomly the topics in function of their weight
	 * @param array of topics $topics
	 * @param int $total
	 * @param int $numResult
	 * @long processing time
	 */
	function chooseRandomly(array $topics, $total, $numResult = 5){
		if( $numResult <= 0 || empty($topics) ){
			return array();
		}
		$size = count($topics);
		if( $size <= $numResult ){
			$results = array_map(function($val){
				return $val['item'];
			}, $topics);
			return $results;
		}
		// here is the real algorithm
		$result = array();
		$count = 0;
		$variableTotal = $total;
		for($i = 0 ; $i < $numResult ; $i++){
			$value = mt_rand(1, $variableTotal);
			$index = retrieveWeightedItem($topics, $value);
			$current = $topics[$index];
			$variableTotal -= $current['weight'];
			$result[] = $current['item'];
			array_splice($topics, $index, 1);
		}
		
		return $result;
	}
	/**
	 * Determine in function of the weights which element is selected
	 * if we have [3, item1], [2, item2], the total is 5, the range is [1, 5]
	 * The mapping: item1 = [1,2,3], item2 = [4, 5]
	 * 
	 * @param array $topics
	 * @param int $value
	 * @return int $index of the element in the array
	 */
	function retrieveWeightedItem(array $topics, $value){
		$currentValue = $value;
		for ($i = 0, $size = count($topics) ; $i < $size ; $i++){
			$topic = $topics[$i];
			$currentValue -= $topic['weight'];
			if( $currentValue <= 0 ){
				return $i;
			}
		}
		throw new \Exception('Problem in value weighted random choice');
	}
?>