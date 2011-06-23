<?php

namespace core\user;

use socialportal\model\User;

interface UserProviderInterface {
	/** @return User */
	public function getUserById($id);
	/** @return User */
	public function getUserByUsername($username);
	/** @param User the user we want to create in the system */
	public function addNewUser(User $user);
}