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
	 * @return Topics|false if the token is not found
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
	
	public function isProfileLinkedWithSubset(){
		$dql1 = $this->_em->createQuery( 'SELECT st FROM SubsetTopic st JOIN PostBase p WHERE p.topicBase = st.topicId' );
		$result1 = $dql1->getResult();
		$dql = $this->_em->createQuery( 'SELECT st FROM SubsetTopic st JOIN PostBase p WHERE p.topicBase = st.topicId JOIN User u WHERE u = p.poster' );
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