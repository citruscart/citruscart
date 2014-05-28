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

class DSCSocial extends JObject
{
    /**
     * Returns a reference to the a Helper object, only creating it if it doesn't already exist
     *
     * @param type 		$type 	 The helper type to instantiate
     * @param string 	$prefix	 A prefix for the helper class name. Optional.
     * @return helper The Helper Object
     */
    public static function getInstance($type = '', $prefix = 'DSCSocial')
    {

        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

        $class = $prefix . ucfirst($type);


        if (empty($instances[$class])) {

            if (!class_exists($class)) {
                $path = JPATH_SITE . '/libraries/dioscouri/library/social/' . strtolower($type) . '.php';

                JLoader::register($class, $path);

                if (!class_exists($class)) {
                    JError::raiseWarning(0, 'Social class ' . $class . ' not found.');
                    return false;
                }

            }

            $instance = new $class();

            $instances[$class] =& $instance;
        }

        return $instances[$class];
    }


    public static function makeShortUrl($url)
    {
        return $url;
    }

    public function likebutton()
    {

    }
}


