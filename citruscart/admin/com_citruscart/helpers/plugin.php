<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/library/dioscouri/library/parameter.php');

class CitruscartHelperPlugin extends CitruscartHelperBase
{
	/**
	 * Only returns plugins that have a specific event
	 * 
	 * @param $eventName
	 * @param $folder
	 * @return array of JTable objects
	 */
	public static function getPluginsWithEvent( $eventName, $folder='Citruscart' )
	{
		$return = array();
		if ($plugins = CitruscartHelperPlugin::getPlugins( $folder ))
		{
			foreach ($plugins as $plugin)
			{
				if (CitruscartHelperPlugin::hasEvent( $plugin, $eventName ))
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
	static function getPlugins( $folder='Citruscart' )
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
	function getPluginsContent( $event, $options, $method='vertical' ) 
	{
		$text = "";
        jimport('joomla.html.pane');
		
		if (!$event) {
			return $text;
		}
		
		$args = array();
		$dispatcher	 = JDispatcher::getInstance();
		$results = JFactory::getApplication()->triggerEvent( $event, $options );
		
		if ( !count($results) > 0 ) {
			return $text;
		}
		
		// grab content
		switch( strtolower($method) ) {
			case "vertical":
				for ($i=0; $i<count($results); $i++) {
					$result = $results[$i];
					$title = $result[1] ? JText::_( $result[1] ) : JText::_('Info');
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
	public static function hasEvent( $element, $eventName )
	{
		$success = false;
	   if (!$element || !is_object($element)) {
			return $success;
		}
		
		if (!$eventName || !is_string($eventName)) {
			return $success;
		}
		
		// Check if they have a particular event
		$import 	= JPluginHelper::importPlugin( strtolower('Citruscart'), $element->element );
		$dispatcher	= JDispatcher::getInstance();
		$result 	= JFactory::getApplication()->triggerEvent( $eventName, array( $element ) );
		if (in_array(true, $result, true)) 
		{
			$success = true;
		}		
		return $success;	
	}	

	/**
	 * Method to get the suffix  based on the geozonetype
	 * @param $geozonetype_id
	 * @return string
	 */
	public static function getSuffix($geozonetype_id)
	{
		switch($geozonetype_id)
		{
			case '2':
				$suffix = 'shipping';
				break;
			case '1':				
			default:
				$suffix = 'payment';
				break;
		}
		
		return $suffix;
	}	
	
	/**
	 * Method to count the number of plugin assigned to a geozone
	 * @param obj $geozone 
	 * @return int
	 */
	public static function countPlgtoGeozone($geozone)
	{		
		$count = 0;	
		if(!is_object($geozone)) return $count;
		
		static $plugins;
		static $geozones;

		if(empty($plugins[$geozone->geozonetype_id]))
		{
			$suffix = CitruscartHelperPlugin::getSuffix($geozone->geozonetype_id);
			JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
			$model = JModelLegacy::getInstance( $suffix, 'CitruscartModel' );
			$model->setState('filter_enabled', '1');
			$plugins[$geozone->geozonetype_id] = $model->getList( );
		}
			
		foreach( $plugins[$geozone->geozonetype_id] as $plugin)
		{
			if(isset($plugin->params))
			{
				if(empty($geozones[$plugin->id]))
				{
					$params = new DSCParameter($plugin->params);           
        			$geozones[$plugin->id] = explode(',',$params->get('geozones')); 
				}				
        		
        		if(in_array($geozone->geozone_id, $geozones[$plugin->id])) $count++;        		
			}
		}
		
		return $count;
	}
}
