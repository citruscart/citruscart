<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
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

// grab the Price range
$helper = new modCitruscartPriceFiltersHelper( $params );
$priceRanges = $helper->getPriceRange();

$show_remove = false;

$app = JFactory::getApplication();
$model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
$ns = $app->getName().'::'.'com.citruscart.model.'.$model->getTable()->get('_suffix');
$filter_price_from = $app->getUserStateFromRequest($ns.'price_from', 'filter_price_from', '0', 'int');
$filter_price_to = $app->getUserStateFromRequest($ns.'price_to', 'filter_price_to', '', '');
$filter_category = $app->getUserStateFromRequest($ns.'.category', 'filter_category', '', 'int');
if (!empty($filter_price_from) || !empty($filter_price_to))
{
    $show_remove = true;
}
$remove_pricefilter_url = "index.php?option=com_citruscart&view=products&filter_category=$filter_category&filter_price_from=&filter_price_to=";

require JModuleHelper::getLayoutPath('mod_citruscart_pricefilters', $params->get('layout', 'default'));
