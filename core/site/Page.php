<?php
/**
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @project PHPorcupine
 * @package core.site
 * @version 2.0
 *
 *
 */
require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."config.php");

import('core.data.DBEntity');
import('core.framework.Object');

class Page extends Object
{
	var $title = NULL;
	var $entity = NULL;
	var $template = NULL;
	var $model = NULL;
	
	var $redirect = NULL;

	var $header = 'header';
	var $content = NULL;
	var $sidebar = 'sidebar';
	var $footer = 'footer';

	var $atom = "<link href=\"%s\" type=\"application/atom+xml\" rel=\"alternate\" title=\"%s\" />";

	/**
	 * Create a new Page controller
	 * 
	 * @param string $title the title of this page
	 * @param Model $model the model containing data for this page
	 */
	public function __construct($title, Model $model=null)
	{
		$this->title = $title;
		$this->setModel( $model );
	}

	/**
	 * 
	 * @param Model $model
	 */
	public function setModel( Model $model )
	{
		$this->model = $model;
	}

	/**
	 * 
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 *
	 * @param $template_path
	 * @return unknown_type
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}
	
	/**
	 * Get the template name
	 * 
	 * @return the name of the template for this page or NULL if no template is set
	 */
	public function getTemplate()
	{
		return $this->template;
	}
	
	public function setRedirect($redirect)
	{
		$this->redirect = $redirect;
	}

	/**
	 * @deprecated
	 * @param unknown_type $content_view
	 */
	public function setContent($content_view)
	{
		$this->content = $content_view;
	}

	/**
	 * @deprecated
	 */
	public function getContent()
	{
		return $this->content;
	}

	public function setAtom($atom_address, $atom_title)
	{
		$this->atom = sprintf($this->atom, $atom_address, $atom_title);
	}

	/**
	 * Void function for display selected page with the template.
	 */
	public function display()
	{
		$template_path = TEMPLATES_DIR . "/" . $this->getTemplate() . "/template.php";

		if(!file_exists($template_path)) throw new FrameworkException("$template_path: template doesn't exist");

		require_once $template_path;
	}

}