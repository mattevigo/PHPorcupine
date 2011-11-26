<?php
require_once( $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'config.php' );
import("core.framework.Object");

/**
 * EXPERIMENTAL
 *
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package DBEntity
 *
 * @version 2.0
 *
 * This class is the core of the Database Abstraction Layer.
 * An istance of DBEntity rappresent a record of a generic table on the DB. You can create
 * an object with DBEntity and save it in the database, errors will be processed from the
 * DBMS, it's very important know what you are doing and the struct of the DB.
 *
 * The best way to use DBEntity is extend it for made object with your own functionality,
 * take a look at the Session and User class in the core directory for an example.
 *
 */
class DBEntity extends Object
{
	/**
	 * @var $primary_key field name for primary key
	 * @var $id primary key value
	 *
	 */
	private $id = NULL;

	// information structure
	private $foreign_keys = array();

	//public $values = Array();	#deprecated
	private $changes = array();

	public $is_new = true;
	public $nelem = 0;

	public $db_wrapper;
	public $table = NULL;	# the name of the table that this DBEntity rappresent
	public $primary_key;

	// commit
	private $commit_query_update = "UPDATE ";
	private $commit_query_set = "SET ";
	private $commit_query_where = "WHERE ";

	// parent
	var $parent = NULL;
	var $parent_table = NULL;
	var $parent_primary_key = NULL;
	var $parent_class = NULL;

	/**
	 * Constructor
	 *
	 * @param DB $db_wrapper the DB object that rappresent this database
	 * @param string $table the table name for this entity
	 * @param string $primary_key the field name of the primary key
	 *
	 * @throws DBException for problem with DBMS
	 *
	 * @todo implementazione di chiavi primarie multiple da realizzare con due array $pk e $kv che contengono
	 * 			i nomi e i valori delle chiavi primarie.
	 */
	function __construct(DB $db_wrapper=NULL, $table, $primary_key, $id=null)
	{
		if( $db_wrapper != NULL )
		{
			$this->db_wrapper = $db_wrapper;
			$this->table = $table;
			$this->primary_key = $primary_key;
	
			if(isset($id))
			{
				$this->id = $id;
	
				$mysql_query = 	"SELECT * " .
					"FROM $table " .
					"WHERE $primary_key=";
	
				if( is_string( $this->id ) )
				{
					$mysql_query .= "'$id'";
				}
				else
				{
					$mysql_query .= $id;
				}
	
				$result = $this->db_wrapper->query($mysql_query);
				$data_array = mysql_fetch_assoc($result);
	
				if(!$data_array) throw new EntityException("The '".$id."' doesn't exist in ".$db_wrapper->name().".".$table);
	
				//var_dump($data_array); # debug
				foreach($data_array as $key => $value)
				{
					$this->set($key, $value, false);
				}
	
				$this->is_new = false;
			}
		}
		else
		{
			$this->is_new = false;
		}
	}

	/**
	 * Default destructur
	 */
	public function __destruct()
	{
		parent::__destruct();
	}

	/**
	 *
	 * @return DB
	 */
	public function getDB()
	{
		return $this->db_wrapper;
	}

	public function setDB( DB $db )
	{
		$this->db_wrapper = $db;
	}

	/**
	 *
	 * @return string the table's name of this entity
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 *
	 */
	public function getPrimaryKey()
	{
		return $this->primary_key;
	}

	/**
	 * Get the primary key value for this DBEntity
	 *
	 * @return string the id for this DBEntity
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the XML rappresentation for this DBEntity
	 *
	 * @return string XML rappresentation for this DBEntity
	 */
	public function getXML()
	{
		$xml = "<$this->table>";

		foreach($this->changes as $key => $value){
			$xml .= "<$key>{$this->get($key)}</$key>";
		}

		$xml .= "</$this->table>";

		return $xml;
	}

	/**
	 * Get the JSON rappresentation for this DBEntity
	 *
	 * @return string JSON rappresentation for this DBEntity
	 */
	public function getJSON()
	{
		$json = "{\n";
		$i = 0;

		foreach($this->changes as $key => $value)
		{
			$json .= "\t\"$key\":\"{$this->get($key)}\"";
			$i++;
			if($i<$this->nelem) $json.= ",\n";
		}

		return $json."\n}";
	}

	/**
	 * Fetch the field values into an associative array
	 *
	 * @return associative array
	 */
	public function fetchAssoc()
	{
		$array = array();

		foreach( $this->changes as $key => $v )
		{
			$array[$key] = $this->$key;
		}

		return $array;
	}

