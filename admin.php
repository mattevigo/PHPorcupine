<?php 
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package DBEntity
 *
 * @version 1.0 - last update 08 nov 2009 - 16:00
 *
 * Admin side Entry Point
 */
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/includes/framework.php";

define("_ENTRY_", 1);
define("_ADMIN_", 1);

import("includes.session");
import("includes.debug"); // only if required

session_start();

$db = getDB();

/** Check if the current user is logged */
try{
if(!user_is_logged($db))
{
	header("Location:login.php?from=admin.php");
	//echo "Sessione non loggata";
	exit();
}
}
catch (Exception $e)
{
	echo $e->getMessage();
}

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
		header("HTTP/1.0 404 Not Found");
		//echo $e->getMessage();
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
		script($script, $content);
	}
	catch (Exception $e)
	{
		//header("HTTP/1.0 404 Not Found");
		echo $e->getTraceAsString();
		echo $e->getMessage();
	}
}
else
{
try
	{
		//echo "Calling panel<br />";
		page('panel');
	}
	catch (Exception $e)
	{
		//header("HTTP/1.0 404 Not Found");
		echo $e->getMessage();
	}
}

$user = get_session_user();	// User core-object for the current user

// variabili da utilizzare per la visualizzazione delle info di login
$username = $user->getUsername();
$is_admin = $user->isAdmin();

?>