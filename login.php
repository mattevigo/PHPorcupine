<?php
/**
 * Login script
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 * @project PHPorcupine
 * @created 20/mar/2009
 */
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/includes/framework.php";

import("includes.session");
import("includes.debug");

import("core.framework.User");

session_start();

$db = getDB();

// deploy indirizzo
$from = "admin.php";
if(isset($_GET['from'])) $from = $_GET['from'];

if(isset($_SESSION['data']))
{
	//echo "Sessione gia' in corso...redirect";
	header("Location:".$from);
	exit();
}

if( isset($_POST['username']) && isset($_POST['password']) )
{ 	// ### se le variabili POST contengono username e password
try
{
	//echo $_POST['username']."<br />";
	//echo hash(HASH_ALGO, $_POST['password'])."<br />";

	$user = User::login($db, $_POST['username'], hash(HASH_ALGO, $_POST['password']));

	$_SESSION['uid'] = $user->getId();
	$_SESSION['username'] = $user->getUsername();
	$_SESSION['data'] = serialize(new Session($db, session_id(), $user->getId()));
	$_SESSION['user'] = serialize($user);

	//echo "UID ".$_SESSION['uid']."<br />";
	//echo "Autenticato...redirect";
	header("Location:".$from);
	//redirect

}
catch (EntityException $e)
{
	//echo "regenerate";
	session_regenerate_id(true);
	$user = User::login($db, $_POST['username'], hash(HASH_ALGO, $_POST['password']));

	$_SESSION['uid'] = $user->getId();
	$_SESSION['username'] = $user->getUsername();
	$_SESSION['data'] = serialize(new Session($db, session_id(), $user->getId()));
	$_SESSION['user'] = serialize($user);
	header("Location:".$from);
}
catch (LoginException $e)
{
	echo $e->getMessage();
}
catch (SessionException $e)
{
	echo $e->getMessage();
}
catch (DBException $e)
{
	echo $e->getMessage();
}
}
else if(!isset($_SESSION['uid']) || $_SESSION['uid'] == ANONYMOUS_ID)
{ // ### caso in cui non sia assegnata un user id oppure siamo in una sessione anonima
?>
<html>
<head>
<title><?php echo SITE_NAME; ?> - Login</title>

<style type="text/css">
body {
	margin: 0px;
	font: 100% "Trebuchet MS", Arial, Helvetica, sans-serif;
}

div.content {
	border-style: solid;
	border-width: 1px;
	margin: 30px auto auto;
	padding: 5%;
	text-align: center;
	width: 440px;
}

div.left {
	float: left;
	text-align: right;
	width: 80px;
	padding: 1px;
}

div.right {
	text-align: left;
}

div.form {
	margin: 20px auto;
	width: 254px;
}
</style>
</head>
<body>
<div class="content"><img src="logo.jpg" /> <br />
	<small>Eseguire il login per amministrare <i><?php echo "http://".$_SERVER['HTTP_HOST']; ?></i></small>
	<div class="separator" ></div>
	<div class="form">
		<form action='login.php?from=<?echo $from;?>' method='post' id="login_form">
			<div class="left">Username </div>
			<div class="right"><input type='text' name='username' /></div>
			<div class="left">Password </div>
			<div class="right"><input type='password' name='password' /></div>
			<div class="left"><input type='submit' value='Login'></div>
		</form>
	</div>
	<div class="separator" ><small>Developed by <a target="_blank"href="mailto:info@vivatech.it">ViVaTech</a></small></div>
</div>
</body>
</html>
<?php
}
else
{ // ### caso di utente gia' autenticato, dobbiamo validare la sessione
	if(Session::validate($db, $_SESSION['data'].getId(), $_SESSION['uid'], SESSION_TIME))
		header("Location:".$from);
}

//dumper(&$db);
?>
