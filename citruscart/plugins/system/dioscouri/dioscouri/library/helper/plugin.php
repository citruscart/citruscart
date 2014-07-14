<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class DSCHelperPlugin extends DSCHelper
{
	/**
	 * Only returns plugins that have a specific event
	 *
	 * @param $eventName
	 * @param $folder
	 * @return array of JTable objects
	 */
	function getPluginsWithEvent( $eventName, $folder='DSC' )
	{
		$return = array();
		if ($plugins = DSCHelperPlugin::getPlugins( $folder ))
		{
			foreach ($plugins as $plugin)
			{
				if (DSCHelperPlugin::hasEvent( $plugin, $eventName ))
				{
					$return[] = $plugin;
				}
			}
		}
		return $return;
	}

	/**
	 * Returns Array of active Plugins
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getPlugins( $folder='DSC' )
	{
		$database = JFactory::getDBO();

		$order_query = " ORDER BY ordering ASC ";
		$folder = strtolower( $folder );

		if(version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
	      $query = "
			SELECT
				*
			FROM
				#__extensions
			WHERE  enabled = '1'
			AND
				LOWER(`folder`) = '{$folder}'

		";
	    } else {
	        // Joomla! 1.5 code here
	      $query = "
			SELECT
				*
			FROM
				#__plugins
			WHERE  published = '1'
			AND
				LOWER(`folder`) = '{$folder}'
			{$order_query}
		";
		}

		$database->setQuery( $query );
		$data = $database->loadObjectList();
		return $data;
	}

	/**
	 * Returns HTML
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function &getPluginsContent( $event, $options, $method='vertical' )
	{
		$text = "";
        jimport('joomla.html.pane');

		if (!$event) {
			return $text;
		}

		$args = array();
		$dispatcher	   = JDispatcher::getInstance();
		$results = JFactory::getApplication()->triggerEvent( $event, $options );

		if ( !count($results) > 0 ) {
			return $text;
		}

		// grab content
		switch( strtolower($method) ) {
			case "vertical":
				for ($i=0; $i<count($results); $i++) {
					$result = $results[$i];
					$title = $result[1] ? JText::_( $result[1] ) : JText::_( 'Info' );
					$content = $result[0];

		            // Vertical
		            $text .= '<p>'.$content.'</p>';
				}
			  break;
			case "tabs":
			  break;
		}

		return $text;
	}

	/**
	 * Checks if a plugin has an event
	 *
	 * @param obj      $element    the plugin JTable object
	 * @param string   $eventName  the name of the event to test for
	 * @return unknown_type
	 */
	function hasEvent( $element, $eventName )
	{
		$success = false;
	   if (!$element || !is_object($element)) {
			return $success;
		}

		if (!$eventName || !is_string($eventName)) {
			return $success;
		}

		// Check if they have a particular event
		$import 	= JPluginHelper::importPlugin( strtolower('DSC'), $element->element );
		$dispatcher	= JDispatcher::getInstance();
		$result 	= JFactory::getApplication()->triggerEvent( $eventName, array( $element ) );
		if (in_array(true, $result, true))
		{
			$success = true;
		}
		return $success;
	}


}

