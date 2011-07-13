<?php
namespace core;
use core\tools\Utils;

require_once 'i18n/language.php';

use core\http\Request;

use core\http\Response;

use core\security\NonceManager;

use core\debug\Logger;

use socialportal\model\User;

use core\http\storage\NativeSessionStorage;

use core\http\Session;

use core\user\UserEntityProvider;

use core\user\UserManager;

use core\ViewHelper;
use Doctrine\ORM\EntityManager;

use core\security\Firewall;

use core\http\exceptions\ControllerNotFoundException;

use core\http\exceptions\InvalidControllerClassException;

use core\http\exceptions\NoSuchActionException;

use core\http\exceptions\ThrowableException;

use core\http\exceptions\UnexpectedException;

use core\http\exceptions\CustomException;

use core\http\exceptions\RedirectLoopAvoidingException;

use core\http\exceptions\PageNotFoundException;

use core\tools;

use core\http\exceptions;

use core\security;

use core\http;

use Doctrine\Common;

use Exception;

use DateTimeZone;
class FrontController {
	/** @var core\FrontController */
	private static $instance = null;
	
	/** @var ClassLoader */
	public $loader;
	
	/** @var AnnotationRetriever */
	private $annotationRetriever;
	/** @var Firewall */
	private $firewall;
	/** @var User */
	private $user;
	/** @var ViewHelper */
	private $viewHelper;
	/** @var UserManager */
	private $userManager;
	/** @var NonceManager */
	private $nonceManager;
	
	/** @var Request */
	private $request;
	/** @var Response */
	private $response;
	
	/** @var EntityManager */
	private $em;
	
	/** 
	 * performance issue, we use a cache to know when the controller are already loaded,
	 * especially when calling multiple time doAction
	 * @var array
	 */
	private $alreadyIncludedController;
	
	/** @var bool Determine if the display function was called for the first time or not */
	private $firstCallDisplay;
	
	/** @var DateTimeZone */
	private $dateTimeZone;
	
	private function __construct() {
		Config::create('_config' . DIRECTORY_SEPARATOR . 'config.ini');
		
		$this->em = DoctrineLink::getEntityManager();
		
		$defaultTimeZone = "Europe/Zurich";
		$timezone = Config::get('timezone', $defaultTimeZone);
//		if(!date_default_timezone_set( $timezone )){
//			Logger::getInstance()->log("The timezone passed as argument is not valid [$timezone]");	
//			$this->dateTimeZone = new DateTimeZone($defaultTimeZone);
//		}else{
			$this->dateTimeZone = new DateTimeZone($timezone);
//		}

		$this->request = Request::createFromGlobals($this->dateTimeZone);
		
		$this->request->setSession( new Session( new NativeSessionStorage( array( 'lifetime' => 0 ) ) ) );
		$this->request->getSession()->start();
		
		$this->response = new Response();
		$this->loader = ClassLoader::getInstance();
		$this->userManager = new UserManager( $this->request, new UserEntityProvider( $this->em ) );
		$this->firewall = new Firewall( $this, $this->userManager );
		$this->firstCallDisplay = true;
	}
	
	public static function getInstance() {
		if( !self::$instance ) {
			self::$instance = new FrontController();
		}
		return self::$instance;
	}
	
	/** 
	 * Entry point of the class
	 * Only be called by index.php !!!
	 * @exit
	 */
	public function dispatch() {
		list( $module, $action ) = $this->request->parseDefaultUrl();
		
		if($_GET){
			Logger::getInstance()->debug_var( '$_GET', $_GET );
		}
		if($_POST){
			Logger::getInstance()->debug_var( '$_POST', $_POST );
		}
		if($_COOKIE){
			Logger::getInstance()->debug_var( '$_COOKIE', $_COOKIE );
		}
		if($_SESSION){
			Logger::getInstance()->debug_var( '$_SESSION', $_SESSION );
		}
		if($_FILES){
			Logger::getInstance()->debug_var( '$_FILES', $_FILES );
		}
		// [Authentification] check who is the current user
		// could lead to exit ? / redirect ? Not for the moment
		$this->user = $this->firewall->getAuthentificatedUser();
		$this->doAction( $module, $action );
		exit();
	}
	/** 
	 * Used when a form is not valid, to redirect internally
	 *	without real redirection to keep form fields values
	 * Will modify the get attributes
	 * @param string $url Optionally the url we want to use
	 * @exit
	 */
	public function dispatchUrl($url) {
		//TODO verify if there is not a problem with response vars !
		list( $module, $action, $gets ) = $this->request->parseUrl( $url, true );
		$this->request->query->replace( $gets );
		$this->doAction( $module, $action );
		exit();
	}
	
