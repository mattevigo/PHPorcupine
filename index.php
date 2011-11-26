<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package DBEntity
 *
 * @version 1.0 - last update 08 nov 2009 - 16:00
 *
 * Public side Entry Point
 */
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/includes/framework.php";

define("_ENTRY_", 1);

import("includes.session");
import("includes.debug"); // only if required

session_start();

$page = null;
$script = null;
if( isset( $_GET['page']) ) // precedence to pages
{
	$page = $_GET['page'];
	//echo $page . "<br />";
	// operation of page calling
	try
	{
		page($page);
	}
	catch (Exception $e)
	{
		$trace = nl2br($e->getTraceAsString());
		//header("HTTP/1.0 404 Not Found");
		echo $e->getMessage() . "<br />";
		echo "Error at line {$e->getLine()} in {$e->getFile()}<br /><br />";
		echo "Trace:<br />$trace";
	}
}
else if( isset( $_GET['script']) )
{
	$script = $_GET['script'];
	//echo $script . "<br />";
	// operation of script calling (redirect?)
	try
	{
		$content = "application/x-javascript";
		if(isset($_GET['content']) && $_GET['content'] == "html")
			$content = "text/html";
		if(isset($_GET['content']) && $_GET['content'] == "atom")
			$content = "application/atom+xml ";
		if(isset( $_GET['content']) && $_GET['content'] == "jsonp")
			$content = "application/json;charset=UTF-8";
			
		script($script, $content);
	}
	catch (Exception $e)
	{
		$trace = nl2br($e->getTraceAsString());
		//header("HTTP/1.0 404 Not Found");
		echo $e->getMessage() . "<br />";
		echo "Error at line {$e->getLine()} in {$e->getFile()}<br /><br />";
		echo "Trace:<br />$trace";
	}
}
else
{
	try
	{
		page('home');
	}
	catch (Exception $e)
	{
		$trace = nl2br($e->getTraceAsString());
		//header("HTTP/1.0 404 Not Found");
		echo $e->getMessage() . "<br />";
		echo "Error at line {$e->getLine()} in {$e->getFile()}<br /><br />";
		echo "Trace:<br />$trace";
	}
}
?>