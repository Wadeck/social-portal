<?php

namespace core\tools;

interface ValidableInterface {
	/** @return true iff the context satisfies the condition */
	function isValid();
}
