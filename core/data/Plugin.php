<?php
/**
 * 
 * 
 * @author Matteo Vigoni <mattevigo@gmail.com>
 *
 */
interface Plugin
{	
	public function getSeedId();
	
	public function getSeed();
	
	public function setSeed( Seed $s );
}