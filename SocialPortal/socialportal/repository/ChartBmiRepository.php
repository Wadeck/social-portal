<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class ChartBmiRepository extends EntityRepository {
	
	/** @return true iff the user has some information */
	public function hasEnoughBmiInfo($userId) {
		if( 1 >= $userId ) {
			return false;
		}
		$dql = $this->_em->createQuery('SELECT partial b.{id} FROM ChartBmi b WHERE b.userId = :id');
		$dql->setParameter( 'id', $userId )->setMaxResults( 2 );
		$results = $dql->getResult();
		if( 2 <= count( $results ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/** @return ChartBmi | false */
	public function findBmiInfo($userId) {
		if( 1 >= $userId ) {
			return false;
		}
		$dql = $this->_em->createQuery('SELECT b FROM ChartBmi b WHERE b.userId = :id ORDER BY b.date');
		$dql->setParameter( 'id', $userId );
		$results = $dql->getResult();
		if( $results ) {
			return $results;
		} else {
			return false;
		}
	}
	
	public function removeItem($userId, $itemId){
		if( 1 >= $userId ) {
			return false;
		}
		$item = $this->find($itemId);
		if(null === $item){
			return false;
		}
		$this->_em->remove($item);
		if( false !== $this->_em->flushSafe() ) {
			return true;
		} else {
			return false;
		}
	}
}