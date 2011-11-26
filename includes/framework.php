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

import("core.data.DB");
import("core.framework.Session");

/**
 * Definition of the FrameworkException for the handling of errors
 *
 */
class FrameworkException extends Exception
{
	function __construct($message){
		parent::__construct($message);
	}
}

/**
 * EXPERIMENTAL
 * Restituisce un parametro post o get
 *
 * @param $varname			il nome del parametro
 * @param $method			il metodo del parametro (post|get)
 * @param $default_value	il valore di default del parametro qualora non esistesse
 */
function get_var($varname, $method="", $default_value="")
{
	global $_POST;
	global $_GET;

	if( isset($_GET[$varname]) && ((strcasecmp($method, "get") == 0 || $method == null)) )
	{
		return $_GET[$varname];
	}
	else if(isset($_POST[$varname]))
	{
		return $_POST[$varname];
	}
	else return $default_value;
}

/**
 * Redirect the page to the target script_name
 *
 * @param $script_name
 * @return unknown_type
 */
function script( $script_name=null, $content_type="application/json" )
{
	$script_path = SCRIPTS . "/" . $script_name . ".php";
	//echo $script_path;
	if(!file_exists($script_path)) throw new FrameworkException("$script_path: script doesn't exist");

	header("content-type: ".$content_type);
	require_once SCRIPTS . "/" . $script_name . ".php";
}

/**
 * Redirect the page to the target script_name
 *
 * @param $script_name
 * @return unknown_type
 */
function page($page_name=null, $content_type="text/html")
{
	$page_path = PAGES . "/" . $page_name . ".php";
	if(!file_exists($page_path)) throw new FrameworkException("$page_path: page doesn't exist");

	header("content-type: ".$content_type);
	require_once $page_path;
}

function add_rewrite_rule( $rule )
{
	if ( $resource = fopen( SERVER_ROOT.DS.".htaccess", "a") )
	{
		if( fwrite( $resource, $rule ) )
			return true;
			
		fclose( $resource );
	}
	else
	{
		return false;
	}
}

/**
 * Get the DB object
 *
 * @param $session_time the expire time of the session
 * @return DB the object of the database configuration
 */
function getDB($session_time=SESSION_TIME)
{
	global $db_config;
	return new DB(&$db_config, $session_time);
}

/**
 * Load a template in a page, if no name is specified load the 'default' template
 *
 * @param $template_name the name of the template to load
 * @throws FrameworkException if target template does'nt exists
 *
 * @deprecated since version 2.0 (use Page controller)
 */
function load_template($template_name='default', Page $p)
{
	$template_path = TEMPLATES_DIR . "/" . $template_name . "/template.php";

	if(!file_exists($template_path)) throw new FrameworkException("$template_path: template doesn't exist");

	$p->setTemplate($template_path);
	$p->display();
}

/**
 * Load a view in a page. There are two kind of views aviable: simple view or advanced view.
 * Simple view consist in only one file (.php) located in the directory PAGE/views.
 * Advanced view consist in a directory who have inside the default view.php and some other file
 * that can be called inside view.php.
 *
 * The calling of that function doesn't make different between the two views.
 *
 * @param $view_name the name of the view
 * @throw FrameworkException if target view doesn't exists
 */
function load_view($view_name, Model $model=null)
{
	$view_path = TEMPLATES_DIR.DS."default".DS."views";

	if( defined("TEMPLATE") )
	$view_path = TEMPLATES_DIR.DS.TEMPLATE.DS."views";

	$view_path .= DS.$view_name;

	if( !file_exists( $view_path) )
	{
		$view_path .= ".php";
		if( !file_exists( $view_path ) )
		throw new FrameworkException("View '$view_name' doesn't exists.");
	}
	else
	{
		$view_path .= DS."view.php";
		if( !file_exists( $view_path ) )
		throw new FrameworkException("View '$view_name' doesn't exists.");
	}

	if($model == null)
	{
		require_once $view_path;
	}
	else
	{
		$model->setView($view_name);
		$model->display();
	}
}

/**
 *
 * @param $view_name
 * @param $entity
 */
function load_simple_view( $view_name, Object $obj=NULL )
{
	$view_path = TEMPLATES_DIR.DS."default".DS."views";

	if( defined("TEMPLATE") )
	$view_path = TEMPLATES_DIR.DS.TEMPLATE.DS."views";

	$view_path .= DS.$view_name;

	$view_path .= ".php";
	if( !file_exists( $view_path ) )
	throw new FrameworkException("View '$view_name' doesn't exists.");

	if( $obj == NULL )
	{
		require $view_path;
	}
	else
	{
		$obj->insertIntoHTML( $view_path );
	}
}

/**
 * EXPERIMENTAL
 * 
 * @param unknown_type $redirect_url
 * @return unknown_type
 */
function set_redirect( $redirect_url )
{
	//global $_SESSION;

	$_SESSION['redirect_page'] = $redirect_url;
}

/**
 * EXPERIMENTAL
 * 
 * @param unknown_type $redirect_page
 * @return unknown_type
 */
function redirect($redirect_page=NULL)
{
	//global $_SESSION;
	
	if( $redirect_page != NULL )
	{
		header("location: $redirect_page");
		$_SESSION['redirect_page'] = NULL;
	}
	else if( $_SESSION['redirect_page'] != NULL )
	{
		header("location: ".$_SESSION['redirect_page']);
		$_SESSION['redirect_page'] = NULL;
	}
}
?>