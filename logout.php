<?php
/**
 * 
 * 
 * @author mattevigo
 * 
 * @project Sandro del Pistoia
 * @created 23/mar/2009
 */
session_start();
session_unset();

session_regenerate_id(true);

header("Location:login.php");
?>
