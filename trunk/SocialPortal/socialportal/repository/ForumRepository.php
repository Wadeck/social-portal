<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class ForumRepository extends EntityRepository {
	
	/** Warning, after this call the forum entity must be reloaded to have the correct value */
	public function incrementTopicCount($forumId, $num = 1) {
		$query = $this->_em->createQuery('UPDATE Forum f SET f.numTopics = f.numTopics+:num WHERE f.id = :id');
		$query->setParameter('num', $num);
		$query->setParameter('id', $forumId);
		return $query->execute();
	}
	
	/** Warning, after this call the forum entity must be reloaded to have the correct value */
	public function incrementPostCount($forumId, $num = 1) {
		$qb = $this->_em->createQueryBuilder();
		$qb->update( 'socialportal\model\Forum', 'f' )->set( 'f.numPosts', 'f.numPosts+?1' )->where( 'f.id = ?2' )->setParameter( 1, $num )->setParameter( 2, $forumId );
		$q = $qb->getQuery();
		return $q->execute();
	}
	
	public function recountAllTopics($forumId){
		$query = $this->_em->createQuery('SELECT COUNT(t.id) FROM TopicBase t JOIN t.forum f WHERE f.id = :id AND t.isDeleted = 0');
		$query->setParameter('id', $forumId);
		$count = $query->getSingleScalarResult();
		
		$forum = $this->_em->find('Forum', $forumId);
		$forum->setNumTopics($count);
		$this->_em->persistAndFlush($forum);
		
		return $count;
	}
	
	public function recountAllPosts($forumId){
		$query = $this->_em->createQuery('SELECT COUNT(p.id) FROM PostBase p JOIN p.topic t JOIN t.forum f WHERE f.id = :id AND t.isDeleted = 0 AND p.isDeleted = 0');
		$query->setParameter('id', $forumId);
		$count = $query->getSingleScalarResult();
		
		$forum = $this->_em->find('Forum', $forumId);
		$forum->setNumPosts($count);
		$this->_em->persistAndFlush($forum);
		
		return $count;
	}
}