<?php

namespace core\debug;

class Displayer{
	public static function displayMessage($message){
		?>
		<div style="border: 1px solid #ccc"><?php echo $message; ?></div>
		<?php
	}
	public static function displayVar($message, $var){
		if( !isset($var) ){
			$message .= ' is not set (or null)'; 
		}elseif( false === $var ){
			$message .= ' is false'; 
		}elseif( ''==$var ){
			$message .= ' is an empty string'; 
		}elseif($var){
			ob_start();
			print_r($var);
			$message .= ' = '.ob_get_clean();
			$message = str_replace('   ', '&nbsp;&nbsp;&nbsp;', $message); 
			$message = nl2br($message);
		}else{
			$message .= ' is not null/false/empty string/correct variable'; 
		}
		?>
		<div style="border: 1px solid #ccc"><?php echo $message; ?></div>
		<?php
	}
}

?>