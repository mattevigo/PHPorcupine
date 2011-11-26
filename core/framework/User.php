<?php
import("core.data.DBEntity");

/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package DBEntity
 * @version 0.2
 */
class User extends DBEntity
{
	const GROUPS_TABLE = "groups";
	const USERS_GROUPS_TABLE = "users_groups";
	
	const USERS_ACCOUNTS_TABLE = "users_accounts";
	const USERS_ACCOUNTS_KEY = "user_account_id";
	
	const USERS_FACEBOOK_TABLE = "phporcupine_facebook_accounts";
	const USERS_FACEBOOK_KEY = "account_id";
	
	/**
	 * Costruttore
	 *
	 * @todo test con il prefix
	 */
	public function __construct(DB $db, $uid)
	{

		parent::__construct($db, $db->t_users, "user_id", $uid);
	}

	/**
	 * Cambia la password utente dopo aver verificato la validita' della vecchia password
	 *
	 * @param string $old_password
	 * @param string $new_password
	 *
	 * @throws DBException per problemi con il database
	 * @throws EntityException se la vecchia password inserita e' sbagliata
	 *
	 * @todo modifica della password nel db con il meccanismo di autenticazione di joomla
	 */
	public function setPassword($old_password, $new_password)
	{
		//echo $old_password . " / ".$new_password . "<br />";
		//echo "DB:". $this->db_wrapper->t_users;
		$mysql_query = "SELECT `user_password`
						FROM `{$this->db_wrapper->name()}`.`{$this->db_wrapper->t_users}`
						WHERE `user_id`={$this->getId()}";
		//echo $mysql_query;
		$result = $this->db_wrapper->query($mysql_query);
		$row = mysql_fetch_row($result);
		//echo $row[0] . "<br />";
		//echo $old_password . "<br />";

		if(strcmp($old_password, $row[0]) == 0){

			$mysql_query = "UPDATE `{$this->db_wrapper->name()}`.`{$this->db_wrapper->t_users}`
							SET `user_password`='$new_password'
							WHERE `user_id`={$this->getId()}";

			$this->db_wrapper->query($mysql_query);

		} else throw new EntityException("Password sbagliata");
	}
	
