<?php

namespace socialportal\repository;

use socialportal\model\TopicVoteStats;

use socialportal\model\Token;

use core\tools\Utils;

use core\Config;

use socialportal\common\topic\TypeCenter;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TopicVoteStatsRepository extends EntityRepository {
	/**
	 * 
	 * @return TopicVoteStats|false if the vote is not found
	 */
	public function findStatsByTopicId($topicId) {
		$dql = $this->_em->createQuery( 'SELECT t FROM TopicVoteStats t WHERE t.topicId = :id');
		$dql->setParameter( 'id', $topicId );
		$dql->setMaxResults( 1 );
		$result = $dql->getResult();
		if( $result ) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/**
	 * @param int $topicId
	 * @return int the number of vote for a given topic
	 */
	public function getRelativeVote($topicId){
		$vote = $this->findStatsByTopicId($topicId);
		if( false === $vote ){
			return 0;
		}else{
			return $vote->getVoteRelative();
		}
	}
	
	/**
	 * 
	 * @return TopicVoteStats|false if the token is not found
	 */
	public function findStatsById($voteId) {
		$dql = $this->_em->createQuery( 'SELECT t FROM TopicVoteStats t WHERE t.id = :id');
		$dql->setParameter( 'id', $voteId );
		$dql->setMaxResults( 1 );
		$result = $dql->getResult();
		if( $result ) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/**
	 * Used when we want to increment to vote counter atomically
	 * @flush
	 */
	public function incrementVote($topicId, $amount = 1){
		$voteStats = $this->findStatsByTopicId($topicId);
		if( false === $voteStats ){
			$voteStats = new TopicVoteStats();
			$voteStats->setTopicId($topicId);
			$voteStats->setVoteTotal(1);
			$voteStats->setVoteRelative(1);
			$this->_em->persist( $voteStats );
			$result = $this->_em->flushSafe( $voteStats );
			return $result;
		}else{
			$dql = $this->_em->createQuery( 'UPDATE TopicVoteStats t SET t.voteTotal = t.voteTotal+:num, t.voteRelative = t.voteRelative+:num WHERE t.topicId = :id' );
			$dql->setParameter( 'id', $topicId );
			$dql->setParameter( 'num', $amount );
			return $dql->execute();
		}
	}
	
	/**
	 * Used ideally by the cron task once a week
	 * @flush
	 */
	public function relativeReduction($topicId, $ratio = 0.9){
		$voteStats = $this->findStatsByTopicId($topicId);
		if( false !== $voteStats ){
			$relative = $voteStats->getVoteRelative();
			$newRelative = (integer)($relative * $ratio);
			if($newRelative === $relative){
				// decrease at least by one
				$relative = max(0, $relative - 1);
			}
			$voteStats->setVoteRelative($relative);
			$this->_em->persist( $voteStats );
			$result = $this->_em->flushSafe( );
			return $result;
		}else{
			return true;
		}
	}
	
	public function relativeReductionAll($ratio = 0.9){
		$voteStats = $this->findAll();
		foreach ($voteStats as $vs){
			$relative = $vs->getVoteRelative();
			$newRelative = ( integer )( $relative * $ratio );
			// this never happens because the (integer) makes a ceil
//			if($newRelative === $relative){
//				// decrease at least by one
//				$relative = max(0, $relative - 1);
//			}
			$vs->setVoteRelative( $newRelative );
			$this->_em->persist( $vs );
		}
		$result = $this->_em->flushSafe( );
		return $result;
	}
}