<?php

namespace core\user;

class UserRoles {
	public static $admin_role = 32;
	public static $moderator_role = 16;
	public static $full_user_role = 8;
//	public static $limited_user_role = 8;
//	public static $anonymous_role = 16;
	public static $anonymous_role = 1;
	
	/**
	 * admin|administrator, moderator|modo, fullUser|full_user|user, anon|anonymous without care of letter cases
	 */
	public static function stringToInt($role){
		switch(strtolower($role)){
			case 'admin': case 'administrator':
				return self::$admin_role;	
			case 'modo' : case 'moderator':
				return self::$moderator_role;
			case 'user': case 'fulluser': case 'full_user':
				return self::$full_user_role;
			case 'anon': case 'anonymous':
				return self::$anonymous_role;
			default:
				return 0;
		}
	}
}