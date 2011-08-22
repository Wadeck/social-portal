<?php

namespace socialportal\repository;

use socialportal\model\Token;

use core\tools\Utils;

use core\Config;

use socialportal\common\topic\TypeCenter;

use core\security\Crypto;

use core\user\UserManager;

use socialportal\model\User;

use Doctrine\ORM\EntityRepository;

class ReportAbuseRepository extends EntityRepository {
	/**
	 * 
	 * @param string $tokenValue The random key we search for
	 * @return Token|false if the token is not found
	 */
	public function findLastReportAbuse() {
		$datetime = new \DateTime('now');
		$dql = $this->_em->createQuery( 'SELECT ra FROM ReportAbuse ra WHERE ra.isViewed = 1' );
		$result = $dql->getResult();
		if( $result ) {
			return $result;
		} else {
			return false;
		}
	}
}