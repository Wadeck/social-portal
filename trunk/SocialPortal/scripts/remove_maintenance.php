<?php
	$filename = '../index.php';
	$exist = file_exists($filename);
	$readable = is_readable($filename);
	$modifiable = is_writable($filename);
	if( !$exist ){
		echo "file not exist\n";
		exit(-1);
	}
	if( !$readable ){
		echo "file not readable\n";
		exit(-2);
	}
	if( !$modifiable ){
		echo "file not modifiable\n";
		exit(-3);
	}
	$content = file_get_contents($filename);
	if( !$content ){
		echo "file empty\n";
		exit(-4);
	}
	if( false !== strpos($content, '/*%s%*/true/*%e%*/') ){
		$content = strtr($content, array('/*%s%*/true/*%e%*/' => '/*%s%*/false/*%e%*/'));
		file_put_contents($filename, $content);
		echo "maintenance removed successfully\n";
		exit(0);
	}else{
		echo "maintenance was not activated\n";
		exit(0);
	}