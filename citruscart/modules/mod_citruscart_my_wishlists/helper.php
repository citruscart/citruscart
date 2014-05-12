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

class modCitruscartMyWishlistsHelper extends JObject
{
    /**
     * Sets the modules params as a property of the object
     * @param unknown_type $params
     * @return unknown_type
     */
    function __construct( $params )
    {
        $this->params = $params;

        if ( !class_exists('Citruscart') ) {
            JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );
        }

        // load the config class
        Citruscart::load( 'Citruscart', 'defines' );

        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

        $this->defines = Citruscart::getInstance();

        Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
        $this->router = new CitruscartHelperRoute();

        $this->user = JFactory::getUser();
    }

    /**
     *
     * @return unknown
     */
    function getItems()
    {
    	$this->model = JModelLegacy::getInstance( 'Wishlists', 'CitruscartModel' );

    	$user = JFactory::getUser();
    	if (empty($user->id)) {
    	    return array();
    	}

    	$this->model->setState( 'filter_user', $user->id);

    	if ($this->params->get( 'max_number' ) > '0') {
            $this->model->setState( 'limit', $this->params->get( 'max_number' ) );
    	}

    	$items = $this->model->getList();

    	return $items;
    }
}

