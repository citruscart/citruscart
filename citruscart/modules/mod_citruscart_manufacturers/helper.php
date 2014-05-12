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
jimport( 'joomla.application.component.model' );

class modCitruscartManufacturersHelper extends JObject
{
    /**
     * Sets the modules params as a property of the object
     * @param unknown_type $params
     * @return unknown_type
     */
    function __construct( $params )
    {
        $this->params = $params;
    }

    /**
     *
     * @return unknown_type
     */
    function getItems()
    {
        // Check the registry to see if our Citruscart class has been overridden
        if ( !class_exists('Citruscart') )
            JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

        // load the config class
        Citruscart::load( 'Citruscart', 'defines' );

        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
    	JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

        // get the model
    	$model = JModelLegacy::getInstance( 'Manufacturers', 'CitruscartModel' );

    	// TODO Make this depend on the current filter_category?

    	// setting the model's state tells it what items to return
    	$model->setState('filter_enabled', '1');
    	$model->setState('order', 'tbl.manufacturer_name');

    	// set the states based on the parameters

        // using the set filters, get a list
    	$items = $model->getList();

    	if (!empty($items))
    	{
    	    foreach ($items as $item)
    	    {
    	    // this gives error
    	       $item->itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->manufacturer($item->manufacturer_id, false);
    	        if (empty($item->itemid))
    	        {
                    $item->itemid = $this->params->get('itemid');
    	        }
    	    }
    	}

    	return $items;
    }
}



