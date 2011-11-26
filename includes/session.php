<?php
/**
 * Functions for session manager
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 * @package PHPorcupine
 * @version 2.0
 */
//require_once $_SERVER['DOCUMENT_ROOT']."/config_dbentity.php";
import("config");
import("core.DB");
import("core.Session");
import("core.User");

/**
 * @deprecated use getDB in includes.framework package
 *
 * Restituisce l'oggetto DB specifico per questa sessione,
 * se non esiste ancora un oggetto DB questo viene creato
 */
function get_session_db()
{
	global $db_config;

	//echo VERSION."<br />";
	//var_dump($db_config);

	$db = null;
	if(!isset($_SESSION['db']))
	{
		//echo "costruisco un oggetto DB<br />";
		$db = new DB(&$db_config, SESSION_TIME);
		$_SESSION['db'] = &$db;//serialize($db);
	}
	else {
		//echo $_SESSION['db']->toString();
		//echo "recupero un oggetto DB<br />";
		$db = &$_SESSION['db'];//unserialize($_SESSION['db']);
	}

	return $db;
}

/**
 * Get the logged User or throw a SessionException if is not logged
 *  
 * @param DB $db
 * @return User
 * @throws SessionException if user not is logged
 */
function getUser( DB &$db )
{
	if( user_is_logged( &$db ) )
	{
		return new User( &$db, $_SESSION['uid'] );
	}
	else
	{
		throw new SessionException( "Not logged" );
	}
}

/**
 * Restituisce l'oggetto User relativo all'utente loggato
 * Questa funzione non esegue controlli di consistenza, e' buona norma quindi chiamarla
 * dopo aver verificato l'esistenza di un utente loggato con user_is_logged()
 * 
 * @deprecated use getUser()
 *
 * @return l'oggetto User relativo all'utente loggato
 */
function get_session_user()
{
	if(isset($_SESSION['user']))
	return unserialize($_SESSION['user']);
	else return null;
}

/**
 * Restituisce l'oggetto Session relativo alla sessione corrente
 * 
 * @deprecated
 *
 * @return l'oggetto Session relativo alla sessione corrente
 */
function get_session_data()
{
	if(isset($_SESSION['data']))
	return unserialize($_SESSION['data']);
	else return null;
}

/**
 * Fornisce informazioni sulla sessione corrente
 *
 * @return true se la sessione corrente e' autenticata
 * 			false altrimenti
 */
function user_is_logged($db, $renew=true)
{
	if(isset($_SESSION['uid']))
	{
		if(Session::validate($db, session_id(), $_SESSION['uid'], SESSION_TIME))
		{
			// viene aggiornato il valore di session_time
			if($renew)
			{
				$session = get_session_data();
				$session->set('session_time', time());
				$session->commit();
				$_SESSION['data'] = serialize($session);
			}
			return true;
		}
		else
		{
			session_unset();
			session_regenerate_id();
			return false;
		}
	}
	else return false;
}

// Session Messages ////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Setta il messaggio $_SESSION['message']
 */
function set_message($msg)
{
	$_SESSION['message'] = $msg;
}

/**
 * Setta il messaggio $_SESSION['warning']
 */
function set_warning($wrn)
{
	$_SESSION['warning'] = $wrn;
}

/**
 * Setta il messaggio $_SESSION['error']
 */
function set_error($err)
{
	$_SESSION['error'] = $err;
}

/**
 * Ritorna e resetta la variabile di sessione $_SESSION['message']
 *
 * @return unknown_type
 */
function get_message()
{
	if($_SESSION['message'] != null)
	{
		$msg = $_SESSION['message'];
		$_SESSION['message'] = null;
		return $msg;
	}
	return null;
}

/**
 * Ritorna e resetta la variabile di sessione $_SESSION['message']
 *
 * @return unknown_type
 */
function get_warning()
{
	if($_SESSION['warning'] != null)
	{
		$wrn = $_SESSION['warning'];
		$_SESSION['warning'] = null;
		return $wrn;
	}
	return null;
}

/**
 * Ritorna e resetta la variabile di sessione $_SESSION['message']
 *
 * @return unknown_type
 */
function get_error()
{
	if($_SESSION['error'] != null)
	{
		$err = $_SESSION['error'];
		$_SESSION['error'] = null;
		return $err;
	}
	return null;
}
?>
