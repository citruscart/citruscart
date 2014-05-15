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
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableSubscriptionHistory extends CitruscartTable 
{
	function CitruscartTableSubscriptionHistory( &$db ) 
	{
		$tbl_key 	= 'subscriptionhistory_id';
		$tbl_suffix = 'subscriptionhistory';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		$nullDate	= $this->_db->getNullDate();

		if (empty($this->created_datetime) || $this->created_datetime == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_datetime = $date->toSql();
		}		
		return true;
	}
	
    /**
     * 
     * @param unknown_type $updateNulls
     * @return unknown_type
     */
    function store( $updateNulls=false )
    {
        if ( $return = parent::store( $updateNulls ))
        {
        	if ($this->notify_customer == '1')
        	{
        		Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
        		$helper = CitruscartHelperBase::getInstance('Email');
        		
        		$model = Citruscart::getClass("CitruscartModelSubscriptions", "models.subscriptions");
        		$model->setId($this->subscription_id);
        		$subscription = $model->getItem();
        		
        		$helper->sendEmailNotices($subscription, 'subscription');
        	}
        }
        return $return;
    }
}
