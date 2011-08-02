<?php

namespace socialportal\common\templates;

//TODO move to core\templates
interface iInsertable {
	/** Method used in view to display the template */
	function insert();
}