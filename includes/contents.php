<?php
/**
 * Funzioni per la gestione dei contenuti
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 * @package pages.templates.admin.includes.contents
 * @version 1.0
 */
require_once 'includes/session.php';

import("core.DBEntity");

/**
 * Inserisce il div contenente il messaggio, warning o error registrato nelle
 * relative variabili di sessione
 * 
 */
//function load_advise()
//{
//	//var_dump($_SESSION);
//	$msg = "no message";
//	if(isset($_SESSION['message']) && $_SESSION['message'] != null) echo "<div class=\"message\"><b>".get_message()."</b></div>";
//	if(isset($_SESSION['warning']) && $_SESSION['warning'] != null) echo "<div class=\"warning\"><b>".get_warning()."</b></div>";
//	if(isset($_SESSION['error']) && $_SESSION['error'] != null) echo "<div class=\"error\"><b>".get_error()."</b></div>";
//}

/**
 * 
 * @param $name the HTML name of that select
 * @param $id the HTML id of that select
 * @param $entities an array of Entities (options)
 * @param $option_name 
 * @return unknown_type
 */
function get_select( $name, $id, $entities, $option_name, $default_text=NULL, $selected_id=NULL )
{
	$opts = get_options(&$entities, $option_name, $default_text, $selected_id);
	
	$select = <<<EOD
	<select name="$name" id="$id">
		$opts
	</select>
EOD;

	return $select;
}

/**
 * 
 * @param $entities a list of Entities
 * @param $option_name the field name that contain the name to display
 * @param $default (optional) the default messag (id=0)
 * @return unknown_type
 */
function get_options ( $entities, $option_name="", $default=NULL, $selected_id=NULL )
{
	$opts = "";
	$selected = "selected=\"selected\"";
	
	if( $default != NULL )
		$opts = "<option value=\"0\">$default</option>";
		
	foreach( $entities as $key => $obj )
	{
		$selected_attr = "";
		if( $selected_id != NULL && $selected_id == $obj->getId() )
			$selected_attr = $selected;
			
		try
		{
			$opts .= "<option $selected_attr value=\"{$obj->getId()}\">{$obj->get($option_name)}</option>";
		}
		catch (Exception $e ) 
		{
			echo $e->getMessage();
		}
	}
	
	return $opts;
}

/**
 * Get the html object for target youtube video
 * 
 * @param $height
 * @param $width
 * @param $youtube_link
 * 
 * @return unknown_type
 */
function youtube_object ( $height, $width, $youtube_link )
{
	$youtube_id = Video::parseLink( $youtube_link );
	$obj = "<object width=\"$width\" height=\"$height\">
  			<param name=\"movie\" value=\"http://www.youtube.com/v/$youtube_id?rel=1&color1=0x2b405b&color2=0x6b8ab6&border=1&fs=1\"></param>
  			<param name=\"allowFullScreen\" value=\"true\"></param>
  			<embed src=\"http://www.youtube.com/v/$youtube_id?rel=1\"
    				type=\"application/x-shockwave-flash\"
    				width=\"$width\" height=\"$height\" 
    				allowfullscreen=\"true\">
    		</embed>
		</object>";
	
	return $obj;
}
?>