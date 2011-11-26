<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package PHPorcupine
 * @version 1.0
 * 
 * Change user password view
 */
defined("_ENTRY_") or die("Restricted Access!");
defined("_ADMIN_") or header("Location:login.php");	// only admin-entry

$user = get_session_user();
?>
<h4>Cambia password</h4>
<div id="breadcumbs"><a href=""><?php echo SITE_NAME ?></a> &#62; <a href="">admin</a> &#62; <a href="">Cambia password</a></div>

<?php if($_SESSION['message'] != null) echo "<div class=\"message\"><b>".$_SESSION['message']."</b></div>"; $_SESSION['message'] = null;?>
<?php if($_SESSION['error'] != null) echo "<div class=\"error\"><b>".$_SESSION['error']."</b></div>"; $_SESSION['error'] = null;?>

<div id="recipient-content">
	<form name="change-user" action="<?php echo WEB_ROOT."/admin.php?script=change_password&content=html"?>" method="post">
		<fieldset>
		<legend>Cambio password</legend>
		<label><input type="password" name="user_password"/> inserisci la vecchia password</label><br /><br />
		<label><input type="password" name="new_password"/> inserisci la nuova password</label><br /><br />
		<label><input type="password" name="repeat_password"/> inserisci nuovamente la nuova password</label><br /><br />
		<button type="submit" >Cambia</button>
		</fieldset>
		
	</form>
</div>