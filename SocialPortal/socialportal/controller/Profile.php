<?php

namespace socialportal\controller;
use core\debug\Logger;

use core\Config;

use socialportal\model\User;

use core\tools\Utils;

use core\templates\ProfileTemplate;

use core\form\custom\ProfileForm;

use socialportal\model\UserProfile;

use core\form\custom\RegisterForm;

use core\user\UserHelper;

use core\form\custom\LoginForm;

use core\FrontController;

use core;

use core\AbstractController;
use core\user\UserRoles;
use DateTime;

class Profile extends AbstractController {
	/**
	 * @Nonce(displayProfile)
	 * @GetAttributes(userId)
	 */
	public function displayAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$profileRepo = $this->em->getRepository( 'UserProfile' );
		$profile = $profileRepo->findByUserId( $userId );
		$userRepo = $this->em->getRepository( 'User' );
		$user = $userRepo->find( $userId );
		
		$profileTemplate = new ProfileTemplate( $this->frontController, $user, $profile );
		
		//		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'user', $user );
		$this->frontController->getResponse()->setVar( 'profileTemplate', $profileTemplate );
		$this->frontController->doDisplay( 'profile', 'displayProfile' );
	}
	
	/**
	 * @Nonce(displayEditProfile)
	 * @GetAttributes(userId)
	 */
	public function displayEditProfileFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$profileRepo = $this->em->getRepository( 'UserProfile' );
		$profile = $profileRepo->findByUserId( $userId );
		
		$form = new ProfileForm( $this->frontController );
		
		$getArgs = array( 'userId' => $userId );
		
		if( null !== $profile ) {
			$form->setupWithProfile( $profile );
			$form->setNonceAction( 'editProfile' );
			$module = 'editProfile';
		} else {
			$form->setNonceAction( 'createProfile' );
			$module = 'createProfile';
		}
		
		$actionUrl = $this->frontController->getViewHelper()->createHref( 'Profile', $module, $getArgs );
		
		// fill the form with the posted field and errors
		$form->setupWithArray( true );
		$form->setTargetUrl( $actionUrl );
		
		$this->frontController->getResponse()->setVar( 'userId', $userId );
		$this->frontController->getResponse()->setVar( 'form', $form );
		$this->frontController->doDisplay( 'profile', 'displayForm' );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(createProfile)
	 * @GetAttributes(userId)
	 */
	public function createProfileAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$form = new ProfileForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$profile = $form->createProfile();
		$profile->setUserId( $userId );
		$profile->setLastModified( new DateTime( '@' . $this->frontController->getRequest()->getRequestTime() ) );
		
		$this->em->persist( $profile );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Profile edition fail, please try again in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'Profile edition success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editProfile)
	 * @GetAttributes(userId)
	 */
	public function editProfileAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$profileRepo = $this->em->getRepository( 'UserProfile' );
		$existingProfile = $profileRepo->findByUserId( $userId );
		if( null === $existingProfile ) {
			$this->frontController->addMessage( __( 'There was an internal error, this is not an edition but a creation' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$form = new ProfileForm( $this->frontController );
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$profile = $form->createProfile( $existingProfile );
		$profile->setLastModified( new DateTime( '@' . $this->frontController->getRequest()->getRequestTime() ) );
		
		$this->em->persist( $profile );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'Profile edition fail, please try again in a moment' ), 'error' );
			$this->frontController->doRedirect( 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'Profile edition success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	}
	
	/**
	 * @Nonce(displayEditEmailForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditEmailFormAction() {

	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editEmail)
	 * @GetAttributes(userId)
	 */
	public function editEmailAction() {

	}
	
	/**
	 * @Nonce(displayEditPasswordForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditPasswordFormAction() {

	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editPassword)
	 * @GetAttributes(userId)
	 */
	public function editPasswordAction() {

	}
	
	/**
	 * @Nonce(displayEditUsernameForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditUsernameFormAction() {

	}
	
	/**
	 * @Method(POST)
	 * @Nonce(editUsername)
	 * @GetAttributes(userId)
	 */
	public function editUsernameAction() {

	}
	
	/**
	 * @Nonce(displayEditAvatarForm)
	 * @GetAttributes(userId)
	 */
	public function displayEditAvatarFormAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		
		$userRepo = $this->em->getRepository( 'User' );
		$user = $userRepo->find( $userId );
		
		$userHelper = new UserHelper( $this->frontController );
		$userHelper->setCurrentUser( $user );
		
		$keys = $links = array();
		//TODO put in configuration file
		$rows = 6;
		$cols = 8;
		$avatarSize = 50;
		$size = $rows * $cols;
		for( $i = 0; $i < $size; $i++ ) {
			$key = Utils::createRandomString( 12, 'numberuppercase' );
			$keys[] = $key;
			$links[] = $userHelper->getGravatar( $key, $avatarSize, 'identicon' );
		}
		$emailLink = $userHelper->getGravatar( $user->getEmail(), $avatarSize, 'identicon' );
		
		$linkWithPlaceholder = $this->frontController->getViewHelper()->createHrefWithNonce( 'editAvatar', 'Profile', 'editAvatar', array( 'userId' => $userId, 'type' => 0, 'avatarKey' => '%avatarKey%' ) );
		
		$referrer = $this->frontController->getRequest()->getRequestedUrl();
		$referrerField =  '<input type="hidden" name="' . Config::$instance->REFERRER_FIELD_NAME . '" value="' . $referrer . '">';
		
		$response = $this->frontController->getResponse();
		$response->setVar( 'userId', $userId );
		$response->setVar( 'user', $user );
		$response->setVar( 'emailLink', $emailLink );
		$response->setVar( 'keys', $keys );
		$response->setVar( 'links', $links );
		$response->setVar( 'userHelper', $userHelper );
		$response->setVar( 'cols', $cols );
		$response->setVar( 'rows', $rows );
		$response->setVar( 'size', $avatarSize );
		$response->setVar( 'linkWithPlaceholder', $linkWithPlaceholder );
		$response->setVar( 'referrerField', $referrerField );
		
		$this->frontController->doDisplay( 'profile', 'chooseAvatar' );
	}
	
	/**
	 * @Nonce(editAvatar)
	 * @GetAttributes({userId, avatarKey, type})
	 */
	public function editAvatarAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		$avatarKey = $get->get( 'avatarKey' );
		$type = $get->get( 'type' );
		
		$userRepo = $this->em->getRepository( 'User' );
		$user = $userRepo->find( $userId );
		
		if( !$user ) {
			$this->frontController->addMessage( __( 'The user does not exist', 'error' ) );
			// we can't redirect to the profile, no correct user id
			$this->frontController->doRedirect( 'Home' );
		}
		
		$user->setAvatarType( $type );
		$user->setAvatarKey( $avatarKey );
		$this->em->persist( $user );
		if( !$this->em->flushSafe() ) {
			$this->frontController->addMessage( __( 'An error occurred during the modification' ), 'error' );
			$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
		}
		
		$this->frontController->addMessage( __( 'The avatar modification was a success' ), 'correct' );
		$this->frontController->doRedirectWithNonce( 'displayProfile', 'Profile', 'display', array( 'userId' => $userId ) );
	
	}
	
	// receive an image, propose to the user to crop it and send it to saveImage
	/**
	 * @Nonce(displayCropAvatar)
	 * @GetAttributes({userId})
	 * @FilePresent
	 */
	public function displayCropImageAction() {
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );

		$viewHelper = $this->frontController->getViewHelper();
		$viewHelper->addJavascriptFile('jquery.js');
		$viewHelper->addJavascriptFile('jcrop.js');
		$viewHelper->addJavascriptFile('crop_interaction.js');
		$viewHelper->addJavascriptVar('avatarImageMaxWidth', Config::$instance->AVATAR_ORIGINAL_MAX_WIDTH);
		$viewHelper->addJavascriptVar('avatarImageMaxHeight', Config::$instance->AVATAR_ORIGINAL_MAX_HEIGHT);
		$viewHelper->addJavascriptVar('avatarCropMaxWidth', Config::$instance->AVATAR_CROP_MAX_WIDTH);
		$viewHelper->addJavascriptVar('avatarCropMaxHeight', Config::$instance->AVATAR_CROP_MAX_HEIGHT);
		$viewHelper->addJavascriptVar('avatarCropMinWidth', Config::$instance->AVATAR_CROP_MIN_WIDTH);
		$viewHelper->addJavascriptVar('avatarCropMinHeight', Config::$instance->AVATAR_CROP_MIN_HEIGHT);
		$viewHelper->addCssFile('profile_avatar_crop.css');
		
		$filename = $this->saveImage($_FILES['avatar_file']);
		$imageLink = Config::$instance->TEMP_DIR . $filename;
		$tempKey = $filename;
		$imageSrcLink = $imageLink;
		
		$http = Utils::isSSL() ? 'https://' : 'http://';
		$imageLink = $http . $_SERVER['HTTP_HOST'] . '/' . Config::$instance->SITE_NAME . '/' . $imageLink;
		
		$actionLink = $viewHelper->createHrefWithNonce('cropAvatar', 'Profile', 'cropImage', array('userId' => $userId, 'key' => $tempKey));
		
		$referrer = $this->frontController->getRequest()->getRequestedUrl();
		
		$response = $this->frontController->getResponse();
		$response->setVar('actionLink', $actionLink);
		$response->setVar('imageLink', $imageLink);
		$response->setVar('imageSrcLink', $imageSrcLink);
		$response->setVar('referrer', $referrer);
		$response->setVar('userId', $userId);
		$this->frontController->doDisplay('profile', 'displayCropImage', array('avatarKey' => $tempKey, 'userId' => $userId));
	}
	// part to display the cropping image
	
	/**
	 *  receive cropped image, save it and call editAvatarAction
	 * @return string|false filename to the file or false if an error occurred
	 */
	private function saveImage($file){
		if(!$this->verifyFile($file)){
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl($referrer);
			return false;
		}
		$filename = $this->createRandomFilename(Config::$instance->TEMP_DIR, 6);
		if(false === $filename){
			$referrer = $this->frontController->getRequest()->getReferrer();
			$this->frontController->doRedirectUrl($referrer);
			return false;
		}
		
		// without extension
		$destination = Config::$instance->TEMP_DIR . $filename ;
		$destWithExt = $this->createThumbIfNecessary($file['tmp_name'], $destination );
		if(false === $destWithExt ){
			$destWithExt = $this->writeToFilename($file, $destination );
			if( false === $destWithExt || is_array($destWithExt)){
				if( is_array( $destWithExt ) ){
					Logger::getInstance()->log_var('Error during writeToFilename', $destWithExt);
				}
				$this->frontController->addMessage(__('The uploaded file is not valid' ), 'error');
				$referrer = $this->frontController->getRequest()->getReferrer();
				$this->frontController->doRedirectUrl($referrer);
				return false;	
			}
		}
		$basename = pathinfo($destWithExt, PATHINFO_BASENAME);
		return $basename;
	}
	
	private function verifyFile($file){
		$uploadErrors = array(
			0 => __( 'There is no error, the file uploaded with success' ),
			// max size is from php.ini
			1 => __( 'Your image was bigger than the maximum allowed file size of: %size%' , array('%size%' => Utils::getNiceSize(Config::$instance->MAX_AVATAR_FILE_SIZE) ) ),
			// max size is from html form
			2 => __( 'Your image was bigger than the maximum allowed file size of: %size%' , array('%size%' => Utils::getNiceSize(Config::$instance->MAX_AVATAR_FILE_SIZE) ) ),
			3 => __( 'The uploaded file was only partially uploaded' ),
			4 => __( 'No file was uploaded' ),
			6 => __( 'Missing a temporary folder' ),
			7 => __( 'Failed to write file to disk.' ),
			8 => __( 'File upload stopped by extension.' )
		);
		
		if ( $file['error'] ){
			$this->frontController->addMessage( __( 'Your upload failed, please try again. Error was: %error%' , array('%error%' => $uploadErrors[$file['error']]) ) , 'error' );
			return false;
		}
		if ( $file['size'] > Config::$instance->MAX_AVATAR_FILE_SIZE ){
			$this->frontController->addMessage( __( 'The file you uploaded is too big. Please upload a file under %size%', array('%size%' => Utils::getNiceSize(Config::$instance->MAX_AVATAR_FILE_SIZE) ) ), 'error' );
			return false;
		}
		if ( $file['size'] <= Config::$instance->MIN_AVATAR_FILE_SIZE ){
			$this->frontController->addMessage( __( 'The file you uploaded is too small. Please upload a file bigger than %size%', array('%size%' => Utils::getNiceSize(Config::$instance->MIN_AVATAR_FILE_SIZE) ) ), 'error' );
			return false;
		}
		if( !@is_uploaded_file( $file['tmp_name'] ) ){
			$this->frontController->addMessage( __( 'Specified file failed upload test.' ), 'error' );
			return false;
		}
		if ( ( !empty( $file['type'] ) &&
				!preg_match( '/(jpe?g|gif|png|pjpe?g)$/', strtolower($file['type'] ) ) ) ||
				!preg_match( '/(jpe?g|gif|png|pjpe?g)$/', strtolower($file['name'] ) ) ){
			$this->frontController->addMessage( __( 'Please upload only JPG, GIF or PNG photos.' ), 'error' );
			return false;
		}
		return true;

	}
	
	/** @return string representing a filename that is not used in folder */
	private function createRandomFilename($folder, $length){
		if( !file_exists( $folder ) ) {
			Logger::getInstance()->log( 'Creation of the temp path: ' . $folder );
			mkdir( $folder, '0777', true );
		}
		
		$num = 0;
		do{
			$name = Utils::createRandomString($length, 'alphanumeric');
			$num++;
			if($num > 100){
				Logger::getInstance()->log('Number of attempt to create random filename is reached...');
				return false;
			}
		}while(file_exists($folder . $name));
		
		return $name;
	}
	
	private function writeToFilename($file, $destination){
		$size = @getimagesize( $file['tmp_name'] );
		if ( !$size ){
			return array('invalid_image', 'Could not read image size', $file['tmp_name']);
		}
		list(,, $orig_type) = $size;
		
		if ( IMAGETYPE_GIF == $orig_type ) {
			$destfilename = "{$destination}.gif";
		} elseif ( IMAGETYPE_PNG == $orig_type ) {
			$destfilename = "{$destination}.png";
		} else {
			// all other formats are converted to jpg
			$destfilename = "{$destination}.jpg";
		}
		
		// Move the file to the uploads dir
		if ( false === @move_uploaded_file( $file['tmp_name'], $destfilename ) ){
			return array('problem during move_uploader_file');
//			return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );
		}
	
		// Set correct file permissions
		$stat = stat( dirname( $destfilename ));
		$perms = $stat['mode'] & 0000666;
		@chmod( $destfilename, $perms );
	
		return $destfilename;
		
	}
	
	private function createThumbIfNecessary( $filepath, $destination ) {
//		//	array( 'file' => $new_file, 'url' => $url, 'type' => $type )
//		$bp->avatar_admin->original = $this->wp_handle_upload( $file['file'] );
	
		/* Get image size */
		$size = @getimagesize( $filepath );
	
		/* Check image size and shrink if too large */
		if ( $size[0] > Config::$instance->AVATAR_ORIGINAL_MAX_WIDTH ) {
//			$thumb = wp_create_thumbnail( $bp->avatar_admin->original['file'], BP_AVATAR_ORIGINAL_MAX_WIDTH );
			$thumb = $this->createResizedImage( $filepath, Config::$instance->AVATAR_ORIGINAL_MAX_WIDTH, Config::$instance->AVATAR_ORIGINAL_MAX_WIDTH, $destination );
			
			/* Check for thumbnail creation errors */
			if(is_array($thumb)){
				$this->frontController->addMessage(__('An error occurred during creation of thumbnail of the uploaded image'), 'error');
				Logger::getInstance()->log_var('Error in thumbnail creation', $thumb);

				$referrer = $this->frontController->getRequest()->getReferrer();
				$this->frontController->doRedirectUrl($referrer);
				return false;
			}
			
			@unlink($filepath);
			return $thumb;
		}
		return false;
	}
	
	/**
	 * Scale down an image to fit a particular size and save a new copy of the image.
	 *
	 * The PNG transparency will be preserved using the function, as well as the
	 * image type. If the file going in is PNG, then the resized image is going to
	 * be PNG. The only supported image types are PNG, GIF, and JPEG.
	 *
	 * Some functionality requires API to exist, so some PHP version may lose out
	 * support. This is not the fault of WordPress (where functionality is
	 * downgraded, not actual defects), but of your PHP version.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Image file path.
	 * @param int $max_w Maximum width to resize to.
	 * @param int $max_h Maximum height to resize to.
	 * @param bool $crop Optional. Whether to crop image or resize.
	 * @param string $suffix Optional. File Suffix.
	 * @param string $dest_path Optional. New image file path.
	 * @param int $jpeg_quality Optional, default is 90. Image quality percentage.
	 * @return mixed WP_Error on failure. String with new destination path.
	 */
	private function createResizedImage( $filepath, $max_w, $max_h, $destination, $crop = false, $jpeg_quality = 90 ) {
		$image = $this->loadImageFromFile( $filepath );
		if ( !is_resource( $image ) ){
			// in this case the $image is the error array returned
			return array('error_loading_image', $image, $filepath );
		}
		$size = @getimagesize( $filepath );
		if ( !$size ){
			return array('invalid_image', 'Could not read image size', $filepath);
		}
		list($orig_w, $orig_h, $orig_type) = $size;
	
		$dims = $this->resizeDimension($orig_w, $orig_h, $max_w, $max_h, $crop);
		if ( !$dims ){
			return array( 'error_getting_dimensions', __('Could not calculate resized image dimensions') );
		}
		list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;
	
		$newimage = $this->createTrueColorImage( $dst_w, $dst_h );
	
		imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	
		// convert from full colors to index colors, like original PNG.
		if ( IMAGETYPE_PNG == $orig_type && function_exists('imageistruecolor') && !imageistruecolor( $image ) ){
			imagetruecolortopalette( $newimage, false, imagecolorstotal( $image ) );
		}
		// we don't need the original in memory anymore
		imagedestroy( $image );
	
		if ( IMAGETYPE_GIF == $orig_type ) {
			$destfilename = "{$destination}.gif";
			if ( !imagegif( $newimage, $destfilename ) )
				return array('resize_path_invalid', __( 'Resize path invalid' ));
		} elseif ( IMAGETYPE_PNG == $orig_type ) {
			$destfilename = "{$destination}.png";
			if ( !imagepng( $newimage, $destfilename ) )
				return array('resize_path_invalid', __( 'Resize path invalid' ));
		} else {
			// all other formats are converted to jpg
			$destfilename = "{$destination}.jpg";
			if ( !imagejpeg( $newimage, $destfilename, $jpeg_quality ) ){
				return array('resize_path_invalid', __( 'Resize path invalid' ));
			}
		}
	
		imagedestroy( $newimage );
	
		// Set correct file permissions
		$stat = stat( dirname( $destfilename ));
		//same permissions as parent folder, strip off the executable bits
		$perms = $stat['mode'] & 0000666; 
		@chmod( $destfilename, $perms );
		
		// to restore previous value
		@ini_restore('memory_limit');
		
		return $destfilename;
	}
	
	/**
	 * Load an image from a string, if PHP supports it.
	 *
	 * @param string $file Filename of the image to load.
	 * @return resource The resulting image resource on success, Error array on failure.
	 */
	private function loadImageFromFile( $file ) {
		if ( ! file_exists( $file ) )
			return array('File does not exist?', $file);
	
		if ( ! function_exists('imagecreatefromstring') )
			return array('The GD image library is not installed.');
	
		// Set artificially high because GD uses uncompressed images in memory
		@ini_set('memory_limit', -1);
		$image = imagecreatefromstring( file_get_contents( $file ) );
	
		if ( !is_resource( $image ) )
			return array('File &#8220;%s&#8221; is not an image.', $file);
	
		return $image;
	}
	
	/**
	 * Create new GD image resource with transparency support
	 *
	 * @param int $width Image width
	 * @param int $height Image height
	 * @return image resource
	 */
	private function createTrueColorImage($width, $height) {
		$img = imagecreatetruecolor($width, $height);
		if ( is_resource($img) && function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
			imagealphablending($img, false);
			imagesavealpha($img, true);
		}
		return $img;
	}
	
	/**
	 * Retrieve calculated resized dimensions for use in imagecopyresampled().
	 *
	 * Calculate dimensions and coordinates for a resized image that fits within a
	 * specified width and height. If $crop is true, the largest matching central
	 * portion of the image will be cropped out and resized to the required size.
	 *
	 * @param int $orig_w Original width.
	 * @param int $orig_h Original height.
	 * @param int $dest_w New width.
	 * @param int $dest_h New height.
	 * @param bool $crop Optional, default is false. Whether to crop image or resize.
	 * @return bool|array False, on failure. Returned array matches parameters for imagecopyresampled() PHP function.
	 */
	private function resizeDimension($orig_w, $orig_h, $dest_w, $dest_h, $crop = false) {
		if ($orig_w <= 0 || $orig_h <= 0)
			return false;
		// at least one of dest_w or dest_h must be specific
		if ($dest_w <= 0 && $dest_h <= 0)
			return false;
	
		if ( $crop ) {
			// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
			$aspect_ratio = $orig_w / $orig_h;
			$new_w = min($dest_w, $orig_w);
			$new_h = min($dest_h, $orig_h);
	
			if ( !$new_w ) {
				$new_w = intval($new_h * $aspect_ratio);
			}
	
			if ( !$new_h ) {
				$new_h = intval($new_w / $aspect_ratio);
			}
	
			$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);
	
			$crop_w = round($new_w / $size_ratio);
			$crop_h = round($new_h / $size_ratio);
	
			$s_x = floor( ($orig_w - $crop_w) / 2 );
			$s_y = floor( ($orig_h - $crop_h) / 2 );
		} else {
			// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
			$crop_w = $orig_w;
			$crop_h = $orig_h;
	
			$s_x = 0;
			$s_y = 0;
	
			list( $new_w, $new_h ) = $this->constrainDimensions( $orig_w, $orig_h, $dest_w, $dest_h );
		}
	
		// if the resulting image would be the same size or larger we don't want to resize it
		if ( $new_w >= $orig_w && $new_h >= $orig_h )
			return false;
	
		// the return array matches the parameters to imagecopyresampled()
		// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
		return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	
	}
	
	/**
	 * Calculates the new dimentions for a downsampled image.
	 *
	 * If either width or height are empty, no constraint is applied on
	 * that dimension.
	 *
	 * @param int $current_width Current width of the image.
	 * @param int $current_height Current height of the image.
	 * @param int $max_width Optional. Maximum wanted width.
	 * @param int $max_height Optional. Maximum wanted height.
	 * @return array First item is the width, the second item is the height.
	 */
	private function constrainDimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
		if ( !$max_width and !$max_height )
			return array( $current_width, $current_height );
	
		$width_ratio = $height_ratio = 1.0;
		$did_width = $did_height = false;
	
		if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width ) {
			$width_ratio = $max_width / $current_width;
			$did_width = true;
		}
	
		if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height ) {
			$height_ratio = $max_height / $current_height;
			$did_height = true;
		}
	
		// Calculate the larger/smaller ratios
		$smaller_ratio = min( $width_ratio, $height_ratio );
		$larger_ratio  = max( $width_ratio, $height_ratio );
	
		if ( intval( $current_width * $larger_ratio ) > $max_width || intval( $current_height * $larger_ratio ) > $max_height )
	 		// The larger ratio is too big. It would result in an overflow.
			$ratio = $smaller_ratio;
		else
			// The larger ratio fits, and is likely to be a more "snug" fit.
			$ratio = $larger_ratio;
	
		$w = intval( $current_width  * $ratio );
		$h = intval( $current_height * $ratio );
	
		// Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
		// We also have issues with recursive calls resulting in an ever-changing result. Contraining to the result of a constraint should yield the original result.
		// Thus we look for dimensions that are one pixel shy of the max value and bump them up
		if ( $did_width && $w == $max_width - 1 )
			$w = $max_width; // Round it up
		if ( $did_height && $h == $max_height - 1 )
			$h = $max_height; // Round it up
	
		return array( $w, $h );
	}

	/**
	 * @Nonce(cropAvatar)
	 * @GetAttributes({userId, key})
	 * @PostAttributes({x, y, w, h})
	 */
	public function cropImageAction(){
		$get = $this->frontController->getRequest()->query;
		$userId = $get->get( 'userId' );
		$tempKey = $get->get( 'key' );
		
		$temporaryFile = Config::$instance->TEMP_DIR . $tempKey;
		$randomName = $this->createRandomFilename(Config::$instance->AVATAR_DIR, 12);
		$destinationFile = Config::$instance->AVATAR_DIR . $randomName . '.jpg';
		
		
		
 // Code from jCrop example
		$targ_w = $targ_h = 150;
		$jpeg_quality = 90;
	
		$src = $temporaryFile;
		$ext = pathinfo($src, PATHINFO_EXTENSION);
		switch($ext){
			case 'png':
				$img_r = imagecreatefrompng($src);
				break;
			case 'jpg': case 'jpeg':
				$img_r = imagecreatefromjpeg($src);
				break;
			case 'gif':
				$img_r = imagecreatefromgif($src);
				break;
			default:
				Logger::getInstance()->log_var('Bad extension name', $src);
				$result = false;
				break;
				
		}
		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

		if($_POST['w'] < Config::$instance->AVATAR_CROP_MIN_WIDTH || $_POST['h'] < Config::$instance->AVATAR_CROP_MIN_HEIGHT){
			$this->frontController->addMessage(__('The selection was too small'), 'error');
			$this->frontController->doRedirectWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$userId) );
		}
		if($_POST['w'] > Config::$instance->AVATAR_CROP_MAX_WIDTH || $_POST['h'] > Config::$instance->AVATAR_CROP_MAX_HEIGHT){
			$this->frontController->addMessage(__('The selection was too big'), 'error');
			$this->frontController->doRedirectWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$userId) );
		}
		
		$result = imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);
		
		if($result){
			$result = imagejpeg($dst_r, $destinationFile ,$jpeg_quality);
		}

		if($result){
			$userRepo = $this->em->getRepository('User');
			$user = $userRepo->find($userId);
			//TODO remove previous one
			if(1 == $user->getAvatarType()){
				$fileToDelete = $user->getAvatarKey();
				$pathToDelete = Config::$instance->AVATAR_DIR . $fileToDelete . '.jpg';
				if(file_exists($pathToDelete)){
					@unlink( $pathToDelete );
				}
			}
			$user->setAvatarKey($randomName);
			$user->setAvatarType(1);
			$this->em->persist($user);
			$result = $this->em->flushSafe();
		}
		
		if(!$result){
			$this->frontController->addMessage(__('An error occurred during the crop of the image'), 'error');
			$this->frontController->doRedirectWithNonce('displayEditProfile', 'Profile', 'displayEditProfileForm', array('userId'=>$userId) );
		}
		$this->frontController->addMessage(__('The modification of your avatar was a success'), 'correct');
		$this->frontController->doRedirectWithNonce('displayProfile', 'Profile', 'display', array('userId'=>$userId) );
	}
}

