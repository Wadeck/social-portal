<?php
namespace socialportal\repository;

use core\topics\TopicType;

use socialportal\model\TopicBase;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TopicRepository extends EntityRepository {
	/** @return TopicBase|false if not found */
	public function findBaseTopic($topicId) {
		$qb = $this->_em->createQueryBuilder();
		$qb->select( 't' )->from( 'socialportal\model\TopicBase', 't' )->where( 't.id = :id' )->setParameter( 'id', $topicId )->setMaxResults( 1 );
		$results = $qb->getQuery()->getResult();
		if( $results ) {
			return $results[0];
		} else {
			return false;
		}
	}
	
	/** @return TopicFull|false if not found */
	public function findFullTopic($topicId) {
		$topicBase = $this->findBaseTopic($topicId);
		if(!$topicBase){
			return false;
		}
		$customId = $topicBase->getCustomId(); 
		$customType = $topicBase->getCustomType(); 
		$customType = TopicType::translateTypeIdToName($customType);
	
		$qb = $this->_em->createQueryBuilder();
		$qb->select( 'ct' )->from( $customType, 'ct' )->where( 'ct.topicBase_id = :id' )->setParameter( 'id', $customId )->setMaxResults( 1 );
		$results = $qb->getQuery()->getResult();
		if( $results ) {
			$fullTopic = $results[0];
			$fullTopic->setTopicBase($topicBase);
			return $fullTopic;
		} else {
			return false;
		}
	}
}