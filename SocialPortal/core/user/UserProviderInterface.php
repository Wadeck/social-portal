<?php

namespace core\user;

use socialportal\model\User;

interface UserProviderInterface {
	/** @return User */
	public function getUserById($id);
	/** @return User */
	public function getUserByUsername($username);
	/** @return User */
	public function getUserByActivationKey($key);
	
	/**
	 * @return User|false if there is already a user with this username
	 */
	public function addNewUser(User $user);
}