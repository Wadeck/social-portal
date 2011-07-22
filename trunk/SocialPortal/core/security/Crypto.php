<?php

namespace core\security;

class Crypto {
	/**  @var array different salts used to hash password in db or cookie */
	private static $SALTS = array( 
		// 13 + 11 + 10 = 34 => 272bits
		'cookie' => array( '62b1d5u|e3_04', '^t:,46ru7ç+', 'c245ho4{s€' ),
		// 10 + 9 + 10 = 29 => 232bits
		'db' => array( 'le$gA5_ds*', 'm0-!%4Dr€', '#@^2s_5gA~' ),
		// 7 + 7 + 7 = 21 => 168bits
		'nonce' => array( '5ks*à-+', 'gZs-/9D', '6èvF9d/' ) );
	
	/** Database usage @return string the encrypted pwd */
	public static function encodeDBPassword($login, $pwd) {
		$encrypted_pass = self::hashPwd( $login, $pwd, 'db' );
		return $encrypted_pass;
	}
	
	/** Database usage @return bool true iff the pwd correspond to the pass in database */
	public static function verifyDBPassword($key, $pwd, $from_base) {
		$hashedPassword = self::hashPwd( $key, $pwd, 'db' );
		if( $hashedPassword === $from_base ) {
			return true;
		} else {
			return false;
		}
	}
	
	/** Session usage @return string Computed by a combination of login and pwd*/
	public static function encodeSession($login, $id, $email) {
		$value = array( $login, $id, $email );
		$value = serialize( $value );
		return base64_encode( $value );
	}
	
	/** Session usage @return ($login, $id, $email) */
	public static function decodeSession($sessionValue) {
		$value = base64_decode( $sessionValue );
		return unserialize( $value );
	}
	
	/** Cookie usage @return string Computed by a combination of login and pwd*/
	public static function encodeCookie($login, $key, $pwd) {
		$value = array( $login, self::hashPwd( $key, $pwd, 'cookie' ) );
		$value = serialize( $value );
		return base64_encode( $value );
	}
	
	/** Cookie usage @return ($login, $hashPwd) */
	public static function decodeCookie($cookieValue) {
		$value = base64_decode( $cookieValue );
		return unserialize( $value );
	}
	
	/** Cookie usage @return bool true iff the cookie value is the one that was set before by encodeCookie */
	public static function verifyCookieHash($hashPwd, $key_from_db, $pwd_from_db) {
		$hash_from_db = self::hashPwd( $key_from_db, $pwd_from_db, 'cookie' );
		if( $hashPwd === $hash_from_db ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * sha256 11362ms for 100 iterations
	 * md5 6251ms for 1000000 iterations
	 * sha1 7171ms for 1000000 iterations (but a bit safer)
	 */
	private static function hashPwd($login, $pwd, $saltKey) {
		if( !isset( self::$SALTS[$saltKey] ) || 3 < count( self::$SALTS[$saltKey] ) ) {
			throw new \InvalidArgumentException( 'Incorrect salt key in hashPwd', 2 );
		}
		$salts = self::$SALTS[$saltKey];
		return sha1( $salts[0] . $login . $salts[1] . sha1( $pwd ) . $salts[2] );
	}
	
	/** @return random 32chars string */
	public static function createRandomKey() {
		list( $usec, $sec ) = explode( " ", microtime() );
		$first = mt_rand();
		$second = $usec * $sec;
		$result = md5( $first . $second );
		return $result;
	}
	
	public static function hashForNonce($nonce) {
		$salts = self::$SALTS['nonce'];
		$hash = sha1( $salts[0] . $salts[1] . $nonce . $salts[2] );
		return substr( $hash, -25, 12 );
	}
}