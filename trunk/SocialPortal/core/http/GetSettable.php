<?php

namespace core\http;

interface GetSettable {
	function get($key, $default=null);
	function set($key, $value);
}

?>