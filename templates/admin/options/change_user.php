<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package PHPorcupine
 * @version 1.0
 * 
 * Change user details view for form
 */
defined("_ENTRY_") or die("Restricted Access!");
defined("_ADMIN_") or header("Location:login.php");	// only admin-entry

$user = get_session_user();
?>
<h4>Modifica informazioni</h4>
<div id="breadcumbs"><a href=""><?php echo SITE_NAME ?></a> &#62; <a href="">admin</a> &#62; <a href="">Modifica informazioni</a></div>

<?php if($_SESSION['message'] != null) echo "<div class=\"message\"><b>".$_SESSION['message']."</b></div>"; $_SESSION['message'] = null;?>

<div id="recipient-content">
	<form name="change-user" action="<?php echo WEB_ROOT."/admin.php?script=change_user&content=html"?>" method="post">
		<fieldset>
		<legend>Modifica dei dati utente</legend>
		<label>email: <input type="text" value="<?php echo $user->get('user_email');?>" name="user_email"/></label>
		<button type="submit" >Cambia</button>
		</fieldset>
		
	</form>

</div>