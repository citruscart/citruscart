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

if ( !class_exists('Citruscart') ) 
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

Citruscart::load( "CitruscartHelperBase", 'helpers._base' );

class CitruscartHelperBillets extends CitruscartHelperBase 
{
    /**
     * Checks if Billets is installed
     * 
     * @return boolean
     */
    function isInstalled()
    {
        $success = false;
        
        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_billets/defines.php')) 
        {
            JLoader::register( "Billets", JPATH_ADMINISTRATOR."/components/com_billets/defines.php" );
           
            if (version_compare(Billets::getInstance()->getVersion(), '4.2.0', '>=')) 
            {
                $success = true;
            }
        }                
        return $success;
    }
    
    /**
     * Processes a new order
     * 
     * @param $order_id
     * @return unknown_type
     */
    function processOrder( $order_id ) 
    {
        if (!$this->isInstalled())
        {
            $this->setError( JText::_('COM_CITRUSCART_BILLETS_NOT_INSTALLED') );
            return null;
        }
        
        // get the order
        $model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
        $model->setId( $order_id );
        $order = $model->getItem();
        
        // find the products in the order that impact billets ticket limit 
        foreach ($order->orderitems as $orderitem)
        {
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
            $model->setId( $orderitem->product_id );
            $product = $model->getItem();
            
            $billets_ticket_limit_increase = $product->product_parameters->get('billets_ticket_limit_increase');
            $billets_ticket_limit_exclusion = $product->product_parameters->get('billets_ticket_limit_exclusion');
            $billets_hour_limit_increase = $product->product_parameters->get('billets_hour_limit_increase');
            $billets_hour_limit_exclusion = $product->product_parameters->get('billets_hour_limit_exclusion');
            
            // does this product impact ticket limits?
            if ( $billets_ticket_limit_increase > '0' || $billets_ticket_limit_exclusion == '1' )
            {
                // update userdata
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_billets/tables' );
                $userdata = JTable::getInstance('Userdata', 'BilletsTable');
                $userdata->load( array('user_id'=>$order->user_id) );
                $userdata->user_id = $order->user_id;
                $userdata->ticket_max = $userdata->ticket_max + $billets_ticket_limit_increase;
                if ($billets_ticket_limit_exclusion == '1')
                {
                    $userdata->limit_tickets_exclusion = $billets_ticket_limit_exclusion; 
                }
                $userdata->save();
            }
            
        	// does this product impact hour limits?
            if ( $billets_hour_limit_increase > '0' || $billets_hour_limit_exclusion == '1' )
            {
                // update userdata
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_billets/tables' );
                $userdata = JTable::getInstance('Userdata', 'BilletsTable');
                $userdata->load( array('user_id'=>$order->user_id) );
                $userdata->user_id = $order->user_id;
                $userdata->hour_max = $userdata->hour_max + $billets_hour_limit_increase;
                if ($billets_hour_limit_exclusion == '1')
                {
                    $userdata->limit_hours_exclusion = $billets_hour_limit_exclusion; 
                }
                $userdata->save();
            }
        }
    }
}