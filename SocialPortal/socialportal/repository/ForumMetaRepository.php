<?php
namespace socialportal\repository;

use socialportal\model\ForumMeta;

use core\topics\TopicType;

use socialportal\model\TopicBase;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class ForumMetaRepository extends EntityRepository {
	
	/** @return mixed|null if not found */
	public function getMeta($forumId, $key, $default = null) {
		$qb = $this->_em->createQueryBuilder();
		$qb->select( 'fm' )->from( 'socialportal\model\ForumMeta', 'fm' )->where( 'fm.forumId = :id' )->andWhere( 'fm.metaKey = :key' )->setParameter( 'id', $forumId )->setParameter( 'key', $key )->setMaxResults( 1 );
		$results = $qb->getQuery()->getResult();
		if( $results ) {
			$meta = $results[0];
			return $meta->getMetaValue();
		} else {
			return $default;
		}
	}
	
	/** 
	 * @param int $forumId
	 * @return array of topic type id
	 */
	public function getAcceptableTopics($forumId) {
		$value = $this->getMeta( $forumId, '_acceptTopics' );
		if( $value ) {
			return unserialize( $value );
		} else {
			return array();
		}
	}
	
	/**
	 * 
	 * @param int $forumId
	 * @param array $acceptTopicsId
	 * @return true if the modification was a success
	 */
	public function setAcceptableTopics($forumId, array $acceptTopicsId = array()) {
		$value = serialize( $acceptTopicsId );
		return $this->setMeta( $forumId, '_acceptTopics', $value );
	}
	
	/** @return true if the modification was a success */
	public function setMeta($forumId, $key, $value) {
		$meta = $this->findOneBy( array( 'forumId' => $forumId, 'metaKey' => $key ) );
		if( !$meta ) {
			$meta = new ForumMeta();
			$meta->setForumId($forumId);
			$meta->setMetaKey( $key );
		}
		$meta->setMetaValue( $value );
		return $this->_em->persistAndFlush( $meta );
	}
}