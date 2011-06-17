<?php

namespace core;

use core\debug\Logger;

use core\http\bags\ResponseHeaderBag;

class Response {
	public static $statusTexts = array( 200 => 'OK', 301 => 'Moved Permanently', 302 => 'Found', 403 => 'Forbidden', 404 => 'Not Found', 500 => 'Internal Server Error' );
	
	private $vars = array();
	/** @var core\http\bags\ResponseHeaderBag */
	private $headers;
	/** @var array */
	private $cookies;
	private $body;
	/** Protocol version 1.0 or 1.1 @var string */
	private $version;
	/** 200, 403, 404, 500 @var int */
	private $statusCode;
	/** @var string */
	private $statusText;
	/** UTF-8 @var string */
	private $charset;
	/** @var string*/
	private $title;
	/** @var array */
	private $desiredCss = array();
	/** @var array */
	private $desiredJs = array();
	
	private $containerClass = 'rounded-box padded';
	
	/** 
	 * Typically translation arrays
	 * @var array Variables that will be passed to javascript
	 */
	private $javascriptVars = array();
	
	/** @var string */
	private $favicon;
	
	public function __construct($content = '', $status = 200, array $headers = array()) {
		$this->setBody( $content );
		$this->setStatusCode( $status );
		$this->setProtocolVersion( '1.0' );
		$this->headers = new ResponseHeaderBag( $headers );
		$this->charset = 'UTF-8';
		// reset.css is used to put all browser to the same base
		$this->addCssFile( 'reset.css' );
		$this->addCssFile( 'default.css' );
	}
	
	/**
	 * Add a css file to the list of files that will be added when we'll build the response
	 * @param string $cssFile could either be default.css or forum/table.css
	 */
	public function addJsFile($jsFile) {
		if( false === strpos( $jsFile, '.js' ) ) {
			$jsFile .= '.js';
		}
		$jsFile = 'http://' . $_SERVER['HTTP_HOST'] . '/' . FrontController::$SITE_NAME . '/' . FrontController::$JS_DIR . $jsFile;
		if( !in_array( $jsFile, $this->desiredJs ) ) {
			$this->desiredJs[] = $jsFile;
		}
	}
	
	public function addJsVar($name, $assoc) {
		$this->javascriptVars[$name] = $assoc;
	}
	
	/**
	 * Add a js file to the list of files that will be added when we'll build the response
	 * @param string $cssFile could either be default.js or forum/table.js
	 */
	public function addCssFile($cssFile) {
		if( false === strpos( $cssFile, '.css' ) ) {
			$cssFile .= '.css';
		}
		$cssFile = 'http://' . $_SERVER['HTTP_HOST'] . '/' . FrontController::$SITE_NAME . '/' . FrontController::$CSS_DIR . $cssFile;
		if( !in_array( $cssFile, $this->desiredCss ) ) {
			$this->desiredCss[] = $cssFile;
		}
	}
	
	//TODO perhaps a better way to manager variables, look into symfony
	public function getVar($key, $default = null) {
		if( isset( $this->vars[$key] ) ) {
			return $this->vars[$key];
		} else {
			return $default;
		}
	}
	
	public function setVar($key, $value) {
		$this->vars[$key] = $value;
	}
	
	public function getVars() {
		return $this->vars;
	}
	
	/** Replace the current vars by the given ones, especially use to simulate a buffer in insertModule */
	public function setVars(array $vars) {
		$this->vars = $vars;
	}
	
	/** Remove the vars and return them, especially use to simulate a buffer in insertModule */
	public function removeAllVars() {
		$result = $this->vars;
		$this->vars = array();
		return $result;
	}
	
	/** @param string $body The real content of the page with html tags etc */
	public function setBody($body) {
		$this->body = $body;
	}
	/** @param string $title The title that will be displayed in the top bar of the browser */
	public function setTitle($title) {
		$this->title = $title;
	}
	/** @param string $favicon The link to the file containing the icon */
	public function setFavicon($favicon) {
		$this->favicon = $favicon;
	}
	
	/**
	 * Sets the HTTP protocol version (1.0 or 1.1).
	 *
	 * @param string $version The HTTP protocol version
	 */
	public function setProtocolVersion($version) {
		$this->version = $version;
	}
	
	/**
	 * Gets the HTTP protocol version.
	 *
	 * @return string The HTTP protocol version
	 */
	public function getProtocolVersion() {
		return $this->version;
	}
	
	/**
	 * Sets response status code.
	 *
	 * @param integer $code HTTP status code
	 * @param string  $text HTTP status text
	 *
	 * @throws \InvalidArgumentException When the HTTP status code is not valid
	 */
	public function setStatusCode($code, $text = null) {
		$this->statusCode = ( int ) $code;
		//        if ($this->isInvalid()) { throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));}
		

		$this->statusText = false === $text ? '' : (null === $text ? self::$statusTexts[$this->statusCode] : $text);
	}
	
