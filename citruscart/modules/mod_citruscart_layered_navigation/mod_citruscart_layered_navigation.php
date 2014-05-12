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

// if DSC is not loaded all is lost anyway
if (!defined('_DSC')) { return; }

// Check the registry to see if our Citruscart class has been overridden
if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

require_once( dirname(__FILE__).'/helper.php' );

// include lang files
$lang = JFactory::getLanguage();
$lang->load( 'com_citruscart', JPATH_BASE );
$lang->load( 'com_citruscart', JPATH_ADMINISTRATOR );
Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

$helper = new modCitruscartLayeredNavigationFiltersHelper( $params );
$categories = $helper->getCategories();
$manufacturers = $helper->getManufacturers();
$priceRanges = $helper->getPriceRanges();
$attributes = $helper->getAttributes();
$ratings = $helper->getRatings();
$found = $helper->getCondition();
$trackcatcount = $helper->getTrackCatCount();
$filters = $helper->getFilters();

require JModuleHelper::getLayoutPath('mod_citruscart_layered_navigation', $params->get('layout', 'default'));
