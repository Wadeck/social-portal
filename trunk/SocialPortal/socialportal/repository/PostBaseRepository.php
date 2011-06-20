<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class PostBaseRepository extends EntityRepository {
	/** @return PostBase|false if not found */
	public function findBasePost($postId) {
		$dql = $this->_em->createQuery( 'SELECT p FROM PostBase p WHERE p.id = :id' );
		$dql->setMaxResults( 1 )->setParameter( 'id', $postId );
		$result = $dql->getSingleResult();
		if( $result ) {
			return $result;
		} else {
			return false;
		}
	}
	
	/** @return array of PostBase */
	public function findPostsFromTopic($topicId, $page_num, $num_per_page) {
		$offset = ($page_num - 1) * $num_per_page;
		$dql = $this->_em->createQuery( 'SELECT p FROM PostBase p WHERE p.topic = :id' );
		$dql->setParameter( 'id', $topicId )->setFirstResult( $offset )->setMaxResults( $num_per_page );
		$posts = $dql->getResult();
		return $posts;
	}
}