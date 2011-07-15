<?php

namespace socialportal\controller;
use socialportal\repository\InstructionRepository;

use socialportal\model\Instruction;

use socialportal\common\topic\TypeCenter;

use core\tools\Mail;

use socialportal\model\UserProfileState;

use socialportal\model\UserProfileCountry;

use core\tools\CountryReader;

use core\debug\Logger;

use socialportal\model\PostFreetext;

use socialportal\model\PostBase;

use socialportal\model\TopicFreetext;

use socialportal\model\TopicBase;

use core\user\UserRoles;

use core\user\UserManager;

use socialportal\model\User;

use socialportal\model\Forum;

use core\security\Crypto;

use core\FrontController;

use core\AbstractController;

/**
 * Simple controller to make some administrative task like create password etc
 *
 */
class Tool extends AbstractController {
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function indexAction() {
		$refl = new \ReflectionClass( $this );
		$currentClass = $refl->getShortName();
		$parentClass = $refl->getParentClass();
		$links = array();
		$methods = $refl->getMethods();
		$parentMethods = $parentClass->getMethods();
		array_walk( $parentMethods, function (&$item, $key) {
			$item = $item->getName();
		} );
		foreach( $methods as $m ) {
			$name = $m->getName();
			if( in_array( $name, $parentMethods ) ) {
				continue;
			}
			if( false === strpos($name, 'Action')){
				continue;
			}
			$name = str_replace( 'Action', '', $name );
			$url = $this->frontController->getViewHelper()->createHref( $currentClass, $name );
			$links[$name] = $url;
		}
		$links['Back to home'] = $this->frontController->getViewHelper()->createHref( 'home' );
		$this->frontController->getResponse()->setVar( 'links', $links );
		$this->frontController->doDisplay( 'tool', 'displayAllTools' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function generateErrorMessageAction() {
		$this->frontController->addMessage( 'Test error', 'error' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function generateCorrectMessageAction() {
		$this->frontController->addMessage( 'Test correct', 'correct' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function generateInfoMessageAction() {
		$this->frontController->addMessage( 'Test info' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 * @_GetAttributes({key, password})
	 */
	public function directCreatePasswordAction() {
		$get = $this->frontController->getRequest()->query;
		$randomkey = $get->get( 'key', null );
		$password = $get->get( 'password', null );
		if( $randomkey && $password ) {
			$encoded = Crypto::encodeDBPassword( $randomkey, $password );
		} else {
			$encoded = null;
		}
		$this->frontController->getResponse()->setVar( 'encoded', $encoded );
		$this->frontController->doDisplay( 'tool', 'displayPassword' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 * Could be long as we want, cause it is called only once per installation
	 */
	public function createBaseForumAction() {
		$forumDiscussion = new Forum();
		$forumDiscussion->setName(  __('Discussions') );
		$forumDiscussion->setDescription(  __('Place where people can speak with others freely') );
		$forumDiscussion->setPosition( 1 );
		$forumDiscussion->setNumPosts( 0 );
		$forumDiscussion->setNumTopics( 0 );
		$this->em->persist( $forumDiscussion );
		
		$forumStories = new Forum();
		$forumStories->setName(  __('Stories') );
		$forumStories->setDescription(  __('Place where people narrate stories and explain their thoughts') );
		$forumStories->setPosition( 2 );
		$forumStories->setNumPosts( 0 );
		$forumStories->setNumTopics( 0 );
		$this->em->persist( $forumStories );
		
		$forumStrategies = new Forum();
		$forumStrategies->setName(  __('Strategies') );
		$forumStrategies->setDescription(  __('Place where people exchange strategies under list format') );
		$forumStrategies->setPosition( 3 );
		$forumStrategies->setNumPosts( 0 );
		$forumStrategies->setNumTopics( 0 );
		$this->em->persist( $forumStrategies );
		
		$forumActivities = new Forum();
		$forumActivities->setName( __('Activities') );
		$forumActivities->setDescription(  __('Place where people exchange activities and can gather some ideas') );
		$forumActivities->setPosition( 4 );
		$forumActivities->setNumPosts( 0 );
		$forumActivities->setNumTopics( 0 );
		$this->em->persist( $forumActivities );
		
		$forumSupport = new Forum();
		$forumSupport->setName(  __('Support') );
		$forumSupport->setDescription(  __('Place where people can ask for help from others') );
		$forumSupport->setPosition( 10 );
		$forumSupport->setNumPosts( 0 );
		$forumSupport->setNumTopics( 0 );
		$this->em->persist( $forumSupport );
		
		if( $this->em->flushSafe() ) {
			$metaRep = $this->em->getRepository( 'ForumMeta' );
			$result = true;
			$result &= $metaRep->setAcceptableTopics( $forumDiscussion->getId(), array( TypeCenter::$freetextType ) );
			$result &= $metaRep->setAcceptableTopics( $forumStories->getId(), array( TypeCenter::$simpleStoryType ) );
			$result &= $metaRep->setAcceptableTopics( $forumStrategies->getId(), array( TypeCenter::$strategyType ) );
			$result &= $metaRep->setAcceptableTopics( $forumActivities->getId(), array( TypeCenter::$activityType ) );
			$result &= $metaRep->setAcceptableTopics( $forumSupport->getId(), array( TypeCenter::$freetextType ) );
			if( $result ) {
				$this->frontController->addMessage( __( 'Creation of the base forum complete with metadata' ), 'correct' );
			} else {
				$this->frontController->addMessage( __( 'The metadata were not added/modified' ), 'info' );
			}
		} else {
			$this->frontController->addMessage( __( 'Creation of the base forum failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function createBaseUserAction() {
		$anonUser = UserManager::getAnonymousUser();
		$nullUser = UserManager::getNullUser();
		$admin = UserManager::createUser( 'admin', 'admin', 'w.follonier@netunion.com', UserRoles::$admin_role, time() );
		$this->em->persist( $nullUser );
		$this->em->persist( $anonUser );
		$this->em->persist( $admin );
		if( $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Creation of the default users completed' ), 'correct' );
		} else {
			$this->frontController->addMessage( __( 'Creation of the default users failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function createBaseInstructionsAction(){
		$instrRepo = $this->em->getRepository('Instruction');
		$instrRepo->createInstruction($instrRepo::$prefixTopicType, 'activity', __( 'The activity topics will be used to share activities with other' ));
		$instrRepo->createInstruction($instrRepo::$prefixTopicType, 'freetext', __( 'The freetext topics will be used to discuss with other' ));
		$instrRepo->createInstruction($instrRepo::$prefixTopicType, 'story', __( 'The story topics will be used to describe special situation' ));
		$instrRepo->createInstruction($instrRepo::$prefixTopicType, 'strategy', __( 'The strategy topics display a list of different strategies used to fight against a problem' ));
		
		// [new_username]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'username_change', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"You have requested a change of username.\n".
			"New username: %new_username%\n\n".
			"This username is used to connect to our site. Thank you for using our service.\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		// [new_username, reset_link]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'username_change_to_old', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"You have requested a change of username.\n".
			"New username: %new_username%\n\n".
			"If it was not you that ask the change or you wanted to undo this modification, please click on the following link to reset the username to the previous one.\n".
			"Link to reset username: %reset_link%\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		// [username]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'username_lost', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"You asked to receive your username. (if it is not the case, just ignore this email)\n".
			"Username: %username%\n\n".
			"This username is used to connect to our site. Thank you for using our service.\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		// [change_password_link]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'password_lost', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"You asked to change your password. (if it is not the case, just ignore this email)\n".
			"To be able to change your password, you will be asked to repeat the new two times.\n".
			"Link: %change_password_link%\n".
			"This password is used to connect to our site. Thank you for using our service.\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		// [username, password_hint]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'password_change', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"You have changed your account information (if it is not the case, just ignore this email)\n".
			"Username: %username%\n".
			"Password hint: %password_hint% \n".
			"This information is used to connect to our site. Thank you for using our service.\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		// [validation_link]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'email_validation', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"To complete your profile setup, you need to validate your email. (if you have not create/edit a profile on our site, just ignore this email)\n".
			"Please click on the following link\n".
			"Validation link: %validation_link%\n".
			"When you are done, you could connect with your account. Thank you for using our service.\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'email_information', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"Your email is updated (if you have not modify your email on our site, just ignore this email)\n".
			"Thank you for using our service.\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		// [new_email, reset_link]
		$instrRepo->createInstruction($instrRepo::$prefixEmail, 'email_reset', __(
			"** This is an automated message -- please do not reply. **\n".
			"Hi,\n".
			"You have requested a change of email.\n".
			"New email: %new_email%\n\n".
			"If it was not you that ask the change or you wanted to undo this modification, please click on the following link to reset the email to this one.\n".
			"Link to reset username: %reset_link%\n".
			"Best regards,\n\n".
			"Support Team"
		));
		
		
		if( $this->em->flushSafe() ){
			$this->frontController->addMessage( __( 'Creation of the default instructions completed' ), 'correct' );
		} else {
			$this->frontController->addMessage( __( 'Creation of the default instructions failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}

	/**
	 * @RoleAtLeast(administrator)
	 */
	public function createBaseCountriesAndStatesAction(){
		$file = '_config/countries_states.txt';
		$reader = new CountryReader();
		$reader->setFile($file);
		
		//where country = [countryCode, countryName, phoneCode, states]
		//	where states = [state]
		//		where state = [countryCode, shortName, stateName]
		$countries = $reader->getAllCountries();
		$entity = null;
		foreach ($countries as $country){
			$entity = new UserProfileCountry();
			$entity->setCountryCode($country['countryCode']);
			$entity->setCountryName(utf8_encode($country['countryName']));
			$entity->setPhoneCode($country['phoneCode']);
			$this->em->persist($entity);
			if(!$this->em->flushSafe()){
				$this->frontController->addMessage('Problem during creation of countries for country '.$country['countryName'], 'error');
				$this->frontController->doRedirect('Tool');
			}
			foreach($country['states'] as $state){
				$stateEntity = new UserProfileState();
				$stateEntity->setCountryId($entity->getId());
				$stateEntity->setShortName($state['shortName']);
				$stateEntity->setStateName(utf8_encode($state['stateName']));
				$this->em->persist($stateEntity);
			}
			if(!$this->em->flushSafe()){
				$this->frontController->addMessage('Problem during creation of states for country '.$country['countryName'], 'error');
				$this->frontController->doRedirect('Tool');
			}
		}
		$this->frontController->addMessage('Creation of the countries done.', 'correct');
		$this->frontController->doRedirect('Tool');
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function updateDatabaseAction(){
		include './scripts/create_database.php';
		$result = 1;
		if( $result ) {
			$this->frontController->addMessage( __( 'Creation of database complete' ), 'correct' );
		} else {
			$this->frontController->addMessage( __( 'Creation of database failed' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function createFloodFirstForumAction() {
		$get = $this->frontController->getRequest()->query;
		$numFlood = $get->get( 'num', 10 );
		
		$forumId = $this->em->getRepository( 'Forum' )->getFirstId();
		$typeId = $this->em->getRepository('ForumMeta')->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		
		for( $i = 1; $i <= $numFlood; $i++ ) {
			$base = new TopicBase();
			$topic = new TopicFreetext();
			
			$base->setCustomType( $typeId );
			$base->setForum( $this->em->getReference( 'Forum', $forumId ) );
			$base->setIsDeleted( 0 );
			$base->setIsOpen( 1 );
			$base->setIsSticky( 0 );
			$base->setLastposter( $this->em->getReference( 'User', UserManager::$nullUserId ) );
			$base->setNumPosts( 0 );
			if( !$this->frontController->getCurrentUser()->getId() ) {
				$base->setPoster( $this->em->getReference( 'User', UserManager::$anonUserId ) );
			} else {
				$base->setPoster( $this->frontController->getCurrentUser() );
			}
			
			$now = new \DateTime( '@' . ($this->frontController->getRequest()->getRequestTime()+$i), $this->frontController->getDateTimeZone() );
			$base->setStartTime( $now );
			$base->setTime( $now );
			$base->setTagCount( 0 );
			$base->setTitle( "flooooooooooood($i / $numFlood)" );
			
			$topic->setTopicbase( $base );
			$topic->setContent( 'floooooooooo oooooooooo ooooooooooo ooooooooooo ooooooooood' );
			
			$this->em->persist( $base );
			$this->em->persist( $topic );
		
		}
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( 'Forum flood failed', 'error' );
			$this->frontController->doRedirect( 'tool', 'index' );
		}
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId, $numFlood );
		$this->frontController->addMessage( "Forum flood success: $numFlood topics created", 'correct' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function createFloodFirstTopicAction() {
		$get = $this->frontController->getRequest()->query;
		$numFlood = $get->get( 'num', 1000 );
		
		$forumId = $this->em->getRepository( 'Forum' )->getFirstId();
		$typeId = $this->em->getRepository('ForumMeta')->getAcceptableTopics($forumId);
		$typeId = $typeId[0];
		
			$baseTopic = new TopicBase();
			$topic = new TopicFreetext();
			
			$baseTopic->setCustomType( $typeId );
			$baseTopic->setForum( $this->em->getReference( 'Forum', $forumId ) );
			$baseTopic->setIsDeleted( 0 );
			$baseTopic->setIsOpen( 1 );
			$baseTopic->setIsSticky( 0 );
			$baseTopic->setNumPosts( 0 );
			if( !$this->frontController->getCurrentUser()->getId() ) {
				$baseTopic->setPoster( $this->em->getReference( 'User', UserManager::$anonUserId ) );
				$baseTopic->setLastposter( $this->em->getReference( 'User', UserManager::$anonUserId ) );
			} else {
				$baseTopic->setPoster( $this->frontController->getCurrentUser() );
				$baseTopic->setLastposter( $this->frontController->getCurrentUser() );
			}
			$now = new \DateTime( '@' . $this->frontController->getRequest()->getRequestTime(), $this->frontController->getDateTimeZone() );
			$baseTopic->setStartTime( $now );
			$baseTopic->setTime( $now );
			$baseTopic->setTagCount( 0 );
			$baseTopic->setTitle( "flood receiver" );
			
			$topic->setTopicbase( $baseTopic );
			$topic->setContent( 'floood receiver,.........' );
			
			$this->em->persist( $baseTopic );
			$this->em->persist( $topic );
		
		for( $i = 1; $i <= $numFlood; $i++ ) {
			$basePost = new PostBase();
			$post = new PostFreetext();
			$basePost->setTopic($baseTopic);
			$basePost->setCustomType( $typeId );
			$basePost->setPosition( $i );
			$basePost->setPosterIp( $this->frontController->getRequest()->getClientIp() );
			$basePost->setIsDeleted( 0 );
			if( !$this->frontController->getCurrentUser()->getId() ) {
				$basePost->setPoster( $this->em->getReference( 'User', UserManager::$anonUserId ) );
			} else {
				$basePost->setPoster( $this->frontController->getCurrentUser() );
			}
			$now = new \DateTime( '@' . ($this->frontController->getRequest()->getRequestTime()+$i), $this->frontController->getDateTimeZone() );
			$basePost->setTime( $now );
			
			$post->setPostbase( $basePost );
			$post->setContent( "$i ) floooooooooo oooooooooo ooooooooooo ooooooooooo ooooooooood" );
			
			$this->em->persist( $basePost );
			$this->em->persist( $post );
		
		}
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( 'Topic flood failed', 'error' );
			$this->frontController->doRedirect( 'tool', 'index' );
		}
		$topicId = $baseTopic->getId();
		$this->em->getRepository( 'Forum' )->incrementTopicCount( $forumId, 1 );
		$this->em->getRepository( 'Forum' )->incrementPostCount( $forumId, $numFlood );
		$this->em->getRepository( 'TopicBase' )->incrementPostCount( $topicId, $numFlood );
		$this->frontController->addMessage( "Topic flood success: $numFlood post created", 'correct' );
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function recountAllTopicsAction() {
		$forumRep = $this->em->getRepository( 'Forum' );
		$forums = $forumRep->findAll();
		$total = 0;
		foreach( $forums as $f ) {
			$count = $forumRep->recountAllTopics( $f->getId() );
			$total += $count;
			$f->setNumTopics( $count );
			$this->em->persist( $f );
		}
		
		if( $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Recount topics complete total=%total%', array( '%total%' => $total ) ), 'info' );
		} else {
			$this->frontController->addMessage( __( 'Recount failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function recountAllPostsAction() {
		$forumRep = $this->em->getRepository( 'Forum' );
		$forums = $forumRep->findAll();
		$total = 0;
		$error = false;
		foreach( $forums as $f ) {
			$count = $forumRep->recountAllPosts( $f->getId() );
			if( false === $count ) {
				$error = true;
				break;
			}
			$total += $count;
			$f->setNumPosts( $count );
			$this->em->persist( $f );
		}
		
		if( !$error && $this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Recount posts complete total=%total%', array( '%total%' => $total ) ), 'info' );
		} else {
			$this->frontController->addMessage( __( 'Recount failed !' ), 'error' );
		}
		$this->frontController->doRedirect( 'tool', 'index' );
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function displayLogAction(){
		$filename = 'log.txt';
		$log = file_get_contents($filename);
		$this->frontController->doDisplay('tool', 'displayLog', array('log' => $log));
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function displayAllCountryAction(){
		$countries = $this->em->getRepository('UserProfileCountry')->findAll();
		$states = $this->em->getRepository('UserProfileState')->findAll();
		$this->frontController->doDisplay('tool', 'displayCountriesTest', array('countries' => $countries, 'states'=>$states));
	}
	
	/**
	 * @RoleAtLeast(administrator)
	 */
	public function sendFakeEmailAction(){
		$currentTime = $this->frontController->getRequest()->getRequestTime();
		$currentTime = date('H:i A', $currentTime);
		
//		$to      = 'w.follonier@netunion.com';
		$to      = 'wadeck.follonier@gmail.com';
		$subject = 'Autre sujet @ ' .$currentTime ." ".rand(0, 10000);
		$message = 'Test ! @ ' .$currentTime;
		$headers = array(
//		'From: webmaster@example.com' . "\r\n" .
//			'Reply-To: webmaster@example.com' . "\r\n" .
			'X-Mailer' => 'PHP/' . phpversion()
		);
	
		$result = Mail::send($to, $subject, $message, $headers);
		if($result){
			$this->frontController->addMessage('Mail sent successfully', 'correct');
		}else{
			$this->frontController->addMessage('Failure during mail shipment', 'error');
		}
		
		$this->frontController->doRedirect('Tool');
	}
	
//	/**
//	 * @RoleAtLeast(administrator)
//	 */
//	public function doMaintenanceAction(){
//		$curr = getcwd();
//		$filename = '.htaccess';
//		$exist = file_exists($filename);
//		$readable = is_readable($filename);
//		$modifiable = is_writable($filename);
//		$content = file_get_contents($filename);
//		$content = str_replace('/SocialPortal/index.php', '/SocialPortal/index2.php', $content);
//		file_put_contents($filename, $content);
//	}
}
