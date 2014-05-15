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

class CitruscartHelperJuga extends CitruscartHelperBase 
{
    /**
     * Checks if Juga is installed
     * 
     * @return boolean
     */
    function isInstalled()
    {
        $success = false;
        
        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_juga/defines.php')) 
        {
            JLoader::register( "Juga", JPATH_ADMINISTRATOR."/components/com_juga/defines.php" );
            if (version_compare(Juga::getVersion(), '2.2.0', '>=')) 
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
            $this->setError( JText::_('COM_CITRUSCART_JUGA_NOT_INSTALLED') );
            return null;
        }
        
        // get the order
        $model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
        $model->setId( $order_id );
        $order = $model->getItem();
        
        // find the products in the order that are integrated 
        foreach ($order->orderitems as $orderitem)
        {
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
            $model->setId( $orderitem->product_id );
            $product = $model->getItem();
            
            $juga_group_csv_add = $product->product_parameters->get('juga_group_csv_add');
            $juga_group_csv_remove = $product->product_parameters->get('juga_group_csv_remove');
            
            $ids_remove = explode( ',', $juga_group_csv_remove );
            if (!empty($ids_remove))
            {
                foreach ($ids_remove as $id)
                {
                    $this->remove($order->user_id, $id);
                }
            }
            
            $ids_add = explode( ',', $juga_group_csv_add );
            if ( !empty($ids_add) )
            {
                foreach ($ids_add as $id)
                {
                    $this->add($order->user_id, $id);
                }
            }
        }
    }
    
    /**
     * 
     * Enter description here ...
     * @param $subscription     mixed  CitruscartTableSubscriptions object or a subscription_id
     * @return unknown_type
     */
    function doExpiredSubscription( $subscription )
    {
        if (is_numeric($subscription))
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
            $table = JTable::getInstance( 'Subscriptions', 'CitruscartTable' );
            $table->load( array( 'subscription_id' => $subscription ) );
            $subscription = $table;
        }
        
        if (empty($subscription->subscription_id) || !is_object($subscription))
        {
            $this->setError( JText::_('COM_CITRUSCART_JUGA_INVALID_SUBSCRIPTION') );
            return false;
        }
        

        if (!empty($subscription->product_id))
        {
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
            $model->setId( $subscription->product_id );
            $product = $model->getItem();
            
            $juga_group_csv_add = $product->product_parameters->get('juga_group_csv_add_expiration');
            $juga_group_csv_remove = $product->product_parameters->get('juga_group_csv_remove_expiration');
            
            $ids_remove = explode( ',', $juga_group_csv_remove );
            if (!empty($ids_remove))
            {
                foreach ($ids_remove as $id)
                {
                    $this->remove($subscription->user_id, $id);
                }
            }
            
            $ids_add = explode( ',', $juga_group_csv_add );
            if ( !empty($ids_add) )
            {
                foreach ($ids_add as $id)
                {
                    $this->add($subscription->user_id, $id);
                }
            }
        }
        
        return true;        
    }
    
    /**
     * Checks if user is in a group
     * 
     * @param $userid
     * @param $groupid
     * @return unknown_type
     */
    function already( $userid, $groupid ) 
    {
        $success = false;
        $database = JFactory::getDBO();
        
        // query the db to see if the user is already a member of group
        $database->setQuery("
            SELECT 
                `user_id` 
            FROM 
                #__juga_u2g
            WHERE 
                `group_id` = '{$groupid}'
            AND 
                `user_id` = '{$userid}' 
        ");
        
        $success = $database->loadResult();

        return $success;
    }
    
    /**
     * Adds User to a Group if not already in it
     * 
     * @param $userid
     * @param $groupid
     * @return unknown_type
     */
    function add( $userid, $groupid )
    {
        $success = false;
        $database = JFactory::getDBO();
        
        $already = $this->already( $userid, $groupid );
        
        // if they aren't already a member of the group, add them to the group
        if (($already != $userid)) 
        {
            $database->setQuery("
                INSERT INTO 
                    #__juga_u2g
                SET
                    `user_id` = '{$userid}',
                    `group_id` = '{$groupid}'
            ");
            
            if ($database->query()) {
                $success = true; 
            }
        } 
        
        return $success;
    }
    
    /**
     * Remove User from a Group 
     * 
     * @param $userid
     * @param $groupid
     * @return unknown_type
     */
    function remove( $userid, $groupid )
    {
        $success = false;
        $database = JFactory::getDBO();
        
        $database->setQuery("
            DELETE FROM 
                #__juga_u2g
            WHERE
                `user_id` = '{$userid}'
            AND
                `group_id` = '{$groupid}'
        ");
        
        if ($database->query()) {
            $success = true; 
        }
    
        return $success;
    }

}