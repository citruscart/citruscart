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

class CitruscartHelperAmigos extends CitruscartHelperBase 
{
    protected $commissions = array();
    
    /**
     * Checks if Amigos is installed
     * 
     * @return boolean
     */
    function isInstalled()
    {
        $success = false;
        
        jimport('joomla.filesystem.file');
     
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_amigos/defines.php')) 
        {
            $success = true;
        }                
        return $success;
    }
    
    /**
     * Gets a user's referral status
     * and returns boolean
     * 
     * @param int $userid
     * @return boolean if false, object if user is a referral
     */
    function getReferralStatus( $userid )
    {
        $return = false;
        
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        $query = new CitruscartQuery();
        $query->select( 'tbl.*' );
        $query->from( '#__amigos_logs AS tbl' );
        $query->where( "tbl.userid = '".(int) $userid."'" );
        
        $db = JFactory::getDbo();
        $db->setQuery( (string) $query );
        $referral = $db->loadObject();
        
        if (!empty($referral->accountid))
        {
            $return = $referral;
        }
        
        return $return;
    }
    
    /**
     * Creates a commission record for an order 
     * if Amigos is installed and the user is a referral
     * 
     * @param int $order_id An order number
     * @return array
     */
    function getCommissions( $order_id )
    {
        if (!isset($this->commissions[$order_id]))
        {
            $return = array();
            Citruscart::load( 'CitruscartQuery', 'library.query' );
            $query = new CitruscartQuery();
            $query->select( 'tbl.*' );
            $query->from( '#__amigos_commissions AS tbl' );
            $query->where( "tbl.orderid = '".(int) $order_id."'" );
            $query->where( "tbl.order_type = 'com_citruscart'" );
            
            $db = JFactory::getDbo();
            $db->setQuery( (string) $query );
            $this->commissions[$order_id] = $db->loadObjectList();
        }

        return $this->commissions[$order_id];
    }
    
    /**
     * Creates a commission record for an order 
     * if Amigos is installed and the user is a referral
     * 
     * @param int $order_id An order number
     * @return boolean
     */
    function createCommission( $order_id ) 
    {
        if (!$this->isInstalled())
        {
            $this->setError( JText::_('COM_CITRUSCART_AMIGOS_NOT_INSTALLED') );
            return null;
        }
        
        // get the order
        $model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
        $model->setId( $order_id );
        $order = $model->getItem();
        
        $referral = $this->getReferralStatus($order->user_id);
        if (empty($order->user_id) || empty($referral))
        {
            $this->setError( JText::_('COM_CITRUSCART_AMIGOS_USER_NOT_A_REFERRAL') );
            return null;            
        }
        
        // If here, create a commissions record
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_amigos/tables' );
        if (!class_exists('Amigos'))
        {
            JLoader::import( 'com_amigos.defines', JPATH_ADMINISTRATOR.'/components' );    
        }        
        Citruscart::load( 'AmigosHelperCommission', 'helpers.commission', array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_amigos' ) );
        
        if (!empty($referral->accountid))
        {
            $config = Amigos::getInstance();
            $date = JFactory::getDate();
            
            if (version_compare(Amigos::getInstance()->getVersion(), '1.2.1', '<')) 
            {
                $account = JTable::getInstance('Accounts', 'Table');
            } 
                else
            {
                $account = JTable::getInstance('Accounts', 'AmigosTable');
            }
            
            $account->load( $referral->accountid );
            
            // get payout type and value
            if (version_compare(Amigos::getInstance()->getVersion(), '1.2.1', '<')) 
            {
                $payout = JTable::getInstance('Payouts', 'Table');
            } 
                else
            {
                $payout = JTable::getInstance('Payouts', 'AmigosTable');
            }
            
            $payout->load( $account->payoutid );
            $payout_type = $payout->payouttype ? $payout->payouttype : $config->get('default_payouttype', 'PPSP');
            $payout_value = $payout->value ? $payout->value : $config->get('default_payout_value', '10%');
            
            // determine the commission value based on each product's commission rate override
            $commission_value = 0;
            foreach ($order->orderitems as $orderitem)
            {
                $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
                $model->setId( $orderitem->product_id );
                $product = $model->getItem();
                
                // does this product have a override for the commission rate?
                if ($product->product_parameters->get('amigos_commission_override') === '')
                {
                    $product_commission_value = AmigosHelperCommission::calculate( $payout_type, $payout_value, $orderitem->orderitem_final_price );
                }
                    else
                {
                    $product_payout_value = $product->product_parameters->get('amigos_commission_override');
                    $product_commission_value = AmigosHelperCommission::calculate( $payout_type, $product_payout_value, $orderitem->orderitem_final_price );
                }
                
                $commission_value += $product_commission_value;
            }
            
            // create commission record
            if (version_compare(Amigos::getInstance()->getVersion(), '1.2.1', '<')) 
            {
                $commission = JTable::getInstance('Commissions', 'Table');
            } 
                else
            {
                $commission = JTable::getInstance('Commissions', 'AmigosTable');
            }
            
            $commission->accountid          = $account->id;
            $commission->orderid            = $order_id;
            $commission->order_type         = 'com_citruscart';
            $commission->order_userid       = $order->user_id;
            $commission->order_value        = $order->order_total;
            $commission->created_datetime   = $date->toSql();
            $commission->refer_url          = $referral->refer_url;
            $commission->amigosid           = $referral->amigosid;
            $commission->payouttype         = $payout_type;
            $commission->payout_value       = $payout_value;
            $commission->value              = $commission_value;
            
            if (!$commission->save())
            {
                JError::raiseNotice("createCommission01", "createCommission :: ".$commission->getError() );
                return false;
            }
            return true;
        }
        
        return null;
    }
    
}