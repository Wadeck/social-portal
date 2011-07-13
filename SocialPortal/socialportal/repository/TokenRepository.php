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

class TokenRepository extends EntityRepository {
	/**
	 * 
	 * @param string $tokenValue The random key we search for
	 * @return Token|false if the token is not found
	 */
	public function findValidToken($tokenValue) {
		$datetime = new \DateTime('now');
		
		$dql = $this->_em->createQuery( 'SELECT t FROM Token t WHERE t.token = :token AND t.expirationDate >= :time' );
		$dql->setParameter( 'token', $tokenValue );
		$dql->setParameter('time', $datetime, \Doctrine\DBAL\Types\Type::DATETIME);
		$dql->setMaxResults( 1 );
		$result = $dql->getResult();
		if( $result ) {
			return $result[0];
		} else {
			return false;
		}
	}
	/**
	 * @param string $tokenValue The random key we search for
	 * @return array Metadata that were stored in the token | false if the token is not found
	 * @warning check false === and not false == because the meta can be an empty array
	 */
	public function findValidTokenMeta($tokenValue) {
		$datetime = new \DateTime('now');
		
		$dql = $this->_em->createQuery( 'SELECT t FROM Token t WHERE t.token = :token AND t.expirationDate >= :time' );
		$dql->setParameter( 'token', $tokenValue );
		$dql->setParameter('time', $datetime, \Doctrine\DBAL\Types\Type::DATETIME);
		$dql->setMaxResults( 1 );
		$result = $dql->getResult();
		if( $result ) {
			return unserialize($result[0]->getMeta() );
		} else {
			return false;
		}
	}

	/**
	 * 
	 * @param int $expirationTime Time in second in which the token will be expired
	 * @param array $meta Metadata that will be stored with the token
	 * @return Valid token already flushed | null if there was a problem in expiration time
	 * @flush
	 */
	public function createValidToken(array $meta = array(), $expirationTime = false){
		if(false === $expirationTime || $expirationTime < 0){
			// no expiration date
			$expirationDate = null;
		}else{
			$expirationDate = time();
			$expirationDate += $expirationTime;
			$expirationDate = new \DateTime( '@'.$expirationDate );
		}

		$meta = serialize($meta);
		
		$token = new Token();
		$token->setExpirationDate($expirationDate);
		$token->setMeta($meta);
		$this->_em->persist($token);
		
		$max = Config::get('max_attempts', 5);
		for($i = 0; $i < $max; $i++){
			$token->setToken(Utils::createRandomString(32, 'alphanumeric'));
			$result = $this->_em->flushSafe();
			if($result){
				break;
			}
		}
		if($result){
			return $token;
		}else{
			return null;
		}
	}
}