	/**
	 * Could be call from a controller that see the previous action is not in his domain
	 * @param string $module The controller name
	 * @param string $action The action name (corresponding to the function name .'Action'
	 * @param array $getAttributes Array of parameter in associative manner passed to the action by get method
	 */
	//	public function doAction($module, $action = '', array $getAttributes = array()) {
	public function doAction($module, $action = '') {
		$action = $action ? $action : 'index';
		$name = ucfirst( strtolower( $module ) );
		$className = Config::getOrDie('controller_dir') . $name;
		try {
			if( !isset( $this->alreadyIncludedController[$name] ) ) {
				// find the corresponding controller
				// use the function directly to avoid generating fatal error
				$canLoad = $this->loader->loadClass( $className );
			} else {
				$canLoad = true;
			}
			if( false === $canLoad ) {
				$this->generateException( new ControllerNotFoundException( $className ) );
			}
			$controller = new $className();
			if( !($controller instanceof AbstractController) ) {
				$this->generateException( new InvalidControllerClassException( $className ) );
			}
			
			// initialize the controller attributes
			$controller->setFrontController( $this );
			$controller->setEntityManager( $this->em );
			
			$methodName = $action . 'Action';
			
			// check if the action exist in that controller
			if( !method_exists( $controller, $methodName ) ) {
				$this->generateException( new NoSuchActionException( $className, $methodName ) );
			}
			
			// [Authorization] check if the user has the right to access this action
			// could lead to exit
			$this->firewall->checkAuthorization( $controller, $methodName );
			
			$this->alreadyIncludedController[$name] = true;
			
			$controller->actionBefore( $action );
			$controller->$methodName();
			$controller->actionAfter( $action );
		
		} catch ( Exception $e ) {
			if( $e instanceof ThrowableException ) {
				// in the case the exception thrown used a Throwable wrapper we can retrieve it
				// this could append typically in the $methodName() action
				$this->generateException( $e->getCustomException() );
			} else {
				Logger::getInstance()->debug( "Unknown Exception thrown: ". get_class($e) );
				Logger::getInstance()->debug( "Trace: " . $e->getTraceAsString() );
				Logger::getInstance()->debug( "Code: " . $e->getCode());
				Logger::getInstance()->debug( "Line: " . $e->getLine());
				Logger::getInstance()->debug( "Previous: " . $e->getPrevious());
				// like the name says, we don't expect to have another exception type
				// so we wrap it into UnexpectedException to be able to go through generateException
				$this->generateException( new UnexpectedException( $e ) );
			}
		}
	}
	
	/**
	 * When we want to make a redirection to the given module/action/...
	 * @param string $module
	 * @param string $action
	 * @param string $params
	 * @param string $gets
	 * @exit
	 */
	public function doRedirect($module, $action = '', array $gets = array(), $targetId=false, $noLoopControl = false) {
		$url = $this->getViewHelper()->createHref( $module, $action, $gets, $targetId );
		$this->doRedirectUrl( $url, $noLoopControl );
	}
	
	/**
	 * Short method to redirect to the referrer, this save one lines of code
	 * @param string $message Shortcut to addMessage
	 * @param string $type error|info|correct
	 * @param boolean $noLoopControl To check if the redirection is looping
	 * @exit
	 */
	public function doRedirectToReferrer($message=false, $type='info', $noLoopControl = false){
		if(false !== $message){
			$this->addMessage($message, $type);
		}
		$url = $this->getRequest()->getReferrer();
		$this->doRedirectUrl($url, $noLoopControl = false);
	}
	
