<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
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
    
// include lang files
$element = 'com_citruscart';
$lang = JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
$lang->load( $element, JPATH_ADMINISTRATOR );

$category_filter = $params->get('category_filter', '1');
$filter_text = $params->get('filter_text');

/* Get the application */
$app = JFactory::getApplication();

$active = $app->getMenu()->getActive();

if (!empty($active))
{
    $item_id = $active->id;
}
    else
{
    //$item_id = JRequest::getInt('Itemid');
    $item_id = $app->input->getInt('Itemid');
}

require JModuleHelper::getLayoutPath('mod_citruscart_search', $params->get('layout', 'default'));

