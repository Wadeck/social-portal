<?php

namespace core\annotations;

interface ValidableInterface {
	/** @return true iff the context satisfies the condition */
	function isValid();
}
