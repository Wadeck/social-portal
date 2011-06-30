<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class UserProfileRepository extends EntityRepository {
	
	/** @return UserProfile|null if not found */
	public function findByUserId($userId) {
		if( 1 >= $userId ) {
			return null;
		}
		$dql = $this->_em->createQuery('SELECT p FROM UserProfile p WHERE p.userId = :id');
		$dql->setParameter( 'id', $userId )->setMaxResults( 1 );
		$results = $dql->getResult();
		if( $results ) {
			return $results[0];
		} else {
			return null;
		}
	}
}