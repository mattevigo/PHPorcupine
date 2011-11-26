<?php
/**
 * 
 * 
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 */
interface Extension
{	
	public function isExtension();
	
	public function getParent();
	
	public function getParentId();
	
	public function getParentTable();
	
	public function getParentPrimaryKey();
	
	public function getParentClassName();
	
	public function setParent( DBEntity $parent );
}