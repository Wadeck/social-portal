<?php

namespace core\tools;

use core\FrontController;

use core\debug\Logger;

use core\Config;

class ImageUtils{
	private static $instance = null;
	/** @var FrontController */
	private $frontController;
	
	private function __construct(FrontController $frontController){
		$this->frontController = $frontController;
	}
	/**
	 *  receive cropped image, save it and call editAvatarAction
	 * @return string|false filename to the file or false if an error occurred
	 */
	public static function saveImage(FrontController $frontController, $file){
		if(null === self::$instance){
			self::$instance = new ImageUtils($frontController);
		}
		return self::$instance->_saveImage($file);
	}
	
	private function _saveImage($file){
		if(!$this->verifyFile($file)){
			$this->frontController->doRedirectToReferrer();
		}
		
		$tempDir = Config::getOrDie('temp_dir');
		$filename = $this->createRandomFilename($tempDir, 6);
		if(false === $filename){
			$this->frontController->doRedirectToReferrer();
		}
		
		// without extension
		$destination = $tempDir . $filename ;
		$destWithExt = $this->createThumbIfNecessary($file['tmp_name'], $destination );
		if(false === $destWithExt ){
			$destWithExt = $this->writeToFilename($file, $destination );
			if( false === $destWithExt || is_array($destWithExt)){
				if( is_array( $destWithExt ) ){
					Logger::getInstance()->log_var('Error during writeToFilename', $destWithExt);
				}
				$this->frontController->doRedirectToReferrer(__('The uploaded file is not valid' ), 'error');
				return false;	
			}
		}
		$basename = pathinfo($destWithExt, PATHINFO_BASENAME);
		return $basename;
	}
	
	private function verifyFile($file){
		$maxAvatarFileSize = Config::get('max_avatar_file_size', 2560000);
		$minAvatarFileSize = Config::get('min_avatar_file_size', 10);
		$uploadErrors = array(
			0 => __( 'There is no error, the file uploaded with success' ),
			// max size is from php.ini
			1 => __( 'Your image was bigger than the maximum allowed file size of: %size%' , array('%size%' => Utils::getNiceSize( $maxAvatarFileSize ) ) ),
			// max size is from html form
			2 => __( 'Your image was bigger than the maximum allowed file size of: %size%' , array('%size%' => Utils::getNiceSize( $maxAvatarFileSize ) ) ),
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
		if ( $file['size'] > $maxAvatarFileSize ){
			$this->frontController->addMessage( __( 'The file you uploaded is too big. Please upload a file under %size%', array('%size%' => Utils::getNiceSize( $maxAvatarFileSize ) ) ), 'error' );
			return false;
		}
		if ( $file['size'] <= $minAvatarFileSize ){
			$this->frontController->addMessage( __( 'The file you uploaded is too small. Please upload a file bigger than %size%', array('%size%' => Utils::getNiceSize( $minAvatarFileSize ) ) ), 'error' );
			return false;
		}
		if( !@is_uploaded_file( $file['tmp_name'] ) ){
			$this->frontController->addMessage( __( 'Specified file failed upload test.' ), 'error' );
			return false;
		}
		
		$size = @getimagesize( $file['tmp_name'] );
		if ( $size ){
			$realType = $size['mime'];
			if(preg_match( '/(jpe?g|gif|png|pjpe?g)$/', strtolower( $realType ) ) ){
				return true;
			}
			if(preg_match( '/(bmp)$/', strtolower( $realType ) ) ){
				$this->frontController->addMessage( __( 'Your image is in BMP format (even if the extension was something else), we do not support BMP'), 'error' );
			}else{
				$this->frontController->addMessage( __( 'Please upload only JPG, GIF or PNG photos.' ), 'error' );
			}
		}else{
			$this->frontController->addMessage( __( 'Please upload an image ! (If the file you uploaded was an image, it was not recognized as such)' ), 'error' );
			$realType = 'NotAnImage';
		}
		
		Logger::getInstance()->log("The file uploaded was type=[{$file['type']}] and name=[{$file['name']}] and sectype=[$realType]");
		return false;
		
	}
	
	/** @return string representing a filename that is not used in folder */
	public static function createRandomFilename($folder, $length){
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
			return array('problem during move_uploader_file', $file['tmp_name'], $destfilename);
		}
	
		// Set correct file permissions
		$stat = stat( dirname( $destfilename ));
		$perms = $stat['mode'] & 0000666;
		@chmod( $destfilename, $perms );
	
		return $destfilename;
		
	}
	
	private function createThumbIfNecessary( $filepath, $destination ) {
		/* Get image size */
		$size = @getimagesize( $filepath );
	
		$maxWidth = Config::get('avatar_original_max_width', 650);
		$maxHeight = Config::get('avatar_original_max_height', 650);
		
		/* Check image size and shrink if too large */
		if ( $size[0] > $maxWidth || $size[1] > $maxHeight ) {
			$thumb = $this->createResizedImage( $filepath, $maxWidth, $maxHeight, $destination );
			
			/* Check for thumbnail creation errors */
			if(is_array($thumb)){
				Logger::getInstance()->log_var('Error in thumbnail creation', $thumb);
				$this->frontController->doRedirectToReferrer(__('An error occurred during creation of thumbnail of the uploaded image'), 'error');
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
			if ( !imagegif( $newimage, $destfilename ) ){
				return array('resize_path_invalid', 'gif',  __( 'Resize path invalid' ));
			}
		} elseif ( IMAGETYPE_PNG == $orig_type ) {
			$destfilename = "{$destination}.png";
			if ( !imagepng( $newimage, $destfilename ) ){
				return array('resize_path_invalid', 'png', __( 'Resize path invalid' ));
			}
		} else {
			// all other formats are converted to jpg
			$destfilename = "{$destination}.jpg";
			if ( !imagejpeg( $newimage, $destfilename, $jpeg_quality ) ){
				return array('resize_path_invalid', 'jpg',__( 'Resize path invalid' ));
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
			return array('File is not an image.', $file);
	
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

}