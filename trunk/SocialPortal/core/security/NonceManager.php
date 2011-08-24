<?php

namespace core\security;

use core\FrontController;

class NonceManager {
	/** @var FrontController */
	private $frontController;
	
	public function __construct(FrontController $frontController) {
		$this->frontController = $frontController;
	}
	
	public function createNonce($actionName) {
		$user = $this->frontController->getCurrentUser();
		if( !$user ) {
			return false;
		}
		$pepper = $user->getRandomKey();
		$time = $this->tick();
		$hash = $this->computeNonce( $time, $pepper, $actionName );
		return $hash;
	}
	
	public function verifyNonce($nonce, $nonceAction) {
		$user = $this->frontController->getCurrentUser();
		if( !$user ) {
			return false;
		}
		$uid = $user->getRandomKey();
		
		$i = $this->tick();
		
		// Nonce generated 0-12 hours ago
		if( $this->computeNonce( ($i), $uid, $nonceAction ) === $nonce ) {
			return 1;
		}
		// Nonce generated 12-24 hours ago
		if( $this->computeNonce( ($i - 1), $uid, $nonceAction ) === $nonce ) {
			return 2;
		}
		// Invalid nonce
		return false;
	}
	
	private function computeNonce($tick, $pepperUnique, $actionName) {
		return Crypto::hashForNonce( $pepperUnique . $tick . $actionName );
	}
	
	/**
	 * Get the time-dependent variable for nonce creation.
	 * A nonce has a lifespan of two ticks. Nonces in their second tick may be
	 * updated, e.g. by autosave.
	 * 43200 = half day, that is the minimum life time, and at maximum the half
	 * @return int
	 */
	private function tick() {
		return ceil( time() / 43200 );
	}
}