	/**
	 * When we want to make a redirection to the given module/action/...
	 * @param string $nonceAction Not hashed
	 * @param string $module
	 * @param string $action
	 * @param string $params
	 * @param string $gets
	 * @exit
	 */
	public function doRedirectWithNonce($nonceAction, $module, $action = '', array $gets = array(), $targetId=false, $noLoopControl = false) {
		$url = $this->getViewHelper()->createHrefWithNonce($nonceAction, $module, $action, $gets, $targetId );
		$this->doRedirectUrl( $url, $noLoopControl );
	}
	
	/**
	 * When we want to make a redirection to the given url (typically when we have referrer)
	 * @exit
	 */
	public function doRedirectUrl($url, $noLoopControl = false) {
		$prevUrl = $this->request->getRequestedUrl();
		if( !$noLoopControl && $url == $prevUrl ) {
			$this->generateException( new RedirectLoopAvoidingException( $url ) );
		}
		$this->request->getSession()->setFlash( 'redirectFrom', $prevUrl );
		
		$this->response = new http\RedirectResponse( $url );
		//		$this->response->redirect($url);
		$this->response->setCookies( $this->request->cookies->all() );
		$this->response->printOut();
		Logger::getInstance()->log( "Redirect to $url from $prevUrl" );
		exit();
	}
	
	/**
	 * Ask the front controller to include the corresponding view
	 * @param string $module The name of the view
	 * @param string $action The subdirectory of the view (could be null
	 * @param string $parameters Associative array passed to the view
	 */
	public function doDisplay($module, $action = null, array $addVars = array()) {
		$module = strtolower( $module );
		$viewDir = Config::getOrDie('view_dir');
		$fileName = $viewDir . $module;
		if( $action ) {
			$fileName .= DIRECTORY_SEPARATOR . $action;
		}
		
		$file = $this->loader->getFileName( $fileName, '.phtml' );
		if( false === $file ) {
			$this->generateException( new PageNotFoundException( $module, $action ) );
		}
		
		if( !$this->firstCallDisplay ) {
			// we are in the buffer
			echo $this->renderFile( $file, $addVars );
			return;
		} else {
			$this->firstCallDisplay = false;
		}
		
		// comes from ajax, so we don't display header / footer
		// it's the case where we want only to display the content
		if( $this->getRequest()->isXmlHttpRequest() ) {
			$fromAjax = true;
		} else {
			$fromAjax = false;
			// header_footer.css is used for the common part like header / footer
			$this->getResponse()->addCssFile( 'header_footer.css' );
			
			$headerFileName = $viewDir . 'header';
			$headerFileName = $this->loader->getFileName( $headerFileName, '.phtml' );
			if( false === $headerFileName ) {
				$this->generateException( new PageNotFoundException( $headerFileName ) );
			}
			
			$footerFileName = $viewDir . 'footer';
			$footerFileName = $this->loader->getFileName( $footerFileName, '.phtml' );
			if( false === $footerFileName ) {
				$this->generateException( new PageNotFoundException( $footerFileName ) );
			}
		}
		// to authorize the view to modify some parameters of the header / footer (by viewHelper->setContainerClass by example)
		$content = $this->renderFile( $file, $addVars );
		$body = '';
		if( !$fromAjax ) {
			$body .= $this->renderFile( $headerFileName );
			$body .= '<div id="container" class="' . $this->getResponse()->getContainerClass() . '">';
			$body .= $content;
			$body .= '</div>';
			$body .= $this->renderFile( $footerFileName );
		} else {
			$body = $content;
		}
		$this->response->setBody( $body );
		$this->response->setCookies( $this->request->cookies->all() );
		$this->response->printOut();
	}
	
	/**
	 * Include the specific file
	 * Must ensure that the file exist !
	 * @param string $file
	 * @param $addVars array assoc key=>value we want to pass to the view, like response->setVar()
	 */
	public function renderFile($file, array $addVars = array()) {
		// the variable that will be passed to the file
		$vars = $this->response->getVars();
		if( $addVars ) {
			$vars = array_merge( $vars, $addVars );
		}
		$vars['helper'] = $this->getViewHelper();
		
		// start a buffer
		ob_start();
		// render the view inside the buffer
		require ($file);
		// and retrieve the buffer content
		$str = ob_get_clean();
		
		unset( $vars );
		return $str;
	}
	
