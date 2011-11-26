<?php
import('core.Feedable');

class AtomFeed
{
	const XML_TAG = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	const FEED_TAG = "<feed xmlns=\"http://www.w3.org/2005/Atom\">%s</feed>";
	
	var $tag_index = 0;
	var $tags = array();
	
	var $entry_index = 0;
	var $entries = array();
	
	/**
	 * Set generic tag in this atom
	 * 
	 * @param $tag_name
	 * @param $value
	 * @param $attr
	 * @return unknown_type
	 */
	private function setTag($tag_name, $value, array $attr=null)
	{
		$tag = "<$tag_name";
		
		if($attr != null)	/* adding attributes for this tag */
		{
			foreach($attr as $name => $value)
			{
				$tag .= " $name=\"$value\" ";
			}
		}
		
		if($value == null)
		{
			$tag .= " />";
		}
		else
		{
			$tag .= ">$value</$tag_name>";	
		}
		
		$this->tags[$this->tag_index] = $tag;
		$this->tag_index++;
	}
	
	public function setTitle($value, array $attr=null)
	{
		$this->setTag('title', $value, $attr);
	}
	
	public function setSubtitle($value, array $attr=null)
	{
		$this->setTag('subtitle', $value, $attr);
	}
	
	public function addLink(array $attr=null)
	{
		$this->setTag('link', null, $attr);
	}
	
	public function addId($value, array $attr=null)
	{
		$this->setTag('id', $value, $attr);
	}
	
	public function setAuthor($name, $email, $uri)
	{
		$tag = "<author><name>$name</name></author>";
		$this->tags[$tag_index] = $tag;
		$this->tag_index++;
	}
	
	public function setGenerator($value, array $attr=null)
	{
		$this->setTag('generator', $value, $attr);
	}
	
	public function setRights($value, array $attr=null)
	{
		$this->setTag('rights', $value, $attr);
	}
	
	public function setUpdated($timestamp, array $attr=null)
	{
		$value = date(DATE_ATOM, $timestamp);
		$this->setTag('updated', $value, $attr);
	}
	
	public function addEntry(Feedable $entry)
	{
		$this->entries[$this->entry_index] = $entry;
		$this->entry_index++;
	}
	
	public function getFeed()
	{
		$content = "";
		
		foreach($this->tags as $key => $value)
		{
			$content .= $value . "\n";
		}
		
		foreach($this->entries as $key => $value)
		{
			$content .= $value->getEntry() . "\n";
		}
		
		$xml = self::XML_TAG . "\n" . sprintf(self::FEED_TAG, $content);
		return $xml;
	}
}