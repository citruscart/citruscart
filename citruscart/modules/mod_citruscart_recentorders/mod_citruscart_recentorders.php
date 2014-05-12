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



// if DSC is not loaded all is lost anyway
/*if (!defined('_DSC')) { return; }

// Check the registry to see if our Citruscart class has been overridden */
if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

require_once( dirname(__FILE__).'/helper.php' );



$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));


$helper = new modCitruscartRecentOrdersHelper( $params );

$cache = JFactory::getCache('com_citruscart');
$cache->setCaching(true);
$cache->setLifeTime('900');
$orders = $cache->call(array($helper, 'getOrders'));

require JModuleHelper::getLayoutPath('mod_citruscart_recentorders', $params->get('layout', 'default'));
