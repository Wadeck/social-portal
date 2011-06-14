<?php
//@WARNING this file is statically loaded in FrontController
/**
 * Translate the text into the desired language
 * @param string $text The text containing the keyword used for translation like 'Hello %name%'
 * @param array $params Contains the keyword => value association like ['%name%' => 'Peter']
 */
function __($text, $params = array(), $withTranslate = true) {
	if( $withTranslate ) {
		return strtr( __js( $text ), $params );
	} else {
		return strtr( $text, $params );
	}
}

/** Prepare for javascript utilization (of the js function __() that only replace the %xx% by the value )*/
function __js($text) {
	return $text;
}
?>