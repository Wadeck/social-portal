<?php

namespace core\http;

use core\Config;

use core\FrontController;

use core\tools\Secured;

use core\debug\Logger;

use core\tools\Utils;

use core\http\storage\NativeSessionStorage;

use core\http\Session;

use core\http\bags\ParameterBag;
use core\http\bags\ServerBag;
use core\http\bags\HeaderBag;

use DateTime;
use DateTimeZone;

class Request {
	/** @var core\http\bags\ParameterBag */
	public $attributes;
	
	/** $_GET @var core\http\bags\ParameterBag */
	public $query;
	
	/** $_POST @var core\http\bags\ParameterBag */
	public $request;
	
	/** $_SERVER @var core\http\bags\ParameterBag */
	public $server;
	
	/** $_FILES @var core\http\bags\ParameterBag */
	public $files;
	
	/** $_COOKIE @var core\http\bags\ParameterBag */
	public $cookies;
	
	/** @var core\http\bags\HeaderBag */
	public $headers;
	
	/** @var string */
	private $method;
	/** $_SESSION @var Session */
	protected $session;
	
	/** @var string */
	public $module;
	/** @var string */
	public $action;
	/**
	 * @var array
	 * @warning not the same as request, they are not the GET POST attributes !
	 */
	public $parameters;
	
	/** @var string */
	private $requestedUrl;
	
	/** @var integer unix timestamp at the arrival of that request */
	private $requestTime;
	
	/** @var DateTime */
	private $requestDateTime;
	
