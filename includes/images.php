<?php
/**
 * Funzioni per la gestione degli uploads
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 * @package pages.templates.admin.includes.uploads
 * @version 1.0
 */
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";

/* images */
define("IMAGE_DEFAULT_WIDTH", 300);
define("IMAGE_DEFAULT_QUALITY", 8);

if (!defined("IMAGES_DIR") ) define("IMAGES_DIR", $_SERVER['DOCUMENT_ROOT']."/images");
if ( !defined("TEMP_DIR") ) define("TEMP_DIR", $_SERVER['DOCUMENT_ROOT']."/tmp");

$allowed_types = array(		# contiene i mime type delle immagini ammesse
	"image/jpeg",
	"image/gif",
	"image/png"
);

/**
 * Definition of the ImageException for the handling of errors
 *
 */
class ImageException extends Exception
{
	function __construct($message){
		parent::__construct($message);
	}
}

/**
 * Controlla le caratteristiche della foto da uplodare
 *
 * @param $uploaded_file
 * @param $allowed_types
 *
 * @return TRUE se la foto e stata accettata
 * 			FALSE altrimenti
 */
function accept($uploaded_file, $allowed_types)
{	
	if(!in_array($uploaded_file['type'], $allowed_types))
		return false;
	//TODO controllo se la foto e' troppo piccola
	return true;
}

/**
 * Ridimensiona la foto 
 *
 * @todo controllo grandezza
 * @todo gestione eccezione
 *
 * @param $file_path indirizzo della foto
 * 
 * @throws ImageException se non e' andata a buon fine
 * @return
 */
function upload_image_resize($file_path, $new_width=IMAGE_DEFAULT_WIDTH, $dir=IMAGES_DIR)
{
	//recupero le informazioni dell'immagine uploadata
	$info_image = getimagesize($file_path);
	$height = $info_image[1];
	$width = $info_image[0];
	$area =  $height/$width;
	
	// calcolo in proprozione la width
	$new_height = (int) ($area * $new_width);
	
	// resample
	$new_image = imagecreatetruecolor($new_width, $new_height); #creo la nuova immagine vuota
	$image = null;
	
	switch ($info_image['mime'])
	{
		case "image/jpeg":
			$image = imagecreatefromjpeg($file_path);
			break;
			
		case "image/gif":
			$image = imagecreatefromgif($file_path);
			break;
			
		case "image/png":
			$image = imagecreatefrompng($file_path);
			break;
	}	
	
	if(!imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height))
	{
		throw new ImageException("Error processing image (resize)");
	}
	
	$image_filename = $dir."/TEMP.PNG";
	if(imagepng($new_image, $image_filename, IMAGE_DEFAULT_QUALITY))
	{
		return $image_filename;
	}
	else
	{
		throw new ImageException("Error processing image (create PNG)");
	}
}

/**
 * Crea un thumbnail per la foto
 *
 * @require la foto deve essere un png
 * @todo controllo grandezza
 * @todo gestione eccezione
 *
 * @param $file_path indirizzo della foto
 *
 * @return
 */
function thumbnail($file_path, $new_height=THUMB_DEFAULT_HEIGHT)
{
	
	$info_image = getimagesize($file_path);
	$height = $info_image[1];
	$width = $info_image[0];
	$area = $width/$height;
	
	// calcolo in proprozione la width
	$new_width = (int) ($area * $new_height);
	
	// resample
	$new_image = imagecreatetruecolor($new_width, $new_height); #creo la nuova immagine vuota
	$image = imagecreatefrompng($file_path);
	imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	return imagepng($new_image, PHOTO_DIR."THUMB.PNG");
}
