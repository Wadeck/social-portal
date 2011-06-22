<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class ForumRepository extends EntityRepository {
	
	/** Warning, after this call the forum entity must be reloaded to have the correct value */
	public function incrementTopicCount($forumId, $num = 1) {
		$query = $this->_em->createQuery( 'UPDATE Forum f SET f.numTopics = f.numTopics+:num WHERE f.id = :id' );
		$query->setParameter( 'num', $num );
		$query->setParameter( 'id', $forumId );
		return $query->execute();
	}
	
	/** Warning, after this call the forum entity must be reloaded to have the correct value */
	public function incrementPostCount($forumId, $num = 1) {
		$qb = $this->_em->createQueryBuilder();
		$qb->update( 'socialportal\model\Forum', 'f' )->set( 'f.numPosts', 'f.numPosts+?1' )->where( 'f.id = ?2' )->setParameter( 1, $num )->setParameter( 2, $forumId );
		$q = $qb->getQuery();
		return $q->execute();
	}
	
	/**
	 * Recount the number of topics in a given forum
	 * @param int $forumId
	 * @return int|false the number of undeleted topic in a given forum (no matter about open/close) or false if something went wrong
	 * @warning Very slow method, not created for the purpose of being used by the user 
	 */
	public function recountAllTopics($forumId) {
		$query = $this->_em->createQuery( 'SELECT COUNT(t.id) FROM TopicBase t JOIN t.forum f WHERE f.id = :id AND t.isDeleted = 0' );
		$query->setParameter( 'id', $forumId );
		$count = $query->getSingleScalarResult();
		
		$forum = $this->_em->find( 'Forum', $forumId );
		$forum->setNumTopics( $count );
		if(!$this->_em->persistAndFlush( $forum )){
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Recount the number of posts in each topics
	 * @param int $forumId
	 * @return int|false the number of undeleted posts in each undeleted topic (no matter about open/close) or false if something went wrong
	 * @warning Very slow method, not created for the purpose of being used by the user 
	 */
	public function recountAllPosts($forumId) {
		$topics = $this->_em->getRepository( 'TopicBase' )->findTopicsFromForum( $forumId );
		$total = 0;
		foreach( $topics as $topic ) {
			if( $topic->getIsDeleted() ) {
				continue;
			}
			$topicId = $topic->getId();
			$query = $this->_em->createQuery( 'SELECT COUNT(p.id) FROM PostBase p JOIN p.topic t WHERE t.id = :id AND p.isDeleted = 0' );
			$query->setParameter( 'id', $topicId );
			$count = $query->getSingleScalarResult();
			$topic->setNumPosts( $count );
			$this->_em->persist( $topic );
			$total += $count;
		}
		
		//		$query = $this->_em->createQuery('SELECT COUNT(p.id) FROM PostBase p JOIN p.topic t JOIN t.forum f WHERE f.id = :id AND t.isDeleted = 0 AND p.isDeleted = 0');
		//		$query->setParameter('id', $forumId);
		//		$count = $query->getSingleScalarResult();
		

		//		$forum = $this->_em->find('Forum', $forumId);
		//		$forum->setNumPosts($count);
		//		$this->_em->persistAndFlush($forum);
		

		$forum = $this->_em->find( 'Forum', $forumId );
		$forum->setNumPosts( $total );
		$this->_em->persist( $forum );
		if( !$this->_em->flushSafe( $forum ) ) {
			return false;
		}
		
		return $total;
	}
}