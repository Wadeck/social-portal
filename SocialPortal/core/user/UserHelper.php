<?php

namespace core\user;
use core\FrontController;

use socialportal\model\User;

/**
 * To be used in views only
 *
 */
class UserHelper {
	/** @var User */
	private $currentUser;
	
	/**
	 * Accepts only user that are already persisted !
	 * @param User $user
	 * @return true if the current user is not null
	 */
	public function setCurrentUser(User $user) {
		$id = intval($user->getId());
		if( !$id ) {
			throw new \InvalidArgumentException( 'The user passed as argument is not persistant' );
		}
		if( $id === UserManager::$anonUserId ) {
			$this->currentUser = UserManager::getAnonymousUser();
		} elseif( $id === UserManager::$nullUserId ) {
			$this->currentUser = UserManager::getNullUser();
			return false;
		} else {
			$this->currentUser = $user;
		}
		return true;
	}
	
	public function getId() {
		return $this->currentUser->getId();
	}
	
	public function getUsername() {
		return $this->currentUser->getUsername();
	}
	
	public function getEmail() {
		return $this->currentUser->getEmail();
	}
	
	/** @display the avatar img tag with link to the profile integrated */
	public function insertAvatar($size) {
		$key = $this->currentUser->getAvatarKey();
		if( $key === 'null' ) {
			return '';
		} elseif( $key === 'anon' ) {
			$imgLink = 'http://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm';
		} else {
			$imgLink = $this->getGravatar( $key, $size, 'identicon' );
		}
		?><a class="avatar" href="<?php
		echo $this->getLinkToProfile();
		?>"><img src="<?php
		echo $imgLink;
		?>" alt="Avatar Image" class="avatar user-11-avatar" width="<?php echo $size; ?>" height="<?php echo $size; ?>"></a>
<?php
	}
	
	/** Taken from http://fr.gravatar.com/site/implement/images/php/ */
	private function getGravatar($avatarKey, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $avatarKey ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if( $img ) {
			$url = '<img src="' . $url . '"';
			foreach( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
	
	/** @display a link to the user profile */
	public function insertLinkToProfile() {
		?><a href="<?php
		echo $this->getLinkToProfile();
		?>" title="<?php
		echo $this->getUsername();
		?>"><?php
		echo $this->getUsername();
		?></a>
<?php
	}
	
	public function getLinkToProfile() {
		return '#';
	}
}