<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {
	/** @return User|false if not found */
	public function findByUsername($username) {
		if( 'Anonymous' === $username ) {
			return UserManager::getAnonymousUser();
		} else if( 'NullUser' === $username ) {
			return UserManager::getNullUser();
		}
		$dql = $this->_em->createQuery('SELECT u FROM User u WHERE u.username = :username');
		$dql->setParameter( 'username', $username )->setMaxResults( 1 );
		$results = $dql->getResult();
//		$qb = $this->_em->createQueryBuilder();
//		$qb->select( 'u' )->from( 'socialportal\model\User', 'u' )->where( 'u.username = :username' )->setParameter( 'username', $username )->setMaxResults( 1 );
//		$results = $qb->getQuery()->getResult();
		if( $results ) {
			return $results[0];
		} else {
			return false;
		}
	}
	
	public function find($id) {
		if( !$id ) {
			return null;
		} else if( UserManager::$nullUserId == $id ) {
			return UserManager::getNullUser();
		} else if( UserManager::$anonUserId == $id ) {
			return UserManager::getAnonymousUser();
		} else {
			return parent::find( $id );
		}
	}
	
	public function getUserPassword($username) {
		return $this->findByUsername( $username )->getPassword();
	}
	
	/**
	 * @param string $email
	 * @return User|null
	 */
	public function findUserByEmail($email){
		$dql = $this->_em->createQuery('SELECT u FROM User u WHERE u.email = :email');
		$dql->setParameter( 'email', $email );
		$result = $dql->getResult();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
	
	/**
	 * @param string $email
	 * @return User|null
	 */
	public function findUserByActivationKey($key){
		$dql = $this->_em->createQuery('SELECT u FROM User u WHERE u.activationKey = :key');
		$dql->setParameter( 'key', $key );
		$result = $dql->getResult();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
	
	/**
	 * @param string $email
	 * @return User|null
	 */
	public function findUserByUsernameAndEmail($username, $email){
		$dql = $this->_em->createQuery('SELECT u FROM User u WHERE u.username = :username AND u.email = :email');
		$dql->setParameter( 'username', $username );
		$dql->setParameter( 'email', $email );
		$result = $dql->getResult();
		if($result){
			return $result[0];
		}else{
			return null;
		}
	}
}