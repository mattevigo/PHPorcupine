<?php
/**
 * Install script, it create the tables in the database
 * 
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package DBEntity
 * 
 * @version 0.2 - last update 08 nov 2009 - 16:00
 */
 
require_once("config.php");
require_once DB;

define( "INSTALL_SCRIPT", SERVER_ROOT.DS."schema".DS."SQL".DS."SQL_Schema" );

$db = new DB($db_config);

$db->script( INSTALL_SCRIPT );

?>