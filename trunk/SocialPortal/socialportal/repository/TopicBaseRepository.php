<?php
namespace socialportal\repository;

use core\tools\TopicType;

use socialportal\model\TopicBase;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TopicBaseRepository extends EntityRepository {
	/** @return TopicBase|false if not found */
	public function findBaseTopic($topicId) {
		$dql = $this->_em->createQuery( 'SELECT t FROM TopicBase t WHERE t.id = :id' );
		$dql->setMaxResults( 1 )->setParameter( 'id', $topicId );
		$result = $dql->getSingleResult();
		//		$qb = $this->_em->createQueryBuilder();
		//		$qb->select( 't' )->from( 'socialportal\model\TopicBase', 't' )->where( 't.id = :id' )->setParameter( 'id', $topicId )->setMaxResults( 1 );
		//		$results = $qb->getQuery()->getResult();
		if( $result ) {
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * @param int $forumId
	 * @param int $page_num Must be >= 1
	 * @param int $num_per_page
	 * @return array of TopicBase
	 */
	public function findTopicsFromForum($forumId, $page_num = 1, $num_per_page = false) {
		$dql = $this->_em->createQuery( 'SELECT t FROM TopicBase t WHERE t.forum = :id AND t.isDeleted=0 ORDER BY t.time DESC' );
		$dql->setParameter( 'id', $forumId );
		if( false !== $num_per_page ) {
			$offset = ($page_num - 1) * $num_per_page;
			$dql->setFirstResult( $offset )->setMaxResults( $num_per_page );
		}
		$topics = $dql->getResult();
		return $topics;
	}
	
	/** @return TopicFull|false if not found */
	public function findFullTopic($topicId) {
		$topicBase = $this->findBaseTopic( $topicId );
		if( !$topicBase ) {
			return false;
		}
		$customType = $topicBase->getCustomType();
		$customType = TopicType::translateTypeIdToName( $customType );
		
		$dql = $this->_em->createQuery( "SELECT ct FROM $customType ct WHERE ct.topicbase = :id" );
		$dql->setParameter( 'id', $topicId )->setMaxResults( 1 );
		$result = $dql->getSingleResult();
		
		//		$qb = $this->_em->createQueryBuilder();
		//		$qb->select( 'ct' )->from( $customType, 'ct' )->where( 'ct.topicbase = :id' )->setParameter( 'id', $topicId )->setMaxResults( 1 );
		//		$results = $qb->getQuery()->getResult();
		if( $result ) {
			$fullTopic = $result;
			$fullTopic->setTopicbase( $topicBase );
			return $fullTopic;
		} else {
			return false;
		}
	}
	
	/** Warning, after this call the topic entity must be reloaded to have the correct value */
	public function incrementPostCount($topicId, $num = 1) {
		$qb = $this->_em->createQueryBuilder();
		$qb->update( 'socialportal\model\TopicBase', 't' )->set( 't.numPosts', 't.numPosts+?1' )->where( 't.id = ?2' )->setParameter( 1, $num )->setParameter( 2, $topicId );
		$q = $qb->getQuery();
		$p = $q->execute();
	}

	/**
	 * Used to retrieve the page number where the post will be displayed, given the num_per_page parameters
	 * @param int $topicId
	 * @param int $topicTypeId
	 * @param int $topicPosition
	 * @param int $num_per_page
	 */
	public function getPostPagePerPosition($topicId, $topicTypeId, $topicPosition, $num_per_page) {
		$customType = TopicType::translateTypeIdToPostName( $topicTypeId );
		
		$dql = $this->_em->createQuery( "SELECT COUNT(ct.id) FROM $customType ct JOIN ct.postbase p WHERE p.topic = :id AND p.isDeleted = 0 AND p.position < :pos" );
		$dql->setParameter( 'id', $topicId );
		$dql->setParameter( 'pos', $topicPosition );
		$totalBefore = $dql->getSingleScalarResult();
		// to count the given posts
		$totalBefore += 1;
		$totalPage = ceil($totalBefore / $num_per_page);
		return $totalPage;
	}
	/**
	 * Used to retrieve the page number where the post will be displayed, given the num_per_page parameters
	 * @param int $topicId
	 * @param int $topicTypeId
	 * @param DateTime $topicTime 
	 * @param int $num_per_page
	 */
	public function getPostPagePerTime($topicId, $topicTypeId, $topicTime, $num_per_page) {
		$customType = TopicType::translateTypeIdToPostName( $topicTypeId );
		
		$dql = $this->_em->createQuery( "SELECT COUNT(ct.id) FROM $customType ct JOIN ct.postbase p WHERE p.topic = :id AND p.isDeleted = 0 AND p.time < :time" );
		$dql->setParameter( 'id', $topicId );
		$dql->setParameter( 'time', $topicTime, \Doctrine\DBAL\Types\Type::DATETIME  );
		$totalBefore = $dql->getSingleScalarResult();
		// to count the given posts
		$totalBefore += 1;
		$totalPage = ceil($totalBefore / $num_per_page);
		return $totalPage;
	}
	
	/**
	 * Used to retrieve the last page number
	 * @param int $topicId
	 * @param int $topicTypeId
	 * @param int $num_per_page
	 * @deprecated Use Topic#getNumPosts instead
	 */
	public function getLastPage($topicId, $topicTypeId, $num_per_page) {
		$customType = TopicType::translateTypeIdToPostName( $topicTypeId );
		
		$dql = $this->_em->createQuery( "SELECT COUNT(ct.id) FROM $customType ct JOIN ct.postbase p WHERE p.topic = :id AND p.isDeleted = 0" );
		$dql->setParameter( 'id', $topicId );
		$total = $dql->getSingleScalarResult();
		
		$totalPage = ceil($total / $num_per_page);
		
		return $totalPage;
	}
}