<?php

namespace socialportal\repository;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {
	/** @return User|false if not found */
	public function findByUsername($username) {
		if( $username === 'Anonymous' ) {
			return $this->getAnonymousUser();
		}
		$qb = $this->_em->createQueryBuilder();
		$qb->select( 'u' )->from( 'socialportal\model\User', 'u' )->where( 'u.username = :username' )->setParameter( 'username', $username )->setMaxResults( 1 );
		$results = $qb->getQuery()->getResult();
		if( $results ) {
			return $results[0];
		} else {
			return false;
		}
	}
	
	public function find($id) {
		if( !$id ) {
			return $this->getAnonymousUser();
		} else {
			return parent::find( $id );
		}
	}
	
	public function getUserPassword($username) {
		if( $username === 'Anonymous' ) {
			return $this->getAnonymousUser()->getPassword();
		}
		$this->findByUsername( $username )->getPassword();
	}
	
	/** @return User */
	public function getAnonymousUser() {
		if( !$this->anonUser ) {
			$this->anonUser = UserManager::createAnonymousUser();
		}
		return $this->anonUser;
	}
//	/**
//	 * @return array of string representing the capabilities
//	 */
//	public function getUserCapability(User $user){
//		$roles = $user->getRoles();
//		foreach($roles){
//			
//		}
//	}
}