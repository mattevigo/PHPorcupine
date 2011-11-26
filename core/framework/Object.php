<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package PHPorcupine	
 * @version 1.0
 * 
 * Class Object
 */
class Object
{	
	/**
	 * Costruttore 
	 * 
	 * @param $db
	 * @param $id
	 * @return unknown_type
	 */
	public function __construct(){}
	
	public function __destruct(){}
	
	/**
	 * Set a variable in this Object
	 * 
	 * @param $index
	 * @param $value
	 */
	public function set($index, $value)
	{
		$this->$index = $value;
	}
	
	/**
	 * Get a variable in this Object
	 * 
	 * @param $index
	 * @return the var value if var is set, otherwise null
	 */
	public function get($index)
	{
		if(isset($this->$index))
			return $this->$index;
		return null;
	}
	
	/**
	 * Insert this object into an html file
	 * 
	 * @param string $html_path the pathname of the php file
	 */
	public function insertIntoHTML( $html_path )
	{
		require $html_path;
	}

}

class NotImplementedException extends Exception
{
	
}