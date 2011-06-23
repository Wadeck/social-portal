<?php

namespace socialportal\repository;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class TermRelationRepository extends EntityRepository {
	
	public function getAllTags($topicId) {
		//TODO to support tags
		return array();
	}
}