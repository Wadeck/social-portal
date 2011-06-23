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
	
	public function addNewUser(User $user) {
		try {
			$this->em->persist( $user );
			$this->em->flush();
			return $user;
		} catch (\Exception $e ) {
			return false;
		}
	}
}