	/**
	 * Constructor.
	 *
	 * @param array  $query      The GET parameters
	 * @param array  $request    The POST parameters
	 * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
	 * @param array  $cookies    The COOKIE parameters
	 * @param array  $files      The FILES parameters
	 * @param array  $server     The SERVER parameters
	 */
	public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), DateTimeZone $timezone) {
		$this->query = new ParameterBag( $query );
		$this->request = new ParameterBag( $request );
		$this->attributes = new ParameterBag( $attributes );
		$this->cookies = new ParameterBag( $cookies );
		$this->server = new ServerBag( $server );
		$this->headers = new HeaderBag( $this->server->getHeaders() );
		
		$this->requestTime = isset($server['REQUEST_TIME']) ? $server['REQUEST_TIME'] : time();
		$this->requestDateTime = new DateTime('@'.$this->requestTime, $timezone);
	}
	
	/**
	 * Creates a new request with values from PHP's super globals.
	 *
	 * @return Request A new request
	 */
	public static function createFromGlobals(DateTimeZone $timezone) {
		return new static( $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER, $timezone);
	}
	
	/** @return unix time at the arrival of the request */
	public function getRequestTime() {
		return $this->requestTime;
	}
	/** @return unix time at the arrival of the request */
	public function getRequestDateTime() {
		return $this->requestDateTime;
	}
	
	/** @return the requested url */
	public function getRequestedUrl() {
		return $this->requestedUrl;
	}
	
	/** @return Session */
	public function getSession() {
		return $this->session;
	}
	
	public function hasSession() {
		// the check for $this->session avoids malicious users trying to fake a session cookie with proper name
		return $this->cookies->has( session_name() ) && null !== $this->session;
	}
	
	public function setSession(Session $session) {
		$this->session = $session;
	}
	
	/**
	 * Returns the client IP address.
	 *
	 * @param  Boolean $proxy Whether the current request has been made behind a proxy or not
	 *
	 * @return string The client IP address
	 */
	public function getClientIp($proxy = false) {
		if( $proxy ) {
			if( $this->server->has( 'HTTP_CLIENT_IP' ) ) {
				return $this->server->get( 'HTTP_CLIENT_IP' );
			} elseif( $this->server->has( 'HTTP_X_FORWARDED_FOR' ) ) {
				return $this->server->get( 'HTTP_X_FORWARDED_FOR' );
			}
		}
		
		return $this->server->get( 'REMOTE_ADDR' );
	}
	
	/**
	 * Returns true if the request is a XMLHttpRequest.
	 *
	 * It works if your JavaScript library set an X-Requested-With HTTP header.
	 * It is known to work with Prototype, Mootools, jQuery.
	 *
	 * @return Boolean true if the request is an XMLHttpRequest, false otherwise
	 */
	public function isXmlHttpRequest() {
		return 'XMLHttpRequest' == $this->headers->get( 'X-Requested-With' );
	}
	
	/**
	 * Parse the requested url and retrieve the module/action/gets
	 * @return (module, action, gets)
	 */
	public function parseUrl($url) {
		if(!$url){
			throw new \InvalidArgumentException('No $url in parseUrl');
		}
		
		$urlAssoc = parse_url( $url );
		
		$path = $urlAssoc['path'];
		$requestUrl = $path;
		$gets = array(); 
		if(isset($urlAssoc['query']) && $urlAssoc['query']){
			$requestUrl .= '?'.$urlAssoc['query'];
			
			$temp = explode('&', $urlAssoc['query']);
		    foreach ($temp as $t) { 
		        list($k, $v) = explode('=', $t); 
		        $gets[$k] = $v;
		    }    
		}

		// +1 for the / at the end
		$siteName = Config::getOrDie('site_name');
		$requestPath = substr( $path, strpos( $path, $siteName) + strlen( $siteName ) + 1 );
		
		if( $requestPath ) {
			$tokens = explode( '/', $requestPath );
			$this->module = array_shift( $tokens );
			$this->action = array_shift( $tokens );
			$this->parameters = $tokens;
		} else {
			$this->module = 'home';
			$this->action = 'index';
			$this->parameters = array();
		}
		
		// same format as the url builder in frontController
		$this->requestedUrl = $requestUrl;
		
		// debug mode in eclipse
		if( 'index.php' == $this->module ) {
			$this->module = 'home';
		}
		// when we don't specify the action, it is automatically set to index
		if( '' == $this->action ) {
			$this->action = 'index';
		}

		if($this->parameters){
			Logger::getInstance()->debug_var('There are some get attributes in the url !', $this->parameters);
		}
		Logger::getInstance()->log( "Request path: {$this->module} :: {$this->action} :: " . ($this->parameters ? print_r( $this->parameters, true ) : '') );
		
		return array( $this->module, $this->action, $gets);
	}
	
	/**
	 * Parse the http requested url (from $_SERVER)
	 * @return (module, action)
	 */
	public function parseDefaultUrl() {
		$urlAssoc = parse_url( $_SERVER['REQUEST_URI'] );
		$path = $urlAssoc['path'];
		$requestUrl = $path;
		if(isset($urlAssoc['query']) && $urlAssoc['query']){
			$requestUrl .= '?'.$urlAssoc['query'];
		}
		
		// +1 for the / at the end
		$siteName = Config::getOrDie('site_name');
		$requestPath = substr( $path, strpos( $path, $siteName) + strlen( $siteName ) + 1 );
		if( $requestPath ) {
			$tokens = explode( '/', $requestPath );
			$this->module = array_shift( $tokens );
			$this->action = array_shift( $tokens );
			$this->parameters = $tokens;
		} else {
			$this->module = 'Connection';
			$this->action = 'index';
			$this->parameters = array();
		}
		// same format as the url builder in frontController
		$this->requestedUrl = $requestUrl;
		
		// debug mode in eclipse
		if( 'index.php' == $this->module ) {
			$this->module = 'home';
		}
		// when we don't specify the action, it is automatically set to index
		if( '' == $this->action ) {
			$this->action = 'index';
		}
		Logger::getInstance()->log( "Request path: {$this->module} :: {$this->action} :: " . ($this->parameters ? print_r( $this->parameters, true ) : '') );
		
		return array( $this->module, $this->action);
	}
	
	/**
	 * Gets the request method.
	 *
	 * @return string The request method
	 */
	public function getMethod() {
		if( null === $this->method ) {
			$this->method = strtoupper( $this->server->get( 'REQUEST_METHOD', 'GET' ) );
		}
		
		return $this->method;
	}
	
	/** @return string The url that was asked, false if not specified */
	public function getReferrer() {
		$refFieldName = Config::get('_referrer_field_name', '_http_referrer');
		$ref = $this->request->get( $refFieldName, null );
		$ref = $ref ? $ref : $this->query->get( $refFieldName, null );
		return $ref;
	}
	
	public function getPOSTAttribute($key, $default, $cleanText = true) {
		$result = $this->request->get( $key, $default );
		if( $cleanText ) {
			$result = Utils::getCleanText( $result );
		}
		return $result;
	}
	public function getGETAttribute($key, $default, $cleanText = true) {
		$result = $this->query->get( $key, $default );
		if( $cleanText ) {
			$result = Utils::getCleanText( $result );
		}
		return $result;
	}
}
