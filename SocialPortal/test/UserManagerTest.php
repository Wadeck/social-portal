<?php

use core\user\UserProviderInterface;
use core\http\Session;
use core\security\Crypto;
use socialportal\model\User;
use core\debug\ReflectionHelper;
use core\DoctrineLink;
use core\http\storage\ArraySessionStorage;
use core\Request;
use core\user\UserManager;
require_once 'PHPUnit\Framework\TestCase.php';

/**
 * UserManager test case.
 */
class UserManagerTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var UserManager
	 */
	private $um;
	/** @var Request */
	private $request;
	/** @var EntityManager */
	private $em;
	
	private $username;
	private $password;
	private $email; 
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		
		$this->username = 'testUM';
		$this->email = 'test@emfil.cad';
		$this->password = 'af§32_#';
		
		$request = new Request();
		$request->setSession( new Session( new ArraySessionStorage() ) );
		
		$user = new User();
		$user->setUsername( $this->username );
		$user->setEmail( $this->email );
		$user->setPassword( Crypto::encodeDBPassword( $this->username, $this->password ) );
		$user->setRandomKey( Crypto::createRandomKey() );
		
		$idRefl = ReflectionHelper::retrieveProperty( $user, 'id' );
		$idRefl->setValue($user, 123);
		
		$this->um = new UserManager( $request, new UserProviderMock( $user ) );
		$propRefl = ReflectionHelper::retrieveProperty( $this->um, 'user' );
		$propRefl->setValue( $this->um, $user );
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->UserManager = null;
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	public function testReversibilitySession() {
		ReflectionHelper::useMethod( $this->um, 'populateSession' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromSession' );
		$this->assertTrue( $result, 'Session retrieval not working' );
	}
	
	public function testReversibilityCookie() {
		ReflectionHelper::useMethod( $this->um, 'populateCookie' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromCookie' );
		$this->assertTrue( $result, 'Cookie retrieval not working' );
	}
	
	public function testReversibilityConnectRememberMe() {
		ReflectionHelper::useMethod( $this->um, 'connectUser', array($this->username, $this->password, true) );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromCookie' );
		$this->assertTrue( $result, 'Cookie retrieval not working after connectUser' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromSession' );
		$this->assertTrue( $result, 'Session retrieval not working after connectUser' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromCookie' );
		$this->assertTrue( $result, 'Cookie retrieval not working after connectUser' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromSession' );
		$this->assertTrue( $result, 'Session retrieval not working after connectUser' );
	}
	public function testReversibilityConnectForgetMe() {
		ReflectionHelper::useMethod( $this->um, 'connectUser', array($this->username, $this->password, false) );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromSession' );
		$this->assertTrue( $result, 'Session retrieval not working after connectUser' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromCookie' );
		$this->assertFalse( $result, 'Cookie retrieval not working after connectUser' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromSession' );
		$this->assertTrue( $result, 'Session retrieval not working after connectUser' );
		$result = ReflectionHelper::useMethod( $this->um, 'retrieveFromCookie' );
		$this->assertFalse( $result, 'Cookie retrieval not working after connectUser' );
	}

}

class UserProviderMock implements UserProviderInterface {
	private $user;
	public function __construct($user) {
		$this->user = $user;
	}
	public function getUserById($id) {
		return $this->user;
	}
	
	public function getUserByUsername($username) {
		return $this->user;
	}
}