	/**
	 * 
	 * @param $groupname
	 * @return unknown_type
	 */
	public function isInGroup( $groupname )
	{
		foreach ( $this->getGroups() as $key => $value )
		{
			if( strcmp( $value, $groupname ) == 0 )
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getGroups()
	{
		$groups = array();
		
		$groups_t = $this->getDB()->prefix() . self::GROUPS_TABLE;
		$users_groups_t = $this->getDB()->prefix() . self::USERS_GROUPS_TABLE;
		
		$sql = "SELECT g.group_id, g.group_name ".
				"FROM $groups_t g, $users_groups_t ug ".
				"WHERE g.group_id=ug.group_id AND ug.user_id={$this->getId()}";
		
		$result = $this->getDB()->query( $sql );
		
		while ( $row = mysql_fetch_assoc( $result ) )
		{
			$groups[$row['group_id']] = $row['group_name'];
		}
		
		return $groups;
	}
	
	/**
	 * Retrive the DBEntity of an external account
	 * 
	 * @param $account_name es: 'Facebook', 'Twitter'
	 * @return DBEntity
	 */
	public function getAccountInformationEntity( $account_name )
	{
		$entities = DBEntity::getArray( $this->getDB(), self::USERS_ACCOUNTS_TABLE, "DBEntity", NULL, "user_id=".$this->getId() );
		
		foreach( $entities as $key => $e )
		{
			if( strcmp($account_name, $e->get("user_account_name")) == 0 )
			{
				return $e;
			}
		}
		
		return NULL;
	}

	/**
	 * 
	 * @param $new_email
	 * @return unknown_type
	 */
	public function setEmail($new_email)
	{
		$this->set('user_email', $new_email);
	}

	/**
	 * @deprecated 0.2 - 09/set/2009
	 */
	public function getId()
	{
		return parent::get('user_id');
	}

	public function getUsername()
	{
		return parent::get('user_username');
	}

	/**
	 *
	 */
	public function isAdmin()
	{
		if($this->get('user_admin') == 1)
		return true;
		else return false;
	}
	
	public static function getUsernameFromId( DB $db, $id )
	{
		$sql = "SELECT user_username FROM ".$db->t_users." WHERE user_id=$id";
		
		$result = $db->query( $sql );
		$user = mysql_fetch_assoc( $result );
		
		return $user['user_username'];
	}
	
	public static function getUserFromId( DB $db, $id )
	{
		$sql = "SELECT * FROM ".$db->t_users." WHERE user_id=$id";
		
		$result = $db->query( $sql );
		$user = mysql_fetch_assoc( $result );
		
		$userobj = NULL;
		if($user['user_admin'] == 1)
		{
			$userobj = new Admin($db);
		}
		else
		{
			$userobj = new User($db);
		}
		
		foreach($user as $key => $value)
		{
			$userobj->set($key, $value);
		}
		
		return $userobj;
	}

	/**
	 * Funzione di login, verifica username e password e se questi coincidono con i relativi valori del database
	 * viene restituito un oggetto di tipo <code>User</code> relativo all'utente
	 *
	 * @param DB $db_wrapper
	 * @param string $username
	 * @param string $password
	 *
	 * @throws 	DBException se si sono verificati problemi con il database
	 * 			LoginException
	 * @return un  nuovo <code>User</code>
	 *
	 * @todo modifica del login utilizzando il meccanismo di joomla
	 */
	public static function login(DB $db, $username, $password)
	{
		$user = null;
		$db_query = "	SELECT `user_id` , `user_password`, `user_admin`
						FROM `$db->t_users`
						WHERE `user_username` = '$username'";

		$result = $db->query($db_query);
		$row = mysql_fetch_row($result);
		//echo "user: " . $row[0] . "<br />";
		//echo $db_query."<br />";

		if(count($row) != 3)
		{
			throw new LoginException("Wrong Username");
		}
		else if(strcmp($password, $row[1]) == 0)
		{
			if($row[2] == 1) $user = new Admin($db, $row[0]);
			if($row[2] == 0) $user = new User($db, $row[0]);
		}
		else throw new LoginException("Wrong Password");

		return $user;
	}

	/**
	 * Funzione di login, verifica username e password e se questi coincidono con i relativi valori del database
	 * viene restituito un oggetto di tipo <code>User</code> relativo all'utente
	 *
	 * @param DB $db_wrapper
	 * @param string $username
	 * @param string $password
	 *
	 * @throws 	DBException se si sono verificati problemi con il database
	 * 			LoginException
	 * @return un  nuovo <code>User</code>
	 *
	 * @todo modifica del login utilizzando il meccanismo di joomla
	 */
	public static function jlogin(DB $db, $username, $password)
	{
		$user = null;
		$db_query = "	SELECT `user_id` , `user_password`, `user_admin`
						FROM `$db->t_users`
						WHERE `user_username` = '$username'";

		$result = $db->query($db_query);
		$row = mysql_fetch_row($result);
		//echo "user: " . $row[0] . "<br />";
		//echo $db_query."<br />";

		if(count($row) != 3)
		{
			throw new LoginException("Wrong Username");
		}
		
		$parts	= explode( ':', $row[1] );
		$crypt	= $parts[0];
		$salt	= @$parts[1];
		$testcrypt = User::getCryptedPassword($password, $salt);
		echo $crypt . "==" . $testcrypt ."<br />";

		if ($crypt == $testcrypt) // is autenticate
		{
			if($row[2] == 1) $user = new Admin($db, $row[0]);
			if($row[2] == 0) $user = new User($db, $row[0]);
		}
		else throw new LoginException("Wrong Password");
		
		return $user;
	}

	/**
	 * Formats a password using the current encryption.
	 *
	 * @access	public
	 * @param	string	$plaintext	The plaintext password to encrypt.
	 * @param	string	$salt		The salt to use to encrypt the password. []
	 *								If not present, a new salt will be
	 *								generated.
	 * @param	string	$encryption	The kind of pasword encryption to use.
	 *								Defaults to md5-hex.
	 * @param	boolean	$show_encrypt  Some password systems prepend the kind of
	 *								encryption to the crypted password ({SHA},
	 *								etc). Defaults to false.
	 *
	 * @return string  The encrypted password.
	 */
	function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
	{
		// Get the salt to use.
		$salt = User::getSalt($encryption, $salt, $plaintext);
		echo "Salt: $salt<br />"; 
		echo "Password: $plaintext<br />";

		// Encrypt the password.
		$encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
		echo "Encrypted: $encrypted : ".md5($plaintext.$salt)."<br />";
		return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;
	}

	/**
	 * Returns a salt for the appropriate kind of password encryption.
	 * Optionally takes a seed and a plaintext password, to extract the seed
	 * of an existing password, or for encryption types that use the plaintext
	 * in the generation of the salt.
	 *
	 * @access public
	 * @param string $encryption  The kind of pasword encryption to use.
	 *							Defaults to md5-hex.
	 * @param string $seed		The seed to get the salt from (probably a
	 *							previously generated password). Defaults to
	 *							generating a new seed.
	 * @param string $plaintext   The plaintext password that we're generating
	 *							a salt for. Defaults to none.
	 *
	 * @return string  The generated or extracted salt.
	 */
	function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
	{
		// Encrypt the password.
		$salt = '';
		if ($seed)
		{
			$salt = $seed;
		}
		return $salt;
	}
	
	public function getFacebookAccount()
	{
		
		// $infos = DBEntity::getArray($this->getDB(), self::USERS_FACEBOOK_KEY, "DBEntity", null, "user_id=".$this->getID() );
		
		# Query for retrieving facebook data 
		$mysql_query = "SELECT * FROM ".self::USERS_FACEBOOK_TABLE." WHERE user_id = ".$this->getID();
		
		#debug
		//echo "<h2>Query: $mysql_query</h2>";
		
		# Execute query
		$res = $this->getDB()->query($mysql_query);
		
		#debug
		//echo "<h2>Risultato</h2>\n<pre>".print_r($res)."</pre>";
		
		$infos = mysql_fetch_assoc($res);
		
		// Verify that user has setup and verified facebook app
		$return = mysql_num_rows($res);
		if($return)
		{
			// Set user informations.
			// Theese information will be used by facebook api client,
			// for making posts, update status, publishing events and other
			
			$this->set("fb_account_id", $infos['account_id']);				# Account id
			$this->set("fb_oauth_uid", $infos['oauth_uid']);					# Facebook OpenAuth id, the profile id
			$this->set("fb_username", $infos['username']);					# Facebook Name and Surname 
			$this->set("fb_session_key", $infos['session_key']);	# Returned after authorizing application
			$this->set("fb_expires", $infos['expires']);					# Returned after authorizing application
			$this->set("fb_secret", $infos['secret']);						# Returned after authorizing application
			$this->set("fb_sig", $infos['sig']);							# Returned after authorizing application
		}
	return $return;
	}
}

class Admin extends User
{
	/**
	 *
	 */
	function __construct(DB $db_wrapper, $uid=null)
	{
		parent::__construct($db_wrapper, $uid);
	}

	/**
	 * @todo
	 */
	public function createNewUser($name)
	{

	}
}

class LoginException extends Exception
{
	function __construct($message){
		parent::__construct($message);
	}
}
?>
