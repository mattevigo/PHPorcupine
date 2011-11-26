<?php
/**
 * Contains function for the framework management, like imports ecc...
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 * @package DBEntity
 * @version 0.2
 */

/**
 * Definition of the FrameworkException for the handling of errors
 *
 */
class DebugException extends Exception
{
	function __construct($message){
		parent::__construct($message);
	}
}

/**
 * Dumper for data structure
 */
function dumper($var)
{
	echo "<pre><br />";
	print_r($var);
	echo "</pre>";
}


function errorLog($err,$loc = NULL)
{
	
	$file = fopen(dirname(__FILE__)."/../logs/error.log","a");
	$date = date("Ymd [H:i:s]");
	$addr = $_SERVER['REMOTE_ADDR'];
	#text formatting
	if($loc)
		$loc = "[$loc]";
	else
		$loc = "[{$_SERVER['SCRIPT_FILENAME']}";
	if($_SERVER['QUERY_STRING'])
		$loc .= "?".$_SERVER['QUERY_STRING'];
	
	$loc.= "]";
	$msg = "$date [$addr] $loc: $err\n";
	$fwrite = fwrite($file,$msg);
	fclose($file);
	return $fwrite;
}

function accessLog($loc = null)
{
	$file = fopen(dirname(__FILE__)."/../logs/access.log","a");
	//dumper(dirname(__FILE__)."/../logs/access.log");
	$addr = $_SERVER['REMOTE_ADDR'];
	
	#text formatting
	if($loc)
		$loc = " [$loc]";
	else
		$loc = "[{$_SERVER['SCRIPT_FILENAME']}";
	if($_SERVER['QUERY_STRING'])
		$loc .= "?".$_SERVER['QUERY_STRING'];
	
	$loc.= "]";
	
	$date = date("Ymd [H:i:s]");
	$msg = "$date [$addr]: $loc\n";
	$fwrite = fwrite($file,$msg);
	fclose($file);
	return $fwrite;
}