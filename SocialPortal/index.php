<?php
define('DEBUG', true);

//require 'core/FrontController.php';

require 'core/ClassLoader.php';
core\ClassLoader::getInstance()
->addMatch('core')
->addMatch('socialportal')
->addMatch('Doctrine', 'lib')
->register();

// to compute the time taken to load the page
\core\debug\Benchmark::start();
core\FrontController::getInstance()->dispatch();


//// Durée de vie de la session (Time To Live)
//$ttl = 10;
//session_set_cookie_params( $ttl );
//session_start();
//ob_start();
//
//var_dump( $_SESSION );
//var_dump( $_COOKIE );
//if( !isset( $_SESSION['foo'] ) || !isset( $_SESSION['bar'] ) ) {
////	if( isset( $_COOKIE['w_key'] ) ) {
////		echo 'from cookie retrieval <br>';
////		$_SESSION['bar'] = $_COOKIE['w_bar'];
////		$_SESSION['foo'] = time() + $_COOKIE['w_key'];
////	} else {
//		echo 'creation of session / cookie <br>';
//		$_SESSION['bar'] = rand( 1, 10000 );
//		$_SESSION['foo'] = time() + $ttl;
//		$_COOKIE['w_key'] = 5 + rand( 0, 5 );
//		$_COOKIE['w_bar'] = $_SESSION['bar'];
////	}
//} else {
//	echo 'from session retrieval <br>';
//}
//
//echo 'TTL de la session (' . $_SESSION['bar'] . ') : ' . (($_SESSION['foo']) - time()) . '<br>';
//
//var_dump( $_COOKIE );
//foreach( $_COOKIE as $key => $value ) {
//	if( false !== strpos( $key, 'w_' ) ) {
//		echo "cookie: $key => $value ";
//		$result = setcookie( $key, $value, time() + 60 * 1 );
//		echo "[$result]<br>";
//	} else {
//		echo "session cookie $key not stored <br>";
//	}
//}
//$content = ob_get_clean();
//echo $content;





