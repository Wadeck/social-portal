<?php

namespace socialportal\repository;

use socialportal\model\Token;

use core\tools\Utils;

use core\Config;

use socialportal\common\topic\TypeCenter;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class SubsetTopicRepository extends EntityRepository {
	/**
	 * @return array of SubsetTopic|false if the token is not found
	 */
	public function findTopicFromForum($forumId) {
		$dql = $this->_em->createQuery( 'SELECT st FROM SubsetTopic st WHERE st.forumId = :forumId' );
		$dql->setParameter('forumId', $forumId);
		$result = $dql->getResult();
		if( $result ) {
			return $result;
		} else {
			return false;
		}
	}
	
	public function isTopicInSubset($topicId){
		$dql = $this->_em->createQuery( 'SELECT st FROM SubsetTopic st WHERE st.topicId = :topicId' );
		$dql->setParameter('topicId', $topicId);
		$result = $dql->getResult();
		if( $result ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @return true if accessible | false otherwise
	 */
	public function isProfileLinkedWithSubset($userId){
		$dql = $this->_em->createQuery();
		$dql->setParameter('userId', $userId);
		
		// if the user has created a topic
		$dql->setDQL( 'SELECT st FROM SubsetTopic st, TopicBase tb WHERE (tb.id = st.topicId AND tb.poster = :userId AND tb.isDeleted = 0)' );
		$result = $dql->getResult();
		if($result){
			return true;
		}
		
		// if the user has created a post
		$dql->setDQL( 'SELECT st FROM SubsetTopic st, PostBase pb WHERE (pb.topic = st.topicId AND pb.isDeleted = 0 AND pb.poster = :userId)' );
		$result = $dql->getResult();
		if( $result ) {
			return true;
		} else {
			return false;
		}
	}
	
	public function regenerateSubsetForForum($forumId, array $topics){
		
	}
}