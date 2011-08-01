<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class ChartMoodRepository extends EntityRepository {
	
	/** @return true iff the user has some information */
	public function hasMoodInfo($userId) {
		if( 1 >= $userId ) {
			return false;
		}
		$dql = $this->_em->createQuery('SELECT partial {b.id} FROM ChartMood b WHERE b.userId = :id');
		$dql->setParameter( 'id', $userId )->setMaxResults( 1 );
		$results = $dql->getResult();
		if( $results ) {
			return true;
		} else {
			return false;
		}
	}
	
	/** @return ChartMood | false */
	public function findMoodInfo($userId) {
		if( 1 >= $userId ) {
			return false;
		}
		$dql = $this->_em->createQuery('SELECT b FROM ChartMood b WHERE b.userId = :id ORDER BY b.date');
		$dql->setParameter( 'id', $userId );
		$results = $dql->getResult();
		if( $results ) {
			return $results;
		} else {
			return false;
		}
	}
}