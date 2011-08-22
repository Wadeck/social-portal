<?php
namespace socialportal\repository;

use socialportal\common\topic\TypeCenter;

use socialportal\model\TopicBase;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TopicBaseRepository extends EntityRepository {
	/** @return the total number of post, included the deleted ones */
	public function getCountWithDeleted($topicId) {
		$query = $this->_em->createQuery( 'SELECT COUNT(p.id) FROM PostBase p WHERE p.topic = :tid' );
		$query->setParameter( 'tid', $topicId );
		$count = $query->getSingleScalarResult();
		return $count;
	}
	
	/** @return TopicBase|false if not found */
	public function findBaseTopic($topicId) {
		$dql = $this->_em->createQuery( 'SELECT t FROM TopicBase t WHERE t.id = :id' );
		$dql->setMaxResults( 1 )->setParameter( 'id', $topicId );
		$result = $dql->getSingleResult();
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
	 * @param boolean $withDeleted If we want to remove the deleted topics or not
	 * @return array of TopicBase
	 */
	public function findTopicsFromForum($forumId, $page_num = 1, $num_per_page = false, $withDeleted = false) {
		if( $withDeleted ) {
			$dql = $this->_em->createQuery( 'SELECT t FROM TopicBase t WHERE t.forum = :id ORDER BY t.time DESC' );
		} else {
			$dql = $this->_em->createQuery( 'SELECT t FROM TopicBase t WHERE t.forum = :id AND t.isDeleted=0 ORDER BY t.time DESC' );
		}
		$dql->setParameter( 'id', $forumId );
		if( false !== $num_per_page ) {
			$offset = ($page_num - 1) * $num_per_page;
			$dql->setFirstResult( $offset )->setMaxResults( $num_per_page );
		}
		$topics = $dql->getResult();
		return $topics;
	}
	
	/**
	 * Retrieve the topics that are sticky
	 * @param int $forumId
	 * @return array of TopicBase
	 */
	public function findStickyTopicsFromForum($forumId){
		$dql = $this->_em->createQuery( 'SELECT t FROM TopicBase t WHERE t.forum = :id AND t.isSticky=1 ORDER BY t.time DESC' );
		$dql->setParameter( 'id', $forumId );
		$stickyTopics = $dql->getResult();
		return $stickyTopics;
	}
	
	/** @return TopicFull|false if not found */
	public function findFullTopic($topicId) {
		$topicBase = $this->findBaseTopic( $topicId );
		if( !$topicBase ) {
			return false;
		}
		$typeId = $topicBase->getCustomType();
		$typeManager = TypeCenter::getTypeManager($typeId);
		$customTopicClass = $typeManager->getTopicClassName();
		
		$dql = $this->_em->createQuery( "SELECT ct FROM $customTopicClass ct WHERE ct.topicbase = :id" );
		$dql->setParameter( 'id', $topicId )->setMaxResults( 1 );
		$result = $dql->getSingleResult();
		
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
		//TODO transform into createQuery directly
		$qb->update( 'socialportal\model\TopicBase', 't' )->set( 't.numPosts', 't.numPosts+?1' )->where( 't.id = ?2' )->setParameter( 1, $num )->setParameter( 2, $topicId );
		$q = $qb->getQuery();
		$p = $q->execute();
	}
	
	/**
	 * Used to retrieve the page number where the post will be displayed, given the num_per_page parameters
	 * @param int $topicId
	 * @param int $typeId
	 * @param int $postPosition
	 * @param int $num_per_page
	 * @param boolean $withDeleted If we want to remove the deleted topics or not
	 */
	public function getPostPagePerPosition($topicId, $typeId, $postPosition, $num_per_page, $withDeleted = false) {
		$typeManager = TypeCenter::getTypeManager($typeId);
		$customPostClass = $typeManager->getPostClassName();
		if($withDeleted){
			$dql = $this->_em->createQuery( "SELECT COUNT(cp.id) FROM $customPostClass cp JOIN cp.postbase p WHERE p.topic = :id AND p.position < :pos" );
		}else{
			$dql = $this->_em->createQuery( "SELECT COUNT(cp.id) FROM $customPostClass cp JOIN cp.postbase p WHERE p.topic = :id AND p.isDeleted = 0 AND p.position < :pos" );
		}
		$dql->setParameter( 'id', $topicId );
		$dql->setParameter( 'pos', $postPosition );
		$totalBefore = $dql->getSingleScalarResult();
		// to count the given posts
		$totalBefore += 1;
		$totalPage = ceil( $totalBefore / $num_per_page );
		return $totalPage;
	}
	/**
	 * Used to retrieve the page number where the post will be displayed, given the num_per_page parameters
	 * @param int $topicId
	 * @param int $typeId
	 * @param DateTime $topicTime 
	 * @param int $num_per_page
	 * @param boolean $withDeleted If we want to remove the deleted topics or not
	 */
	public function getPostPagePerTime($topicId, $typeId, $topicTime, $num_per_page, $withDeleted = false) {
		$typeManager = TypeCenter::getTypeManager($typeId);
		$customPostClass = $typeManager->getPostClassName();
		if($withDeleted){
			$dql = $this->_em->createQuery( "SELECT COUNT(cp.id) FROM $customPostClass cp JOIN cp.postbase p WHERE p.topic = :id AND p.time < :time" );
		}else{
			$dql = $this->_em->createQuery( "SELECT COUNT(cp.id) FROM $customPostClass cp JOIN cp.postbase p WHERE p.topic = :id AND p.isDeleted = 0 AND p.time < :time" );
		}
		$dql->setParameter( 'id', $topicId );
		$dql->setParameter( 'time', $topicTime, \Doctrine\DBAL\Types\Type::DATETIME );
		$totalBefore = $dql->getSingleScalarResult();
		// to count the given posts
		$totalBefore += 1;
		$totalPage = ceil( $totalBefore / $num_per_page );
		return $totalPage;
	}
	
	/**
	 * Used to retrieve the last page number
	 * @param int $topicId
	 * @param int $typeId
	 * @param int $num_per_page
	 * @param boolean $withDeleted If we want to remove the deleted topics or not
	 * @deprecated Use Topic#getNumPosts instead
	 */
	public function getLastPage($topicId, $typeId, $num_per_page, $withDeleted = false) {
		$typeManager = TypeCenter::getTypeManager($typeId);
		$customPostClass = $typeManager->getPostClassName();
		if($withDeleted){
			$dql = $this->_em->createQuery( "SELECT COUNT(cp.id) FROM $customPostClass cp JOIN cp.postbase p WHERE p.topic = :id" );
		}else{
			$dql = $this->_em->createQuery( "SELECT COUNT(cp.id) FROM $customPostClass cp JOIN cp.postbase p WHERE p.topic = :id AND p.isDeleted = 0" );
		}
		$dql->setParameter( 'id', $topicId );
		$total = $dql->getSingleScalarResult();
		
		$totalPage = ceil( $total / $num_per_page );
		
		return $totalPage;
	}
	public function findReportedTopic(){
		$dql = $this->_em->createQuery( 'SELECT t FROM topicBase t WHERE t.id IN (SELECT distinct rt.topicId FROM reportTopic rt WHERE rt.isdeleted=0 AND rt.istreated=0)' );
		$result = $dql->getSingleResult();
		if( $result ) {
			return $result;
		} else {
			return false;
		}
	}
}