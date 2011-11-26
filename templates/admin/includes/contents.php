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

define("OPTION_PATH", PAGES . '/templates/admin/views');

/**
 * Inserisce il div contenente il messaggio, warning o error registrato nelle
 * relative variabili di sessione
 *
 */
function load_advise()
{
	//var_dump($_SESSION);
	$msg = "no message";
	if( isset($_SESSION['message']) && $_SESSION['message'] != null ) echo "<div class=\"message\"><b>".get_message()."</b></div>";
	if( isset($_SESSION['warning']) && $_SESSION['warning'] != null) echo "<div class=\"warning\"><b>".get_warning()."</b></div>";
	if( isset($_SESSION['error']) && $_SESSION['error'] != null) echo "<div class=\"error\"><b>".get_error()."</b></div>";
}

/*function load_option($view_name)
{
	$view_path = OPTION_PATH . "/" . $view_name;

	if(file_exists($view_path))
		$view_path = $view_path . "/view.php";
	else
		$view_path = $view_path . ".php";

	if(!file_exists($view_path))
		throw new FrameworkException("$view_path: view doesn't exists");
	else
		require_once $view_path;
}*/
?>