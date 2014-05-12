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
if ( !class_exists('Citruscart') ) {
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );
}

require_once( dirname(__FILE__).'/helper.php' );

// include lang files
$element = 'com_citruscart';
$lang = JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );

$display_null = $params->get( 'display_null', '1' );
$null_text = $params->get( 'null_text', JText::_('COM_CITRUSCART_NO_WISHLISTS_FOUND') );

$helper = new modCitruscartMyWishlistsHelper( $params );
$items = $helper->getItems();
$num = count($items);

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();

if (empty($num) && !$display_null) {
    return;
}

require JModuleHelper::getLayoutPath('mod_citruscart_my_wishlists', $params->get('layout', 'default'));