	/**
	 * //TODO should redirect to the given error page to custom them
	 * 'Catch' exception and send the corresponding page
	 * @param CustomException $customException
	 * @warning Exit method !
	 */
	public function generateException(CustomException $customException) {
		Logger::getInstance()->debug( 'Exception thrown: ' . var_export( $customException, true ) );
		$message = $customException->getUserMessage();
		ob_get_clean();
		ob_start();
		$code = $customException->getCode();
		switch ( $code ) {
			case 404 :
			default :
				echo '<h1>404</h1><br>';
				if( !$message ) {
					$message = __( 'The desired page does not exist' );
				}
				echo "<p>$message</p>";
				break;
			case 403 :
				echo '<h1>403</h1><br>';
				if( !$message ) {
					$message = __( 'Forbidden Access' );
				}
				echo "<p>$message</p>";
				break;
			case 500 :
				echo '<h1>500</h1><br>';
				if( !$message ) {
					$message = __( 'Internal error occurred' );
				}
				echo "<p>$message</p>";
				break;
		}
		$body = ob_get_clean();
		$this->response = new Response();
		$this->response->setBody( $body );
		$this->response->setCookies( $this->request->cookies->all() );
		$this->response->printOut();
		exit();
	}
	
	// normally the view should not have to add message,
	// it's only the controllers that do actions
	/**
	 * Add a message to the cookies to be used in the next page (useful with redirect)
	 * @param string $message The message to display, just a string without format
	 * @param string $type 'info'|'error'|'correct'
	 */
	public function addMessage($message, $type = 'info') {
		$this->request->getSession()->setFlash( "notice_$type", $message );
	}
	
	/** @return Response */
	public function getResponse() {
		return $this->response;
	}
	
	/** @return Request */
	public function getRequest() {
		return $this->request;
	}
	
	/** @return User */
	public function getCurrentUser() {
		return $this->user;
	}
	
	public function setCurrentUser(User $user) {
		$this->user = $user;
	}
	
	/** @return UserManager */
	public function getUserManager() {
		return $this->userManager;
	}
	
	/** @return ViewHelper */
	public function getViewHelper() {
		// lazy loading to gain time when we do a redirect
		if( !$this->viewHelper ) {
			$this->viewHelper = new ViewHelper( $this, $this->request, $this->response );
		}
		return $this->viewHelper;
	}
	
	/** @return NonceManager */
	public function getNonceManager() {
		// lazy loading because we don't need in each page a nonceManager
		if( !$this->nonceManager ) {
			$this->nonceManager = new NonceManager( $this );
		}
		return $this->nonceManager;
	}
	
	/** @return EntityManager */
	public function getEntityManager(){
		return $this->em;
	}
	
	/** @return DateTimeZone */
	public function getDateTimeZone(){
		return $this->dateTimeZone;	
	}
	
	/**
	 * @var string specific nonce used only to buffer a nonce when we want to add a particular module inside another
	 * in other case the normal nonce is used
	 */
	private $nonce;
	/**
	 * This nonce can be modified by using setNonce
	 * @return string the current nonce
	 */
	public function getCurrentNonce() {
		if( $this->nonce ) {
			return $this->nonce;
		}
		$nonce = $this->getRequest()->request->get( '_nonce', null );
		if( null === $nonce ) {
			$nonce = $this->getRequest()->query->get( '_nonce', null );
		}
		return $nonce;
	}
	
	public function setNonce($nonce) {
		$this->nonce = $nonce;
	}
	// FIXME better way to do that, for the moment it just works ...
	// not linked with different nonce per href etc... could lead to problem in some sub request
	public function getNonceGET() {
		return $this->getRequest()->query->get( '_nonce', null );
	}
	public function getNoncePOST() {
		return $this->getRequest()->request->get( '_nonce', null );
	}
}