	/** @return string Status code */
	public function getStatusCode() {
		return $this->statusCode;
	}
	
	/** @param string $charset Character set */
	public function setCharset($charset) {
		$this->charset = $charset;
	}
	
	/** @return string Character set */
	public function getCharset() {
		return $this->charset;
	}
	
	/**
	 * Sends HTTP headers.
	 */
	protected function sendHeaders() {
		$this->fixContentType();
		
		// status
		header( sprintf( 'HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText ) );
		
		// headers
		foreach( $this->headers->all() as $name => $values ) {
			foreach( $values as $value ) {
				header( $name . ': ' . $value );
			}
		}
		//TODO remove after debug
		$cs = $this->headers->getCookies();
		if( $cs ) {
			Logger::getInstance()->log( "Header cookies: " . print_r( $cs, true ) );
		}
		$cs = $this->cookies;
		if( $cs ) {
			Logger::getInstance()->log( "Simple cookies: " . print_r( $cs, true ) );
		}
		// cookies
		foreach( $this->headers->getCookies() as $cookie ) {
			setcookie( $cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly() );
		}
		$sname = session_name();
		foreach( $this->cookies as $key => $value ) {
			if( $key === $sname ) {
				Logger::getInstance()->log( "Avoid storing session name in cookie $key=>$value" );
				continue;
			} else {
				Logger::getInstance()->log( "Storing cookie $key=>$value" );
			}
			if( $value ) {
				setcookie( $key, $value, time() + 60 * 60 * 24 * 15, '/' );
			} else {
				setcookie( $key, null, 1, '/' );
			}
		
		// timeout 15 days
		}
	}
	
	protected function fixContentType() {
		if( !$this->headers->has( 'Content-Type' ) ) {
			$this->headers->set( 'Content-Type', 'text/html; charset=' . $this->charset );
		} elseif( 'text/' === substr( $this->headers->get( 'Content-Type' ), 0, 5 ) && false === strpos( $this->headers->get( 'Content-Type' ), 'charset' ) ) {
			// add the charset
			$this->headers->set( 'Content-Type', $this->headers->get( 'Content-Type' ) . '; charset=' . $this->charset );
		}
	}
	public function printOut() {
		$this->sendHeaders();
		if( !$this->title ) {
			$this->title = 'Social Portal';
		}
		//@formatter:off
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php
		echo $this->title;
		?></title>
	<?php if( $this->favicon ) : ?>
		<link rel="shortcut icon" href="<?php echo $this->favicon; ?>" type="image/x-icon" />		
	<?php endif ;
	$this->insertCSS();
	$this->insertJavascript();
	?>
</head>
<body><?php
		echo $this->body;
		?>
</body>
</html><?php
	
		//@formatter:on
	}
	
	/**
	 * @param array $cookies key=>value, could contains the session_name => session_id, but will not be sent (actually it's done by php directly)
	 */
	public function setCookies($cookies) {
		Logger::getInstance()->debug( 'Cookies received: ' . var_export( $cookies, true ) );
		$this->cookies = $cookies;
	}
	
	private function insertCSS() {
		if( !$this->desiredCss ) {
			return;
		}
		$result = '';
		foreach( $this->desiredCss as $css ) {
			$result .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
		}
		echo $result;
	}
	
	private function insertJavascript() {
		if( !$this->desiredJs ) {
			return;
		}
		$result = '';
		foreach( $this->desiredJs as $js ) {
			$result .= '<script type="text/javascript" src="' . $js . '"></script>';
		}
		if( $this->javascriptVars ) {
			$result .= '<script type="text/javascript">/* <![CDATA[ */';
			foreach( $this->javascriptVars as $name => $assoc ) {
				$result .= "var $name={";
				$temp = array();
				foreach( $assoc as $key => $value ) {
					$temp[] = "$key: \"$value\"";
				}
				$result .= implode( ',', $temp );
				$result .= '};';
			}
			$result .= '/* ]]> */</script>';
		}
		echo $result;
	}
	
	/** @return true if the status code is in (201, 301, 302, 303, 307) */
	public function isRedirect() {
		return in_array( $this->statusCode, array( 201, 301, 302, 303, 307 ) );
	}
	
	/** 
	 * Internal use only, to display the css class of the container 
	 * @return string
	 */
	public function getContainerClass() {
		return $this->containerClass;
	}
	
	/**
	 * The container class is the css class we'll add to the container, the div that contains all the things that are not in header neither in footer
	 * @param string $class css class, default is 'rounded-box padded'
	 */
	public function setContainerClass($class = 'rounded-box padded') {
		$this->containerClass = $class;
	}
}