<?php

namespace core\common;

use socialportal\common\templates\iInsertable;

class NullTemplate implements iInsertable{
	public function insert(){
		echo '<!-- null template -->';
	}
}