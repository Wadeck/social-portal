<?php

use core\security\Crypto;
require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Crypto test case.
 */
class CryptoTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
	}
	
	/**
	 * Tests Crypto::encodeDBPassword()
	 */
	public function testEncodeVerifyDBPassword() {
		$login = 'salut';
		$pwd = 'bidule';
		$encoded = Crypto::encodeDBPassword( $login, $pwd );
		$result = Crypto::verifyDBPassword( $login, $pwd, $encoded );
		$this->assertTrue( $result );
	}
	
	/**
	 * Tests Crypto::encodeSession()
	 */
	public function testEncodeDecodeSession() {
		$login = 'salut';
		$id = '1452134';
		$email = 'ag.fd@s.cos';
		$session = Crypto::encodeSession( $login, $id, $email );
		list( $ret_login, $ret_id, $ret_email ) = Crypto::decodeSession( $session );
		$this->assertSame( $login, $ret_login );
		$this->assertSame( $id, $ret_id );
		$this->assertSame( $email, $ret_email );
	}
	
	/**
	 * Tests Crypto::encodeCookie()
	 */
	public function testEncodeDecodeCookie() {
		$login = 'salut';
		$pwd = 'bidul¬§°e';
		$key = 'sad^f9346_af';
		$cookie = Crypto::encodeCookie( $login, $key, $pwd );
		list( $ret_login, $ret_hash_pwd ) = Crypto::decodeCookie( $cookie );
		$this->assertSame( $login, $ret_login );
		$result = Crypto::verifyCookieHash( $ret_hash_pwd, $key, $pwd );
		$this->assertTrue( $result );
	}
	/**
	 * Tests Crypto::generateRandomKey()
	 */
	public function testGenerateRandomKey() {
		$keys = array();
		for($i = 0 ; $i < 10 ; $i++){
			$keys[$i] = Crypto::createRandomKey();
			for($j = 0 ; $j < $i ; $j++){
				$this->assertFalse($keys[$j] === $keys[$i], 'The random generator has low collision');
			}
		}
	}
	

}

