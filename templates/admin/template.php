<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package InTVWeb
 * @version 1.0
 * 
 * Template per l'admin
 */
defined("_ENTRY_") or die("Restricted Access!");
defined("_ADMIN_") or header("Location:login.php");	// only admin-entry

define( "LOCAL_ROOT", $_SERVER['DOCUMENT_ROOT'] . "/templates/admin" );
define("LOCAL_WEB_ROOT", WEB_ROOT."/templates/admin" );

import("includes.contents");
import("templates.admin.includes.framework");

$db = getDB();

$user = get_session_user();	// user object for this session
$option = get_var( "option", "get", NULL );
//echo "Template Admin ({$user->getUsername()})<br />";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	
	<link type="text/css" rel="stylesheet" href="<?php echo WEB_ROOT."/templates/admin/css/style.css"; ?>" />
	<link type="text/css" rel="stylesheet" href="<?php echo WEB_ROOT."/templates/admin/css/custom-theme/jquery-ui-1.8.1.custom.css"; ?>" />
	<?php load_css($option); ?>
	
	<script type="text/javascript" src="<?php echo WEB_ROOT."/js/jquery-1.4.2.min.js"; ?>"></script>
	<script type="text/javascript" src="<?php echo WEB_ROOT."/js/jquery-ui-1.8rc2.custom.min.js"; ?>"></script>		
	<script type="text/javascript" src="<?php echo LOCAL_WEB_ROOT."/js/jquery.url.js"; ?>"></script>
	<script type="text/javascript" src="<?php echo LOCAL_WEB_ROOT."/js/ppanel.js"; ?>"></script>
	<?php load_js($option); ?>
	
	<title><?php echo SITE_NAME; ?> - Admin</title>
</head>
<body>

	<div id="header">
		<table>
			<tr>
				<td id="header-title"><b><?php echo SITE_NAME; ?></b></td>
				<td id="header-subtitle"><div id="subtitle"><?php echo SUB_TITLE; ?></div></td>
			</tr>
		
		</table>
		
		<div id="header-logout"><?php echo $user->getUsername(); ?> | <a href="logout.php">logout</a></div>
	</div>
	
	<div id="content-body">
		<div id="menu-left">
		<div id="datepicker"></div>
		
			<div class="options" id="menu-left-options1">
				<h4>Admin</h4>
				<div id="menu-left-options-content" class="options-content">
					
				</div>
			</div>

			<div class="options" id="menu-left-options3">
				<h4>Options</h4>
				<div id="menu-left-options-content" class="options-content">
					<div class="menu-item"><a class="menu" href="<?php echo WEB_ROOT; ?>/admin.php?option=change_user">Change Details</a></div>
					<div class="menu-item"><a class="menu" href="<?php echo WEB_ROOT; ?>/admin.php?option=change_password">Change Password</a></div>
					<div class="menu-item"><a class="menu" href="<?php echo WEB_ROOT; ?>/admin.php?option=add_user">Add User</a></div>
					<div class="menu-item"><a class="menu" href="<?php echo WEB_ROOT; ?>/admin.php?option=add_user">Manage Users</a></div>
					
				</div>
			</div>
		</div>
		
		<div id="recipient">
			<?php load_option( get_var( "option", "get", NULL ) ); ?>
		</div> <!-- END recipient -->
		<div id="footer">PHPorcupine Administration Panel &copy;2011 <a href="mailto:mattevigo@gmail.com">mattevigo</a></div>
	</div>

</body>
</html>