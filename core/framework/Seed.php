<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @project PHPorcupine
 * @package core
 * @version 2.0
 *
 * The Seed is a generic information published (or drafted) in the site.
 * Each object have an unambiguous seed
 */
require_once( $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."config.php" );
require_once( DBENTITY );

// Class Definition ///////////////////////////////////////////////////////////////////////////
class Seed extends DBEntity
{
	// tabella
	const TABLE = 'seeds';
	const PRIMARY_KEY = 'seed_id';

	var $table = 'seeds';
	var $primary_key = 'seed_id';

	const AUTH_GROUP = "";

	public $usr = NULL;		// User object

	/**
	 *
	 * @param DB $db
	 * @param unknown_type $table_name
	 * @param unknown_type $primary_key
	 * @param unknown_type $id
	 * @param User $usr
	 */
	public function __construct( DB $db, $table_name=null, $primary_key=null, $id=null, User $usr=NULL )
	{
		parent::__construct($db, $db->prefix().$this->table, $this->primary_key, $id);

		if( $id != NULL )
		{
			$this->user_username = User::getUsernameFromId( &$db, $this->get('user_id'));
		}
		else
		{
			$this->usr = $usr;

			if( $usr != NULL )
			$this->set('user_id', $usr->getId());
		}
	}

	// GET /////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get the classname that rappresent the object
	 *
	 * @return string
	 */
	public function getClasspath()
	{
		return $this->get('seed_classpath');
	}

	/**
	 * Get the User for this Seed
	 *
	 * @return User
	 */
	public function getUser()
	{
		return $this->usr;
	}

	/**
	 *
	 */
	public function getUsername()
	{
		if( $this->usr != NULL )
		{
			return $this->usr->getUsername();
		}
		else
		{
			return $this->get('user_username');
		}
	}

	/**
	 *
	 */
	public function getPermalink()
	{
		return $this->get("seed_permalink");
	}

	/**
	 *
	 * @param unknown_type $format
	 */
	public function getDate( $format )
	{
		return date( $format, $this->get('seed_date') );
	}

	/**
	 *
	 * @param unknown_type $class
	 */
	public function getObjectId( $class )
	{
		$vars = get_class_vars( $class );

		$table = $this->getDB()->prefix().$vars['table'];
		$primary_key = $vars['primary_key'];

		$sql = "SELECT $primary_key ".
				"FROM $table ".
				"WHERE seed_id={$this->getId()}";

		$result = $this->getDB()->query( $sql );
		$row = mysql_fetch_assoc( $result );

		return $row[$primary_key];
	}

	/**
	 * Get the Object associated to $this Seed.
	 * The Object type depend to the 'seed_class' value in table 'seeds'
	 *
	 * @return unknown
	 */
	public function getObject( $classpath=NULL )
	{
		$object = NULL;
		
		// import the class of seeded object
		$class = "";
		if( $classpath == NULL )
		{
			import( $this->getClasspath() );
			$class = Seed::getClassname( $this->getClasspath() );
		}
		else
		{
			import( $classpath );
			$class = Seed::getClassname( $classpath );
		}

		$vars = get_class_vars( $class );

		$table = $this->getDB()->prefix().$vars['table'];
		$primary_key = $vars['primary_key'];
		
		if( $this->isNew() )
		{
			$object = new $class( $this->getDB(), NULL, NULL, NULL);
		}
		else
		{
			$object = new $class( $this->getDB(), NULL, NULL, &$this);
			$object->is_new = 0;
		}

		return $object;
	}

	public function getRewriteURL()
	{
		import("includes.date");
		$rewrite = "";

		$date = timestamp2datearray( time() );
		$rewrite = $date['cal']."/";

		return $rewrite .= substr( strtr( $this->get('seed_title'), " QWERTYUIOPASDFGHJKLZXCVBNM", "-qwertyuiopasdfghjklzxcvbnm"), 0, 25);
	}

	// SET //////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////

	public function setUser( User $usr )
	{
		if( $this->usr == NULL )
		{
			$this->usr = $usr;
			$this->set('user_id', $usr->getId());
		}
		else throw new SeedException("User already assigned");
	}

	public function setDate()
	{
		if( $this->isNew() )
		{
			$this->set('seed_date', time());
		}
	}

	public function setClasspath( $classpath )
	{
		$this->set('seed_classpath', $classpath);
	}

	// OVERRIDE ////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////

	public function store( $id=NULL )
	{
		if( $this->getUser() == NULL ) throw new SeedException("User not specified");

		$this->set('seed_modified', time());

		parent::store($id);
	}

	// STATIC FUNCTIONS ////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get the Seed from id
	 *
	 * @param int $seed_id
	 *
	 * @return Seed
	 */
	public static function getSeed( DB $db, $seed_id )
	{
		return DBEntity::getFromId( $db, Seed::TABLE, Seed::PRIMARY_KEY, $seed_id, 'Seed' );
	}

	public static function getClassname( $classpath )
	{
		//echo "<br />".$class_path;
		$exploded = explode( ".", $classpath, 10);

		$index = count( $exploded ) - 1;
		//var_dump($exploded);

		return $exploded[ $index ];
	}
}

/**
 *
 * @author mattevigo
 *
 */
class SeedException extends Exception
{

}
