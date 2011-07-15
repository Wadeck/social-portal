<?php

namespace core\user;

use socialportal\model\User;

use Doctrine\ORM\EntityManager;

class UserEntityProvider implements UserProviderInterface {
	private $em;
	
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
	
	public function getUserById($id) {
		return $this->em->find( 'User', $id );
	}
	
	public function getUserByUsername($username) {
		return $this->em->getRepository( 'User' )->findByUsername( $username );
	}
	public function getUserByActivationKey($key){
		return $this->em->getRepository( 'User' )->findUserByActivationKey( $key );
	}
	
	public function addNewUser(User $user) {
		$this->em->persist( $user );
		if($this->em->flushSafe()){
			return $user;
		}else{
			return false;
		}
	}
	
}