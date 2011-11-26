<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @package core.site	
 * @version 1.0
 * 
 * 
 */
require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."config.php");

import('core.DBEntity');
import('core.Object');

class Model extends Object
{	
	var $view = NULL;
	var $title = '';
	var $entity = NULL;
	
	/**
	 * Create a new Model for data rappresentation on the page
	 * 
	 * @param string $title the title of this model (you can display it into your pages)
	 * @param Object $ent the DBEntity that may contain data (default is 'NULL')
	 */
	public function __construct($title='', Object $ent=NULL)
	{
		$this->title = $title;
		$this->entity = $ent;
	}
	
	public function setView($view)
	{
		$this->view = $view;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * Void function for display selected page with the template.
	 * 
	 * @deprecated
	 */
	public function display()
	{
		if($this->view != null)
		{
			if(!file_exists( LOCAL_VIEWS.DS.$this->view.".php" ))
			{
				echo LOCAL_VIEWS.DS.$this->view.".php";
				throw new ViewException("$this->view: view doesn't exists");
			}
			else
			{
				require_once LOCAL_VIEWS.DS.$this->view.".php";
			}
		}
	}
}

class ViewException extends Exception{}