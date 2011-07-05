<?php

namespace core\tools;
use Exception;
class CountryReader{
	private $fileName;
	
	public function __construct(){
		
	}
	
	public function setFile($fileName){
		$this->fileName = $fileName;
		if(!file_exists($fileName)){
			throw new Exception('File not found or not accessible');
		}
	}
	
	/**
	 * @return [country]
	 * 	where country = [countryCode, countryName, phoneCode, states]
	 * 		where states = [state]
	 * 			where state = [countryCode, shortName, stateName]
	 */
	public function getAllCountries(){
		$countries = array();
		$country = array();
		$states = array();
		$tokens = null;
//		$lines = file_get_contents($this->fileName);
		$lines = file($this->fileName);
		$lines = str_replace("\r", '', $lines);
		$lines = str_replace("\n", '', $lines);
		foreach($lines as $line){
			if(!strlen($line)){
				continue;
			}
			if('#' === $line[0]){
				// comment
				continue;
			}
			$tokens = explode(':', $line);
			if(count($tokens) >= 3){
				if(is_numeric($tokens[2])){
					$isCountry = true;
				}else{
					$isCountry = false;
				}
			}else{
				continue;
			}
			$countryCode = $tokens[0];
			if($isCountry){
				$country = array();
				$country['countryCode'] = $countryCode;
				$country['countryName'] = $tokens[1];
				$country['phoneCode'] = $tokens[2];
				
				$countries[$countryCode] = $country;
			}else{
				$state = array();
				$state['countryCode'] = $countryCode;
				$state['shortName'] = $tokens[1];
				$state['stateName'] = $tokens[2];
				
				$countries[$countryCode]['states'][] = $state; 
			}
		}
		
		return $countries;
	}
	
	function file_get_contents_ANSI($fn) { 
	    $opts = array( 
	        'http' => array( 
	            'method'=>"GET", 
	            'header'=>"Content-Type: text/html; charset=iso-8859-1" 
	        ) 
	    ); 

	    $context = stream_context_create($opts); 
	    $result = @file_get_contents($fn,false,$context); 
    return $result; 
} 
	
}