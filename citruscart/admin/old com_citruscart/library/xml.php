<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/**
 * Class to translate PHP Array element into XML and vice versa.
 *
 * @author	  Daniele Rosario
 * @copyright GPL 3
 * @citruscart   index.php
 * @version   0.8
 */

class CitruscartArrayToXML {

	/**
	 * @var string - String to use as key for node attributes into array
	 */

	var $attr_arr_string = 'attributes';

	var $value_string = '@value';

	var $doc = null;

	/**
	 * The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
	 *
	 * @param  array $data
	 * @param  string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @param  SimpleXMLElement $xml - should only be used recursively
	 * @param  array $namespaces - the namespaces (like $namespace[] = array('url' => 'http://....', 'name' => 'xmlns:g')
	 */
	public function toXml($data, $rootNodeName = 'data', &$xml = null, $namespaces = null, $root_ns = null)
	{
		// First call: create document and root node
		if (is_null($xml))
		{
			$this->doc = new DOMDocument('1.0', 'utf8');

			// Root namespace
			if($root_ns)
			{
				$root = $this->doc->createElementNS($root_ns, $rootNodeName);
			}
			$xml = &$root;

			$this->doc->appendChild($root);

			// Namespaces
			foreach($namespaces as $ns)
			{
				$root->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:'.$ns['name'], $ns['url']);
			}
		}

		// loop though the array
		foreach($data as $key => $value)
		{
			// normal list of nodes
			if(is_numeric($key))
			{
				$key = $rootNodeName;
			}

			// Attributes support
			if($key == $this->attr_arr_string)
			{
        		// Add attributes to node
        		foreach($value as $attr_name => $attr_value)
        		{
        			$att = $this->doc->createAttribute($attr_name);
        			$xml->appendChild($att);
        			$att->appendChild($this->doc->createTextNode($attr_value));
        		}


			}
			else
			{
				// Add the value if there was a value together with the att
				if($key == $this->value_string)
				{
					// Add value to node
        			$xml->appendChild($this->doc->createTextNode($value));
				}
				else
				{

					// delete any char not allowed in XML element names
	        		$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

	        		// if there is another array found recrusively call this function
	        		if (is_array($value))
	        		{
	        			// create a new node unless this is an array of elements
	         			if($this->isAssoc($value))
	         			{
	         				$node = $this->doc->createElement($key);
	         				$xml->appendChild($node);
	         			}
	         			else
	         			{
	         				$node = $xml;
	         			}

				        // recrusive call - pass $key as the new rootNodeName
				        $this->toXml($value, $key, $node);
	        		}
	        		else
	        		{
	        			// Add a single value
	        			$value = htmlentities($value);
	          			$t = $this->doc->createElement($key);
	         			$xml->appendChild($t);

	         			$v = $this->doc->createTextNode($value);
	         			$t->appendChild($v);
	        		}
				}
			}

		}

		$this->doc->formatOutput = true;
		echo Citruscart::dump($this->doc->saveXML());
		return $this->doc->saveXML();

	}

  /**
   * The main function for converting to an array.
   * Pass in a XML document and this recrusively loops through and builds up an array.
   *
   * @static
   * @param  string $obj - XML document string (at start point)
   * @param  array  $arr - Array to generate
   * @return array - Array generated
   */
  public static function toArray( $obj, &$arr = NULL ) {
    if ( is_null( $arr ) )   $arr = array();
    if ( is_string( $obj ) ) $obj = new SimpleXMLElement( $obj );

    // Get attributes for current node and add to current array element
    $attributes = $obj->attributes();
    foreach ($attributes as $attrib => $value) {
      $arr[CitruscartArrayToXML::attr_arr_string][$attrib] = (string)$value;
    }

    $children = $obj->children();
    $executed = FALSE;
    // Check all children of node
    foreach ($children as $elementName => $node) {
      // Check if there are multiple node with the same key and generate a multiarray
      if($arr[$elementName] != NULL) {
        if($arr[$elementName][0] !== NULL) {
          $i = count($arr[$elementName]);
          CitruscartArrayToXML::toArray($node, $arr[$elementName][$i]);
        } else {
          $tmp = $arr[$elementName];
          $arr[$elementName] = array();
          $arr[$elementName][0] = $tmp;
          $i = count($arr[$elementName]);
          CitruscartArrayToXML::toArray($node, $arr[$elementName][$i]);
        }
      } else {
        $arr[$elementName] = array();
        CitruscartArrayToXML::toArray($node, $arr[$elementName]);
      }
      $executed = TRUE;
    }
    // Check if is already processed and if already contains attributes
    if(!$executed && $children->getName() == "" && !isset ($arr[CitruscartArrayToXML::attr_arr_string])) {
      $arr = (String)$obj;
    }
    return $arr;
  }

  /**
   * Determine if a variable is an associative array
   *
   * @static
   * @param  array $obj - variable to analyze
   * @return boolean - info about variable is associative array or not
   */
  private static function isAssoc( $array ) {
    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
  }
}