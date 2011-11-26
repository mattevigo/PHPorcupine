<?php
/**		
 * @author Matteo Vigoni																				
 * @package PHPorcupine
 * @version 2.0a																									 
 */

// constants
define("VERSION", '2.0a');					# versione corrente
define("DEFAULT_DBNAME", '');	# nome di default del database
define("SITE_NAME", 'PHPorcupine');
define("SUB_TITLE", "OpenSource-ObjectOriented");

// core
define("SERVER_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("DS", DIRECTORY_SEPARATOR );
define("CORE_PATH", $_SERVER['DOCUMENT_ROOT'].DS."core");
define("DBENTITY", CORE_PATH.DS."DBEntity.php");
define("SESSION", CORE_PATH.DS."Session.php");
define("USER", CORE_PATH.DS."User.php");
define("DB", CORE_PATH.DS."DB.php");

define("BLOG", CORE_PATH.DS."blog");
define("BLOGPOST", BLOG.DS."Blogpost.php");

//scripts
/** Script used for login */
define("LOGIN_FILE", $_SERVER['SERVER_NAME']."/login.php");
/** Script used for logout */
define("LOGOUT_FILE", $_SERVER['SERVER_NAME']."/logout.php");

//incudes
define("INCLUDES",$_SERVER['DOCUMENT_ROOT'].DS."includes");
define("SESSION_INCLUDE", INCLUDES."/session.php");

//directories
define("PAGES",SERVER_ROOT.DS."pages");
define("TEMPLATES_DIR", SERVER_ROOT.DS."templates");
define("VIEWS_DIR", SERVER_ROOT.DS."views"); // DEPRECATED

//scripts
define("SCRIPTS",$_SERVER['DOCUMENT_ROOT'].DS."scripts");

//web paths
define("WEB_ROOT", "http://".$_SERVER['HTTP_HOST']);

/**
 * Configuration variables for the database 
 * @var array contains information about your dbms configuration
 * @see the config_dbentity.php example page
 * @link http://wiki.github.com/mattevigo/dbentity/configphp
 */
$db_config = array(
	'dbms'			=>	'',
	'host'			=>	'localhost',
	'port'			=>	'',
	'name'			=>	'phporcupine',
	'user'			=>	'root',
	'pw'			=>	'porcupine',
	'prefix' 		=> 	'phporcupine_',
	't_users'		=>	'users',
	't_sessions'	=>	'sessions'
	//inserire qua i nomi di eventuali tabelle
);

// formats
/** The algoritm used for digest  */
define("HASH_ALGO", "md5");					# algoritmo hash
define("DATE_FORMAT_GNU", "j F Y");			# formato data secondo lo standard GNU (usato in strtotime)
/** The date format that will dislplayed in your pages */
define("DATE_FORMAT", "j/m/Y \o\\r\e G:i");	# formato data

// options
/** The length of the session (seconds)  */
define("SESSION_TIME", 0);		# durata sessione in secondi (20 min)
/** The id used on the database for anonymous users */
define("ANONYMOUS_ID", 0);			# user_id di default per utente anonimo non autenticato

/**
 * Import php file, for example if you need to import the file ./includes/my_file.php, call this function:
 *
 * import("includes.my_file");
 *
 * @param $path
 */
function import($path)
{
	$new_path = $_SERVER['DOCUMENT_ROOT'] . "/" . str_replace(".", "/", $path) . ".php";
	//echo $new_path . "<br />";
	
	if( file_exists($new_path) )
		require_once $new_path;
}
?>