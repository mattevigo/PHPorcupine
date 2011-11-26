<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package core.site	
 * @version 2.0
 * 
 * The Blogset contains an array of Blogpost filtered from month, tag and category
 */
require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."config.php");

import('core.Object');
import('core.DB');
import('core.DBEntity');
import('core.Seed');

class Set extends Object
{	
	var $db;
	var $set = NULL;
	
	// Database
	var $query = NULL;
	var $result = NULL;
	var $affected_rows = 0;
	var $current = 0;
	
	public function setQuery( $query )
	{
		$this->query = $query;
		
		$this->result = $this->db->query( $query );
		$this->affected_rows = mysql_affected_rows( $this->db->getLink() );
	}
	
	public function getNext()
	{
		//var_dump( $this->query );
		$values = mysql_fetch_assoc( $this->result );
		
		if( $values == NULL ) return NULL;
		
		$classpath = 'core.Object';
		
		try
		{
			$classpath = $values['seed_classpath'];
		}
		catch (Exception $e)
		{
			//...nothing to do!
		}
		
		$class = Seed::getClassname( $classpath );
		
		import( $classpath );
		$obj = new $class();
		
		foreach( $values as $key => $value )
		{
			$obj->set( $key, $value );
		}
		
		$this->current++;
		return $obj;
	}
	
	public function __construct( DB $db )
	{
		$this->db = $db;
	}
	
	/**
	 * 
	 */
	public function getSet()
	{
		return $this->set;
	}
	
	/**
	 * Retrive all blogpost ordered by date (DESC)
	 * 
	 * @param $from
	 * @param $limit
	 */
	public function setAll( $from=0, $limit=NULL )
	{
		$this->set = Blogpost::getArray( $this->db, 'seed_id DESC', 1, $limit, $from );
	}
}