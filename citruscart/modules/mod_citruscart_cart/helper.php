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

class modCitruscartCartHelper
{
    function getCart()
    {
        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );

        // determine whether we're working with a session or db cart
        $suffix = CitruscartHelperCarts::getSuffix();
    	$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );

        $session = JFactory::getSession();
        $user = JFactory::getUser();

        $model->setState('filter_user', $user->id );
        if (empty($user->id))
        {
            $model->setState('filter_session', $session->getId() );
        }

    	$list = $model->getList( false, false );

    	Citruscart::load( 'Citruscart', 'defines' );
        $config = Citruscart::getInstance();
        $show_tax = $config->get('display_prices_with_tax');
        $this->using_default_geozone = false;

        if ($show_tax)
        {
            Citruscart::load('CitruscartHelperUser', 'helpers.user');
            $geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
            if (empty($geozones))
            {
                // use the default
                $this->using_default_geozone = true;
                $table = JTable::getInstance('Geozones', 'CitruscartTable');
                $table->load(array('geozone_id'=>Citruscart::getInstance()->get('default_tax_geozone')));
                $geozones = array( $table );
            }

            Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
            foreach ($list as &$item)
            {
                $taxtotal = CitruscartHelperProduct::getTaxTotal($item->product_id, $geozones);
                $item->product_price = $item->product_price + $taxtotal->tax_total;
                $item->taxtotal = $taxtotal;
            }
        }

    	return $list;
    }
}

