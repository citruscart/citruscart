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

class modCitruscartMyOrdersHelper extends JObject
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
     * Sample use of the products model for getting products with certain properties
     * See admin/models/products.php for all the filters currently built into the model
     *
     * @param $parameters
     * @return unknown_type
     */
    function getOrders()
    {
        // Check the registry to see if our Citruscart class has been overridden
        if ( !class_exists('Citruscart') )
            JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

        // load the config class
        Citruscart::load( 'Citruscart', 'defines' );

        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
    	JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

        // get the model
    	$model = JModelLegacy::getInstance( 'orders', 'CitruscartModel' );
        $model->setState( 'limit', $this->params->get( 'max_number', '5') );
    	$user = JFactory::getUser();

    	$model->setState( 'filter_userid', $user->id);
    	$orders = $model->getList();
    	return $orders;
    }
}

