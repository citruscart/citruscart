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

if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );

/**
 * Build the route
 * Is just a wrapper for CitruscartHelperRoute::build()
 *
 * @param unknown_type $query
 * @return unknown_type
 */
function CitruscartBuildRoute(&$query)
{
    return CitruscartHelperRoute::build($query);
}

/**
 * Parse the url segments
 * Is just a wrapper for CitruscartHelperRoute::parse()
 *
 * @param unknown_type $segments
 * @return unknown_type
 */
function CitruscartParseRoute($segments)
{
    return CitruscartHelperRoute::parse($segments);
}