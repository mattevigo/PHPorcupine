<?php
/**
 * Contains function for the framework management, like imports ecc...
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 * @package PHPorcupine
 * @version 1.0
 */
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";

/**
 * Load the option of the panel
 * 
 * @var unknown_type
 */
define( "DEFAULT_OPTION", "na" );

function load_option( $option=NULL )
{
	if ( $option == NULL) $option = DEFAULT_OPTION;
	
	$option_path = LOCAL_ROOT . "/options/" . $option;

	if(file_exists($option_path))
		$option_path = $option_path . "/option.php";
	else
		$option_path = $option_path . ".php";

	//echo $option_path;
	if(!file_exists($option_path))
		throw new FrameworkException("$option_path: view doesn't exists");

	require_once $option_path;
}

function load_js( $option )
{
	$filename = "ppanel.$option.js";
	
	$path = LOCAL_ROOT.DS."js".DS.$filename;
	$url = LOCAL_WEB_ROOT."/js/".$filename;
	
	if( file_exists($path) )
		echo "<script type=\"text/javascript\" src=\"$url\"></script>";
}

function load_css( $option )
{
	$filename = "style.$option.css";
	
	$path = LOCAL_ROOT.DS."css".DS.$filename;
	$url = LOCAL_WEB_ROOT."/css/".$filename;
	
	if( file_exists($path) )
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$url\" />";
}
?>