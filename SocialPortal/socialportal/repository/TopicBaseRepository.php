<?php
namespace socialportal\repository;

use core\topics\TopicType;

use socialportal\model\TopicBase;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TopicBaseRepository extends EntityRepository {
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
	
	/** @return array of TopicBase */
	public function findTopicsFromForum($forumId, $page_num, $num_per_page) {
		$offset = ($page_num - 1) * $num_per_page;
		$qb = $this->_em->createQueryBuilder();
		$qb->select( 't' )->from( 'socialportal\model\TopicBase', 't' )->where( 't.forum = :id' )->setParameter( 'id', $forumId )->setFirstResult( $offset )->setMaxResults( $num_per_page );
		$topics = $qb->getQuery()->getResult();
		return $topics;
	}
	
	/** @return TopicFull|false if not found */
	public function findFullTopic($topicId) {
		$topicBase = $this->findBaseTopic( $topicId );
		if( !$topicBase ) {
			return false;
		}
		$customId = $topicBase->getCustomId();
		$customType = $topicBase->getCustomType();
		$customType = TopicType::translateTypeIdToName( $customType );
		
		$qb = $this->_em->createQueryBuilder();
		//TODO should be a problem here !
		$qb->select( 'ct' )->from( $customType, 'ct' )->where( 'ct.topicBase_id = :id' )->setParameter( 'id', $customId )->setMaxResults( 1 );
		$results = $qb->getQuery()->getResult();
		if( $results ) {
			$fullTopic = $results[0];
			$fullTopic->setTopicBase( $topicBase );
			return $fullTopic;
		} else {
			return false;
		}
	}
	
	/** Warning, after this call the topic entity must be reloaded to have the correct value */
	public function incrementPostCount($topicId, $num = 1) {
		$qb = $this->_em->createQueryBuilder();
		$qb->update( 'socialportal\model\TopicBase', 't' )->set( 't.numPosts', 't.numPosts+?1' )->where( 'f.id = ?2' )->setParameter( 1, $num )->setParameter( 2, $topicId );
		$q = $qb->getQuery();
		$p = $q->execute();
	}
}