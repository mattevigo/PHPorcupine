<?php
/**		
 * Wrapper class for MySQL features
 * 
 * @author Matteo Vigoni <mattevigo@gmail.com>																		
 * @package DBEntity
 * @version 0.2																									 
 */
class DB
{
	
	// variabili di ambiente
	private $dbms;
	private $host;
	private $port;
	private $name;	# nome del database
	private $user;
	private $pw;
	public $prefix;
	private $connection = NULL;
	
	// nomi tabelle
	public $t_users = "users";
	public $t_sessions = "sessions";
	
	// sessione
	public $session_time = 0;
	
	/**
	 * Construtor
	 *
	 * @param array $db_config the database configuration array 
	 * @param string $session_time the length of the session in seconds, this parameter may be omit
	 * 	for unlimited session or if session is not need
	 * @see the documentation for the configuration file
	 * @link config.php
	 */
	function __construct($db_config, $session_time=0)
	{
		$this->dbms = $db_config['dbms'];
		$this->host = $db_config['host'];
		$this->port = $db_config['port'];
		$this->name = $db_config['name'];
		$this->user = $db_config['user'];
		$this->pw = $db_config['pw'];
		$this->prefix = $db_config['prefix'];
		
		$this->t_users = $this->prefix . $db_config['t_users'];
		$this->t_sessions = $this->prefix . $db_config['t_sessions'];
		
		$this->session_time = $session_time;
	}
	
	/**
	 * Database name for this wrapper
	 *
	 * @return string rappresentation for the name of this database
	 */
	public function name()
	{
		return $this->name;
	}
	
	/**
	 * Prefix of the tables for this database
	 * 
	 * @return string rappresentation of the prefix for each table of this database
	 */
	public function prefix()
	{
		if(isset($this->prefix))
			return $this->prefix;
		else return "";
	}
	
	/**
	 * Send a query to the dbms
	 *
	 * @param string $mysql_query the query to send
	 * 
	 * @return resource result of this query
	 * @throws DBException if errors occurred
	 */
	function query($mysql_query)
	{
		$this->connect();
		mysql_select_db($this->name);
		$result = mysql_query( ereg_replace( '#__', $this->prefix(), $mysql_query ) );
		
	//	echo ereg_replace( '#_', $this->prefix(), $mysql_query )."<br />\n"; # debug
		
		if(!$result) throw new DBException(mysql_error());
		return $result;
	}
	
	/**
	 * Execute an SQL script from a file
	 * 
	 * @param $file_path
	 * @return unknown_type
	 */
	function script( $filename )
	{
		if( file_exists( $filename ) )
		{
			$script = file_get_contents( $filename );
			$queries = explode( ";", $script );
			
			foreach ( $queries as $k => $q )
			{
				echo $q . "<br /><br />";
				
				try
				{
					$this->query( $q );
				}
				catch (DBException $e ){}
			}
		}
	}
	
	/**
	 * Connect this wrapper to the database
	 * 
	 * @throws DBException if errors occurred
	 */
	function connect()
	{
		$this->connection = mysql_connect($this->host, $this->user, $this->pw);
		
		if(!$this->connection) throw new DBException(mysql_error());
	}
	
	/**
	 * Close the connection for this database
	 * 
	 */
	function disconnect()
	{
		if($this->connection = null)
			return;
		mysql_close($this->connection);
	}
	
	/**
	 * Socket's link
	 *
	 * @return resorce link of the mysql connection
	 */
	function getLink()
	{
		return $this->connection;
	}
	
	/**
	 * @todo chiusura della connessione
	 */
	function __destruct(){}
	
	public function toString()
	{
		return "DB - $this->name";
	}
}

class DBException extends Exception{}
?>