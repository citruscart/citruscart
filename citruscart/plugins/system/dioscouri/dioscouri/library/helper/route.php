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
require_once(JPATH_SITE .'/libraries/dioscouri/library/helper.php');
class DSCHelperRoute extends DSCHelper
{
    static $itemids = null;

    /**
     *
     */
    public static function getItems( $option='' )
    {
        static $items;

        if (empty($option)) {
            $com = DSC::getApp();
            $option = 'com_' . $com->getName();
        }

        $app = JApplication::getInstance('site');
        $menus = $app->getMenu();
        if (empty($menus))
        {
            return array();
        }

        if (empty($items))
        {
            $items = array();
        }

        if (empty($items[$option]))
        {
            $component  = JComponentHelper::getComponent($option);
            foreach ($menus->getItems('component', $option) as $item)
            {
                if ( !is_object($item) )
                {
                    continue;
                }

                if ($item->component_id == $component->id || $item->componentid == $component->id || (!empty($item->query['option']) && $item->query['option'] == $option) )
                {
                    $items[$option][] = $item;
                }
            }
        }

        if (empty($items[$option])) return array();
           return $items[$option];

       }

    /**
     * Finds the itemid for the set of variables provided in $needles
     *
     * @param array $needles
     * @return unknown_type
     */
    public static function findItemid($needles=array('view'=>'products', 'task'=>'', 'filter_category'=>'', 'id'=>''))
    {
        // populate the array of menu items for the extension
        if (empty(self::$itemids))
        {
            self::$itemids = array();

            // method=upgrade KILLS all of the useful properties in the __menus table,
            // so we need to do this manually
            // $menus      = &JApplication::getMenu('site', array());
            // $component  = &JComponentHelper::getComponent('com_sample');
            // $items      = $menus->getItems('componentid', $component->id);
            $items = self::getItems();

            if (empty( $items ))
            {
                return null;
            }

            foreach ($items as $item)
            {
                if (!empty($item->query) && !empty($item->query['view']))
                {
                    // reconstruct each url query, in case admin has created custom URLs
                    $query = "";

                    $view = $item->query['view'];
                    $query .= "&view=$view";

                    if (!empty($item->query['task']))
                    {
                        $task = $item->query['task'];
                        $query .= "&task=$task";
                    }

                    if (!empty($item->query['id']))
                    {
                        $id = $item->query['id'];
                        $query .= "&id=$id";
                    }

                    // set the itemid in the cache array
                    if (empty(self::$itemids[$query]))
                    {
                        self::$itemids[$query] = $item->id;
                    }
                }
            }
        }

        // Make this search the array of self::$itemids, matching with the properties of the $needles array
        // return null if nothing found

        // reconstruct query based on needle
        $query = "";

        if (!empty($needles['view']))
        {
            $view = $needles['view'];
            $query .= "&view=$view";
        }

        if (!empty($needles['task']))
        {
            $task = $needles['task'];
            $query .= "&task=$task";
        }

        if (!empty($needles['id']))
        {
            $id = $needles['id'];
            $query .= "&id=$id";
        }

        // if the query exists in the itemid cache, return it
        if (!empty(self::$itemids[$query]))
        {
            return self::$itemids[$query];
        }

        return null;
    }

    /**
     * Build the route
     *
     * @param   array   An array of URL arguments
     * @return  array   The URL arguments to use to assemble the URL
     */
    public static function build( &$query )
    {
        $segments = array();

        // get a menu item based on the Itemid or the currently active item
        $menu = JFactory::getApplication()->getMenu();

        if (empty($query['Itemid']))
        {
            $item = $menu->getActive();
        }
            else
        {
            $item = $menu->getItem( $query['Itemid'] );
        }

        $menuView = (empty($item->query['view'])) ? null : $item->query['view'];
        $menuTask = (empty($item->query['task'])) ? null : $item->query['task'];
        $menuId = (empty($item->query['id'])) ? null : $item->query['id'];

        // if the menu item and the query match...
        if ($menuView == $query['view'] &&
            $menuTask == $query['task'] &&
            $menuId == $query['id']
        ) {
            // unset all variables and use the menu item's alias set by user
            unset ($query['view']);
            unset ($query['task']);
            unset ($query['id']);
        }

        // otherwise, create the sef url from the query
        if ( !empty ($query['view'])) {
            $view = $query['view'];
            $segments[] = $view;
            unset ($query['view']);
        }

        if ( !empty ($query['task'])) {
            $task = $query['task'];
            $segments[] = $task;
            unset ($query['task']);
        }

        if ( !empty ($query['id'])) {
            $id = $query['id'];
            $segments[] = $id;
            unset ($query['id']);
        }

        return $segments;
    }

    /**
     * Parses the segments of a URL
     *
     * @param   array   The segments of the URL to parse
     * @return  array   The URL attributes
     */
    public static function parse( $segments )
    {
        //echo "segments:<br /><pre>";
        //print_r($segments);
        //echo "</pre>";

        $vars = array();
        $count = count($segments);

        $vars['view'] = $segments[0];
        switch ($segments[0])
        {
            default:
                if ($count == '2')
                {
                    $vars['task'] = $segments[1];
                }

                if ($count == '3')
                {
                    $vars['task'] = $segments[1];
                    $vars['id'] = $segments[2];
                }
                break;
        }


        //echo "vars:<br /><pre>";
        //print_r($vars);
        //echo "</pre>";

        return $vars;
    }
}