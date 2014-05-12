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

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();

// params
$display_null = $params->get( 'display_null', '1' );
$null_text = $params->get( 'null_text', 'No Items in Your Cart' );
$isAjax = $mainframe->getUserState( 'mod_usercart.isAjax' );
$ajax = ($isAjax == '1');

// Grab the cart
Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
$items = CitruscartHelperCarts::getProductsInfo();
$num = count($items);

// Convert the cart to a "fake" order, to show totals and others things
JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
$orderTable = JTable::getInstance('Orders', 'CitruscartTable');
foreach($items as $item)
{
    $orderTable->addItem($item);
}
$items = $orderTable->getItems();

Citruscart::load( 'Citruscart', 'defines' );
$config = Citruscart::getInstance();
$show_tax = $config->get('display_prices_with_tax');
if ($show_tax)
{
    Citruscart::load('CitruscartHelperUser', 'helpers.user');
    $geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
    if (empty($geozones))
    {
        // use the default
        $table = JTable::getInstance('Geozones', 'CitruscartTable');
        $table->load(array('geozone_id'=>Citruscart::getInstance()->get('default_tax_geozone')));
        $geozones = array( $table );
    }
    $orderTable->setGeozones( $geozones );
}

// order calculation can happen after all items are added to order object
$orderTable->calculateTotals();

// format the subtotal
//$order_subtotal = CitruscartHelperBase::currency($orderTable->order_total);

if (!empty($items) || (empty($items) && $params->get('display_null')) )
{
	require JModuleHelper::getLayoutPath('mod_citruscart_cart', $params->get('layout', 'default'));
}
    else
{
    // don't display anything
}

