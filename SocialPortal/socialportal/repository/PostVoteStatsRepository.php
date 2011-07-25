<?php

namespace socialportal\repository;
use socialportal\model\PostBase;

use core\tools\MathUtils;

use socialportal\model\PostVoteStats;

use socialportal\model\Token;

use core\tools\Utils;

use core\Config;

use socialportal\common\topic\TypeCenter;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class PostVoteStatsRepository extends EntityRepository {
	/**
	 * 
	 * @return PostVoteStats|false if the vote is not found
	 */
	public function findStatsByPostId($postId) {
		$dql = $this->_em->createQuery( 'SELECT p FROM PostVoteStats p WHERE p.postId = :id');
		$dql->setParameter( 'id', $postId );
		$dql->setMaxResults( 1 );
		$result = $dql->getResult();
		if( $result ) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * @return PostVoteStats|false if the vote is not found
	 */
	public function findStatsById($voteId) {
		$dql = $this->_em->createQuery( 'SELECT p FROM PostVoteStats p WHERE p.id = :id');
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
	public function incrementVote($topicId, $postId, $amount = 1){
		$voteStats = $this->findStatsByPostId($postId);
		if( false === $voteStats ){
			$voteStats = new PostVoteStats();
			$voteStats->setTopicId($topicId);
			$voteStats->setPostId($postId);
			$voteStats->setVoteTotal(1);
			$this->_em->persist( $voteStats );
			$result = $this->_em->flushSafe( $voteStats );
			return $result;
		}else{
			// TODO make a bench to be sure that is really the case
			// we filter first by the topic, then by the post, both are indices
			$dql = $this->_em->createQuery( 'UPDATE PostVoteStats p SET p.voteTotal = p.voteTotal+:num WHERE p.topicId = :topicId AND p.postId = :postId' );
			$dql->setParameter( 'topicId', $topicId );
			$dql->setParameter( 'postId', $postId );
			$dql->setParameter( 'num', $amount );
			return $dql->execute();
		}
	}
	
	/**
	 * Used by the post template to show only the best answer when the number of answers is large
	 * Should not be called when the number of answer is small
	 * @param $topicId
	 * @param $typeId
	 * @param $max [1, 100] the number of results desired
	 * @return array of PostBase, could be empty
	 */
	public function findBestPosts($topicId, $typeId, $max){
		$max = MathUtils::clamp($max, 1, 100);
		$typeManager = TypeCenter::getTypeManager($typeId);
		$customPostClass = $typeManager->getPostClassName();
		
		$dql = $this->_em->createQuery( "SELECT ct, pb FROM PostVoteStats pvs, $customPostClass ct JOIN ct.postbase pb WHERE pb.id = pvs.postId AND pvs.topicId = :id AND pb.isDeleted = 0 ORDER BY pvs.voteTotal DESC");
		$dql->setParameter( 'id', $topicId );
		$dql->setMaxResults( $max );
		$result = $dql->getResult();
		
		if( $result ) {
			return $result;
		} else {
			return array();
		}
	}
}