	/**
	 *
	 * @param $array
	 * @return unknown_type
	 */
	public function addAssoc( $array )
	{
		// integrity check
		foreach( $array as $key => $value )
		{
			if( isset($this->$key) )
			{
				if( is_int( $this->$key ) && $this->$key != $value )
				throw new DBEntityException( "The fields don't' have the same value" );
					
				if( is_string( $this->$key ) && strcmp($this->$key, $value) != 0 )
				throw new DBEntityException( "The fields don't' have the same value" );
			}
		}

		// if passed set the value
		foreach( $array as $key => $value )
		{
			//echo "$key\n";
			$this->set( $key, $value, false, false );
		}
	}

	/**
	 * Get the value for the generic $index field name
	 *
	 * @param string $index the field name
	 */
	public function get($index)
	{
		if(!isset($this->$index))
		throw new EntityException("No value found for '".$index."'");

		return $this->$index;
	}

	/**
	 * Set/Modify a record value. This modify chenge only the object and not the datbase value that
	 * the object rappresent. To apply change to the database too you need to execute $this->commit()
	 *
	 * @param string $index the field name
	 * @param string $value the field values
	 * @param bool update if is true (default) it will update the changes array
	 * @param bool add if is true (default) value will added to the field list
	 */
	public function set($index, $value, $update=true, $add=true)
	{
		$this->$index = $value;

		if( $add )
		{
			if( $update )
			{
				$this->changes[$index] = true;
				$this->nelem++;
			}
			else
			{
				$this->changes[$index] = false;
			}
		}
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Check if DBEntity is new (not stored yet in the DB)
	 * @return unknown_type
	 */
	public function isNew()
	{
		return $this->is_new;
	}

	/**
	 *
	 * @param $table_name the name of the table that contains the foreign attributes
	 * @param $foreign_key the primary key of the same table (foreign key)
	 * @return unknown_type
	 */
	public function setForeignEntity( $table_name, $foreign_key )
	{
		if( !array_key_exists( $table_name, &$this->foreign_keys) )
		{
			$this->foreign_keys[$table->name] = $foreign_key;
		}
	}

	/**
	 *
	 * @param $table_name
	 * @param $filter
	 * @return unknown_type
	 */
	public function getForeignEntities( $table_name, $filter=1, $class='DBEntity' )
	{
		$array = array();

		$sql = "SELECT {$this->foreign_keys[$table_name]} FROM $table_name WHERE {$this->getPrimaryKey()}={$this->getId()} AND $filter";

		$result = $this->getDB()->query($sql);

		while( $elem = mysql_fetch_assoc( $result ) )
		{
			$array[$elem[$this->foreign_keys[$table_name]]] = new DBEntity( $this->getDB(), $table_name, $this->foreign_keys[$table_name] );
		}

		return $array;
	}

	/**
	 * Apply all the changes to the backened database.
	 */
	public function commit()
	{
		$toSet = $this->commit_query_set;
		$first = TRUE; # variabile usata per posizionare le virgole

		foreach($this->changes as $key => $val){ # iterazione all'interno dell'array

			if($val){

				if($first) {					#
					$first = FALSE;				# Corretto posizionamento della virgola
				} else $toSet = $toSet . ", ";	#

				# viene aggiornato solo il valore cambiato
				$toSet = $toSet . "`" . $key . "`='" . $this->$key . "'";
			}

		}

		$toSet = $toSet . " ";
		//echo $toSet . "<br />";	# stampa di verifica

		$query = $this->commit_query_update . $this->table . " "
		. $toSet
		. $this->commit_query_where . "`{$this->table}`.`{$this->primary_key}`='" . $this->getId()
		. "' LIMIT 1";

		//echo $query . "<br />";	# stampa di verifica
		try{
			$this->db_wrapper->query($query);
		} catch (DBException $e){
			throw new EntityException($e->getMessage());
		}

		$this->reset();
	}

	/**
	 * Insert a new record on the database table that this class rappresent.
	 *
	 * @param string $id the primary key value, if this is null will assigned one from the DB, if AUTO_INCREMENT
	 * 			is present.
	 *
	 * @trow EntityException se la chiave primaria e' gie' definita oppure se e' occorso un errore
	 * 							durante la scrittura nel database.
	 */
	public function store($id=null)
	{
		if( !$this->isNew() )
		{
			$this->commit();
			return;
		}

		$mysql_query_insert = "INSERT INTO `{$this->db_wrapper->name()}`.`$this->table` (";
		$mysql_query_values = ")VALUES (";

		//echo "sid: ".$id . "<br />";

		$mysql_query_insert .= $this->primary_key . ", ";
		if(is_null($id)) $mysql_query_values .= "NULL, ";
		else {
			if(is_string($id)) $mysql_query_values .= "'$id', ";
			else $mysql_query_values .= "$id, ";
		}

		$i = 1;
		foreach($this->changes as $key => $value){

			$mysql_query_insert .= $key;

			if(is_string($this->$key)) $mysql_query_values .= "'{$this->get($key)}'";
			else $mysql_query_values .= $this->get($key);

			if($i < $this->nelem){
				$mysql_query_insert .= ", ";
				$mysql_query_values .= ", ";
			}
			$i++;
		}

		$mysql_query = $mysql_query_insert . $mysql_query_values . ");";
		//echo $mysql_query;

		try{
			//scrivo i cambiamenti sul database
			$result = $this->db_wrapper->query($mysql_query);
			if(is_null($id)) $this->id = mysql_insert_id();
			else $this->id = $id;
			//echo "ID ".$this->id."<br />";

		} catch (DBException $e){
			throw new EntityException($e->getMessage());
		}

		//resetto tutti i valori ausiliari
		$this->reset();
	}

	/**
	 * Delete this record from database
	 *
	 * @throws DBException
	 */
	public function cancel()
	{
		$mysql_query = "DELETE FROM `$this->table` WHERE `$this->table`.`$this->primary_key` = $this->id LIMIT 1";
		$this->db_wrapper->query($mysql_query);
	}

	// Extension //////////////////////////////////////////////////////////////////////////

	public function getParent()
	{
		if( $this->parent != NULL )
		{
			return $this->parent;
		}
		else
		{
			$parent_id = $this->getParentId();

			if( $parent_id < 0 )
			{
				return NULL;
			}
			else
			{
				$class = $this->getParentClassName();
				return new $class( $this->getDB(), NULL, NULL, $parent_id );
			}
		}
	}

	public function getParentId()
	{
		if( $this->parent == NULL )
		{
			$sql = "SELECT {$this->getParentPrimaryKey()} ".
					"FROM {$this->getParentTable()} ".
					"WHERE {$this->getPrimaryKey()}={$this->getId()} LIMIT 0,1";

			$result = $this->getDB()->query( $sql );
			$row = mysql_fetch_assoc( $result );

			if( !$result )
			{
				return -1;
			}
			else
			{
				return $row[$this->getParentPrimaryKey()];
			}
		}
	}

	public function getParentTable()
	{
		return $this->getDB()->prefix() . $this->parent_table;
	}

	public function getParentPrimaryKey()
	{
		return $this->parent_primary_key;
	}

	public function getParentClassName()
	{
		return $this->parent_class;
	}

	public function setParentTable( $table )
	{
		$this->parent_table = $table;
	}
	
	public function setParentPrimaryKey( $primary_key )
	{
		$this->parent_primary_key = $primary_key;
	}
	
	public function setParentClassName( $class_name )
	{
		$this->parent_class = $class_name;
	}
	
	public function setParent( DBEntity $parent )
	{
		$this->parent = $parent;

		$this->setParentClassName( get_class( $parent ) );
		$this->setParentTable( $parent->getDB()->prefix().$parent->getTable() );
		$this->setParentPrimaryKey( $parent->getPrimaryKey() );

		$this->addAssoc( $parent->fetchAssoc() );
	}

	// Actions ///////////////////////////////////////////////////////////////////////////

	/**
	 *
	 * @return unknown_type
	 */
	public function doPost( User $user=NULL, Session $session=NULL )
	{
		throw new NotImplementedException("function DBEntity::doPost() not implemented");
	}

	/**
	 *
	 * @return unknown_type
	 */
	public function doGet( User $user=NULL, Session $session=NULL )
	{
		throw new NotImplementedException("function DBEntity::doGet() not implemented");
	}

	/**
	 *
	 * @return unknown_type
	 */
	public function doDelete( User $user=NULL, Session $session=NULL )
	{
		throw new NotImplementedException("function DBEntity::doDelete() not implemented");
	}

	//////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Reset all the aux vars
	 */
	private function reset()
	{
		$this->commit_query_set = "SET ";
		$this->commit_query_update = "UPDATE ";
		$this->commit_query_where = "WHERE ";

		foreach ( $this->changes as $key => $value ) {
			$this->changes[$key] = false;
		}

		$this->nelem = 0;
	}

	/**
	 * Count the number of record on this table
	 *
	 * @param DB $db_wrapper the DB class for this database
	 * @param string $table_name
	 *
	 * @return the number of record in $table_name
	 */
	public static function count(DB $db_wrapper, $table_name)
	{
		$mysql_query = "SELECT count(*) FROM ".$table_name." WHERE 1";
		$data = mysql_fetch_row($db_wrapper->query($mysql_query));

		return $data[0];
	}

	/**
	 * Return the DBEntity that rappresent the record identified from the value of $id
	 * of its $primary_key
	 *
	 * @param DB $db_wrapper the DB class for this database
	 * @param string $table_name the name of the table
	 * @param string $primary_key the field name of the primary key
	 * @param string $id the primary key value
	 *
	 * @return the DBEntity that rappresent the record
	 */
	public static function getFromId(DB $db, $table_name=null, $primary_key=null, $id, $class="DBEntity")
	{
		//import("");
		return new $class($db, $table_name, $primary_key, $id);
	}

	/**
	 * Return the XML rappresentation of the record identified from the value of $id of its
	 * $primary_key.
	 *
	 * @param DB $db_wrapper the DB class for this database
	 * @param string $table_name the name of the table
	 * @param string $primary_key the field name of the primary key
	 * @param string $id the primary key value
	 *
	 * @return a string that contains the XML rappresentation of the record
	 */
	public static function getXMLFromId(DB $db_wrapper, $table_name, $prymary_key ,$id)
	{
		$mysql_query = 	"SELECT * " .
			"FROM $table_name " .
			"WHERE $primary_key=$id ";

		$xml = "<$table_name>";

		try{
			$data = mysql_fetch_assoc($db_wrapper->query($mysql_query));

			foreach($data as $key => $value){
				$xml .= "<$key>$value</$key>";
			}
		}catch (DBException $e){
			throw new EntityException($e->getMessage());
		}

		$xml .= "</$table_name>";

		return $xml;
	}

	/**
	 * Return the JSON rappresentation of the record identified from the value of $id of its
	 * $primary_key.
	 *
	 * @param DB $db_wrapper the DB class for this database
	 * @param string $table_name the name of the table
	 * @param string $primary_key the field name of the primary key
	 * @param string $id the primary key value
	 *
	 * @return a string that contains the JSON rappresentation of the record
	 */
	public static function getJSONFromId(DB $db_wrapper, $table_name, $primary_key, $id)
	{
		$mysql_query = 	"SELECT * " .
			"FROM $table_name " .
			"WHERE $primary_key=$id ";

		$json = "{\n";

		try{
			$data = mysql_fetch_assoc($db_wrapper->query($mysql_query));

			$nelem = count($data);
			$i = 0;

			foreach($data as $key => $value){
				$json .= "\t\"$key\":\"$value\"";
				$i++;
				if($i<$nelem) $json.= ",\n";
			}
		}catch (DBException $e){
			throw new EntityException($e->getMessage());
		}

		$json .= "\n}";

		return $json;
	}

	/**
	 * EXPERIMENTAL
	 *
	 * Get all Entity of a target table as an array of DBEntity filtered
	 *
	 * @param $db
	 * @param $table
	 * @param $primary_key
	 * @param $classname
	 * @param $orderby
	 * @param $filter
	 * @param $limit
	 * @return unknown_type
	 */
	public static function getArray($db, $table, $primary_key, $classname='DBEntity', $orderby=null, $filter=null, $limit=null)
	{
		$array = NULL;

		$mysql = "SELECT $primary_key FROM $table";

		if($filter != null)
		$mysql .= " WHERE $filter ";
			
		if($orderby != null)
		$mysql .= " ORDER BY $orderby ";
			
		if($limit != null)
		$mysql .= " LIMIT $limit";

		//echo $mysql;
		$result = $db->query($mysql);

		while( $obj = mysql_fetch_assoc($result) )
		{
			if ( $array == NULL )
			$array = array();

			$array[$obj[$primary_key]] = new $classname($db, $table, $primary_key, $obj[$primary_key]);
		}

		return $array;
	}
}

/**
 *
 * @author mattevigo
 *
 */
class EntityException extends Exception
{

}

/**
 *
 * @author mattevigo
 *
 */
class DBEntityException extends Exception
{

}

/**
 *
 * @author mattevigo
 *
 */
class InvalidInputException extends Exception
{

}
?>
