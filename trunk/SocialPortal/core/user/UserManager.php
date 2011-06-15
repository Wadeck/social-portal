<?php

namespace core\user;

use core\debug\Logger;

use core\tools\Utils;

use socialportal\model\User;

use Doctrine\ORM\EntityManager;

use core\security\Crypto;

use core\Request;

class UserManager {
	private static $COOKIE_NAME = 'rememberMeC';
	private static $SESSION_NAME = 'rememberMeS';
	
	/** @var Request */
	private $request;
	/** @var UserProviderInterface */
	private $userProvider;
	
	/** @var User */
	private $user;
	
	public function __construct(Request $request, UserProviderInterface $userProvider) {
		$this->request = $request;
		$this->userProvider = $userProvider;
	}
	
	/** @return User */
	public function retrieveInformationAboutConnectedUser() {
		if( !$this->retrieveFromSession() ) {
			if( !$this->retrieveFromCookie() ) {
				$this->user = self::createAnonymousUser();
			} else {
				// we loaded the info from the cookie, so we need to populate the session
				$this->populateSession();
			}
		}
		return $this->user;
	}
	
	/** Called when we want to create the session for the user, normally at the first page load for each visit */
	private function populateSession() {
		$value = Crypto::encodeSession( $this->user->getUsername(), $this->user->getId(), $this->user->getEmail() );
		$this->request->getSession()->set( self::$SESSION_NAME, $value );
		Logger::getInstance()->debug( 'populate session for ' . var_export( $this->user, true ) );
	}
	
	/** @return true iff the User information were loaded from the session */
	private function retrieveFromSession() {
		$rememberMe = $this->request->getSession()->get( self::$SESSION_NAME, null );
		if( $rememberMe ) {
			list( $username, $id, $email ) = Crypto::decodeSession( $rememberMe );
			if( $username && $id && $email ) {
				$user = $this->userProvider->getUserById( $id );
				if( !$user ) {
					// the user was deleted in the interval
					$this->removeSession();
					return false;
				}
				$this->user = $user;
				Logger::getInstance()->debug( 'retrieve user from session for ' . var_export( $this->user, true ) );
				return true;
			} else {
				$this->removeSession();
				return false;
			}
		}
		return false;
	}
	
	/** Called when we want to create the cookie for the user, normally only the first time he connected*/
	private function populateCookie() {
		if( !$this->user ) {
			return false;
		}
		$encodedCookie = Crypto::encodeCookie( $this->user->getUsername(), $this->user->getRandomKey(), $this->user->getPassword() );
		$this->request->cookies->set( self::$COOKIE_NAME, $encodedCookie );
		Logger::getInstance()->debug( 'populate cookies for ' . var_export( $this->user, true ) );
	}
	
	/** @return true iff the User information were loaded from the cookie */
	private function retrieveFromCookie() {
		$rememberMe = $this->request->cookies->get( self::$COOKIE_NAME, null );
		if( $rememberMe ) {
			list( $username, $hashPwd ) = Crypto::decodeCookie( $rememberMe );
			$user = $this->userProvider->getUserByUsername( $username );
			if( !$user ) {
				$this->removeCookie();
				return false;
			}
			$randomKey = $user->getRandomKey();
			$hashPwdDB = $user->getPassword();
			$result = Crypto::verifyCookieHash( $hashPwd, $randomKey, $hashPwdDB );
			if( $result ) {
				$this->user = $user;
				Logger::getInstance()->debug( 'retrieve user from cookies for ' . var_export( $this->user, true ) );
			} else {
				$this->removeCookie();
			}
			return $result;
		}
		return false;
	}
	
	/**
	 * Call when the user has filled the form to log in
	 * Add session and cookies !
	 * @param string $username
	 * @param string $password
	 * @param bool $rememberMe
	 * @return User if the connection succeeds, false otherwise
	 */
	public function connectUser($username, $password, $rememberMe = false) {
		//TODO implement me
		$user = $this->userProvider->getUserByUsername( $username );
		if( !$user ) {
			return false;
		}
		if( Crypto::verifyDBPassword( $user->getRandomKey(), $password, $user->getPassword() ) ) {
			$this->user = $user;
			if( $rememberMe ) {
				$this->populateCookie();
			}
			$this->populateSession();
			return $user;
		} else {
			return false;
		}
	}
	
	public function disconnect() {
		Logger::getInstance()->log( "Disconnection of " . var_export( $this->user, true ) );
		$this->removeSession();
		$this->removeCookie();
	}
	
	public function removeSession() {
		$this->request->getSession()->remove( self::$SESSION_NAME );
	}
	
	public function removeCookie() {
		$this->request->cookies->set( self::$COOKIE_NAME, null );
	}
	
	public function registerNewUser($username, $password, $email, $withActivation) {
		$result = $this->userProvider->getUserByUsername( $username );
		if( $result ) {
			return false;
		}
		
		$status = $withActivation ? 0 : 1;
		$user = self::createUser( $username, $password, $email, UserRoles::$full_user_role, $status );
		
		return $this->userProvider->addNewUser( $user );
	}
	
	/** Give very limited capabilities */
	public static function createAnonymousUser() {
		$user = new User();
		$user->setUsername( 'Anonymous' );
		$user->setStatus( 0 );
		$user->setEmail( 'anon@void.com' );
		$user->setRoles( UserRoles::$anonymous_role );
		//32 chars
		$user->setRandomKey( '12345678901234567890123456789012' );
		// to generate this password, we use the url: tool/createDirectPassword/_randomKey_/_password_
		// password = 'anon'
		$user->setPassword( '7e3ea8c729242fac9036b888d952767deb7be460' );
		
		return $user;
	}
	
	/**
	 * Helper to create a user with the correct format for password / randomKey
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $activationKey
	 * @return User that is not persisted at all for the moment
	 */
	public static function createUser($username, $password, $email, $role, $status = 0, $activationKey = '') {
		$user = new User();
		$user->setUsername( $username );
		$randomKey = Crypto::createRandomKey();
		$user->setPassword( Crypto::encodeDBPassword( $randomKey, $password ) );
		$user->setRandomKey( $randomKey );
		$user->setEmail( $email );
		$user->setStatus( $status );
		$user->setActivationKey( $activationKey );
		$user->setRegistered( new \DateTime( "now" ) );
		$user->setRoles( $role );
		return $user;
	}

}
		