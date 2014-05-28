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

class DSCTools
{
	/**
	 *
	 * @param $folder
	 * @return unknown_type
	 */
	public static function getPlugins( $folder='DSC' )
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
    			WHERE
    				`type` = 'plugin'
    			AND
    				LOWER(`folder`) = '{$folder}'
    			ORDER BY ordering ASC
    		";
        } else {
            // Joomla! 1.5 code here
    		$order_query = " ORDER BY ordering ASC ";
    		$folder = strtolower( $folder );

    		$query = "
    			SELECT
    				*
    			FROM
    				#__plugins
    			WHERE 1
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
	 *
	 * @param $element
	 * @param $eventName
	 * @return unknown_type
	 */
	public static function hasEvent( $element, $eventName, $group )
	{
		$success = false;
		if (!$element || !is_object($element)) {
			return $success;
		}

		if (!$eventName || !is_string($eventName)) {
			return $success;
		}
		
		// Check if they have a particular event
		$import 	= JPluginHelper::importPlugin( strtolower( $group ), $element->element );
		$dispatcher	= JDispatcher::getInstance();
		$result 	= JFactory::getApplication()->triggerEvent( $eventName, array( $element ) );
		if (in_array(true, $result, true)) {
			$success = true;
		}
		return $success;
	}

}