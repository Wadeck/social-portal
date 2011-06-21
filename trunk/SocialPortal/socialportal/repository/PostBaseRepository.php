<?php

namespace socialportal\repository;

use core\topics\TopicType;

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
//
//	/** @return array of PostBase */
//	public function findPostsFromTopic($topicId, $page_num, $num_per_page) {
//		$offset = ($page_num - 1) * $num_per_page;
//		$dql = $this->_em->createQuery( 'SELECT p FROM PostBase p WHERE p.topic = :id' );
//		$dql->setParameter( 'id', $topicId )->setFirstResult( $offset )->setMaxResults( $num_per_page );
//		$posts = $dql->getResult();
//		return $posts;
//	}
	
	public function findAllFullPosts($topicId, $topicTypeId, $page_num, $num_per_page){
		$offset = ($page_num - 1) * $num_per_page;
		$customType = TopicType::translateTypeIdToPostName( $topicTypeId );
		// special join to fetch the customtype and the postbase information
		$dql = $this->_em->createQuery( "SELECT ct, p FROM $customType ct JOIN ct.postbase p WHERE p.id = :id" );
		$dql->setParameter( 'id', $topicId )->setFirstResult( $offset )->setMaxResults( $num_per_page );
		$fullPosts = $dql->getResult();
		return $fullPosts;
	}
	
	/** @return specific post|false if not found */
	public function findFullPost($postId) {
		// we are forced to use two queries because we don't know in advance
		// what is the name of the database where are stored the specific topic
		$postBase = $this->findBasePost( $postId );
		if( !$postBase ) {
			return false;
		}
		$customType = $postBase->getCustomType();
		$customType = TopicType::translateTypeIdToPostName( $customType );
		
		$dql = $this->_em->createQuery( "SELECT ct FROM $customType ct WHERE ct.postbase = :id" );
		$dql->setParameter( 'id', $postId )->setMaxResults( 1 );
		$result = $dql->getSingleResult();
		
		if( $result ) {
			$fullPost = $result;
			$fullPost->setPostbase( $postBase );
			return $fullPost;
		} else {
			return false;
		}
	}
	
	/** @return int */
	public function getLastPosition($topicId){
		$dql = $this->_em->createQuery( 'SELECT PARTIAL p.{id,position} FROM PostBase p WHERE p.topic = :id ORDER BY p.position DESC' );
		$dql->setParameter( 'id', $topicId )->setMaxResults( 1 );
		$posts = $dql->getResult();
		if(!$posts){
			return 1;
		}
		$post = $posts[0];
		$position = $post->getPosition()+1;
		return $position;
	}
}