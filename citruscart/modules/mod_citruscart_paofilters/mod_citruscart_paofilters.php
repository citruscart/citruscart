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
if ( !class_exists('Citruscart') ) {
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );
}

require_once( dirname(__FILE__).'/helper.php' );

// include lang files
$lang = JFactory::getLanguage();
$lang->load( 'com_citruscart', JPATH_BASE );
$lang->load( 'com_citruscart', JPATH_ADMINISTRATOR );

$helper = new modCitruscartPAOFiltersHelper( $params );

$app = JFactory::getApplication();
$model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
$ns = $app->getName().'::'.'com.citruscart.model.'.$model->getTable()->get('_suffix');
$filter_category = $app->getUserStateFromRequest($ns.'.category', 'filter_category', '', 'int');

$category_ids = array();
if ($filter_category) {
    $category_ids = array( $filter_category );
}

$itemid = JRequest::getInt('Itemid');
$session = JFactory::getSession();
$app = JFactory::getApplication();
$ns = $app->getName().'::'.'com.citruscart.products.state.'.$itemid;
$session_state = $session->get( $ns );

$helper->state = $session_state;

$items = $helper->getItems( $category_ids );
FB::log($items, 'modCitruscartPAOFilters.items');
FB::log($helper->state, 'modCitruscartPAOFilters.$helper->state');

$filter_pao_id_groups = $helper->state['filter_pao_id_groups'];
$show_reset = false;
if (!empty($filter_pao_id_groups))
{
    foreach ($filter_pao_id_groups as $filter_pao_id_group)
    {
        if (!empty($filter_pao_id_group) && is_array($filter_pao_id_group))
        {
            $filter_id_set = implode("', '", $filter_pao_id_group);

            if (!empty($filter_id_set))
            {
                $show_reset = true;
                break;
            }
        }
    }
}

$optionnames = array();
if ($show_reset)
{
    // a filter exists, so get its name to display in the option-group
    $model = Citruscart::getClass('CitruscartModelProductAttributeOptions', 'models.productattributeoptions');
    foreach ($filter_pao_id_groups as $key=>$filter_pao_id_group)
    {
        if (!empty($filter_pao_id_group) && is_array($filter_pao_id_group))
        {
            $optionnames[$key] = $model->getNames($filter_pao_id_group);
        }
    }
}

FB::log($filter_pao_id_groups, '$filter_pao_id_groups');
FB::log($optionnames, '$$optionnames');

require JModuleHelper::getLayoutPath('mod_citruscart_paofilters', $params->get('layout', 'default'));
