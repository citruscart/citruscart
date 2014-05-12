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

class DSCModule extends JObject
{
	public static function renderModules( $position, $item_id, $options = array('style' => 'default') )
	{
		$html = '';
		$modules = self::loadModules( $position, $item_id );

		foreach ($modules as $module)
		{
			// foreach module, render
			$renderer = JFactory::getDocument()->loadRenderer('module');
			$html .= $renderer->render( $module, $options );
		}

		return $html;
	}

	public static function loadModules( $position, $Itemid )
	{
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_advancedmodules/advancedmodules.php')) {
			return self::loadModulesAMM($position, $Itemid);
		}

		return self::loadModulesCore($position, $Itemid);
	}

	public static function loadModulesAMM( $position, $Itemid )
	{
		static $return;

		if (!empty($return[$position][$Itemid]))
		{
			return $return[$position][$Itemid];
		}

		//$Itemid = JRequest::getInt('Itemid');
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		/*
		 $cache = JFactory::getCache('com_modules', '');
		$cacheid = md5(serialize(array($Itemid, $groups, $clientId, $lang)));

		if (!($clean = $cache->get($cacheid)))
		{
		*/
		$db = JFactory::getDbo();

		$query = new stdClass();
		$query->select = array();
		$query->from = array();
		$query->join = array();
		$query->where = array();
		$query->order = array();

		$query->select[] = 'm.published, m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid';
		$query->from[] = '#__modules AS m';
		$query->join[] = '#__modules_menu AS mm ON mm.moduleid = m.id';
		$query->where[] = 'm.published = 1';

		$query->join[] = '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id';
		$query->where[] = 'e.enabled = 1';

		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();
		$query->where[] = '(m.publish_up = ' . $db->q($nullDate) . ' OR m.publish_up <= ' . $db->q($now) . ')';
		$query->where[] = '(m.publish_down = ' . $db->q($nullDate) . ' OR m.publish_down >= ' . $db->q($now) . ')';

		$query->where[] = 'm.access IN ('.$groups.')';
		$query->where[] = 'm.client_id = ' . $clientId;
		$query->where[] = '(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)';
		$query->where[] = "m.position = '". $position ."'";

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where[] = 'm.language IN (' . $db->q($lang) . ',' . $db->q('*') . ')';
		}

		$query->order[] = 'm.position, m.ordering';

		// Do 3rd party stuff to change query
		$app->triggerEvent( 'onCreateModuleQuery', array( &$query ) );

		$q = $db->getQuery(true);
		// convert array object to query object
		foreach ( $query as $type => $strings )
		{
			foreach ( $strings as $string )
			{
				if ( $type == 'join' )
				{
					$q->{$type}( 'LEFT', $string );
				}
				else
				{
					$q->{$type}( $string );
				}
			}
		}

		// Set the query
		$db->setQuery($q);
		$modules = $db->loadObjectList();
		$clean = array();

		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
			return $clean;
		}

		// Apply negative selections and eliminate duplicates
		$negId = $Itemid ? -(int) $Itemid : false;
		$dupes = array();
		for ($i = 0, $n = count($modules); $i < $n; $i++)
		{
		$module = &$modules[$i];

		// The module is excluded if there is an explicit prohibition
		$negHit = ($negId === (int) $module->menuid);

		if (isset($dupes[$module->id]))
		{
		// If this item has been excluded, keep the duplicate flag set,
			// but remove any item from the cleaned array.
			if ($negHit)
			{
			unset($clean[$module->id]);
		}
		continue;
		}

		$dupes[$module->id] = true;

		// Only accept modules without explicit exclusions.
		if (!$negHit)
			{
			// Determine if this is a 1.0 style custom module (no mod_ prefix)
				// This should be eliminated when the class is refactored.
				// $module->user is deprecated.
				$file = $module->module;
				$custom = substr($file, 0, 4) == 'mod_' ?  0 : 1;
				$module->user = $custom;
				// 1.0 style custom module name is given by the title field, otherwise strip off "mod_"
				$module->name = $custom ? $module->module : substr($file, 4);
				$module->style = null;
				$module->position = strtolower($module->position);
				$clean[$module->id] = $module;
		}
		}

		unset($dupes);

		// Do 3rd party stuff to manipulate module array.
		// Any plugins using this architecture may make alterations to the referenced $modules array.
		// To remove items you can do unset($modules[n]) or $modules[n]->published = false.

		// "onPrepareModuleList" may alter or add $modules, and does not need to return anything.
		// This should be used for module addition/deletion that the user would expect to happen at an
		// early stage.
		$app->triggerEvent( 'onPrepareModuleList', array( &$clean ) );

		// "onAlterModuleList" may alter or add $modules, and does not need to return anything.
			$app->triggerEvent( 'onAlterModuleList', array( &$clean ) );

		// "onPostProcessModuleList" allows a plugin to perform actions like parameter changes
			// on the completed list of modules and is guaranteed to occur *after*
		// the earlier plugins.
		$app->triggerEvent( 'onPostProcessModuleList', array( &$clean ) );

		// Remove any that were marked as disabled during the preceding steps
		foreach ( $clean as $id => $module )
			{
					if ( !isset( $module->published ) || $module->published == 0 )
					{
					unset( $clean[$id] );
					}
		}


		// Return to simple indexing that matches the query order.
		$clean = array_values($clean);

		/*
		$cache->store($clean, $cacheid);
		}
		*/

		$return[$position][$Itemid] = $clean;

		return $return[$position][$Itemid];
	}

	public static function loadModulesCore( $position, $Itemid )
	{
		$modules = array();

		$db	= JFactory::getDbo();
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$groups		= implode(',', $user->getAuthorisedViewLevels());
		$lang 		= JFactory::getLanguage()->getTag();
		$clientId 	= (int) $app->getClientId();

		$query = $db->getQuery(true);
		$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid');
		$query->from('#__modules AS m');
		$query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id');
		$query->where('m.published = 1');

		$query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
		$query->where('e.enabled = 1');

		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
		$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');

		$query->where('m.access IN ('.$groups.')');
		$query->where('m.client_id = '. $clientId);
		$query->where('(mm.menuid = '. (int) $Itemid .' OR mm.menuid <= 0)');
		$query->where("m.position = '". $position ."'");

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter()) {
			$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
		}

		$query->order('m.position, m.ordering');

		// Set the query
		$db->setQuery($query);
		$modules = $db->loadObjectList();
		$clean	= array();

		if ($db->getErrorNum()){
			JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
			return $clean;
		}

		// Apply negative selections and eliminate duplicates
		$negId	= $Itemid ? -(int)$Itemid : false;
		$dupes	= array();
		for ($i = 0, $n = count($modules); $i < $n; $i++)
		{
		$module = &$modules[$i];

		// The module is excluded if there is an explicit prohibition or if
		// the Itemid is missing or zero and the module is in exclude mode.
		$negHit	= ($negId === (int) $module->menuid)
		|| (!$negId && (int)$module->menuid < 0);

		if (isset($dupes[$module->id])) {
		// If this item has been excluded, keep the duplicate flag set,
		// but remove any item from the cleaned array.
			if ($negHit) {
			unset($clean[$module->id]);
		}
		continue;
		}

		$dupes[$module->id] = true;

		// Only accept modules without explicit exclusions.
		if (!$negHit) {
			//determine if this is a custom module
			$file				= $module->module;
			$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
			$module->user		= $custom;
			// Custom module name is given by the title field, otherwise strip off "mod_"
			$module->name		= $custom ? $module->title : substr($file, 4);
				$module->style		= null;
				$module->position	= strtolower($module->position);
				$clean[$module->id]	= $module;
			}
			}

			unset($dupes);

			// Return to simple indexing that matches the query order.
			$modules = array_values($clean);

			return $modules;
		}
}