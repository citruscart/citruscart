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

class CitruscartTableOrderHistory extends CitruscartTable 
{
	function CitruscartTableOrderHistory( &$db ) 
	{
		$tbl_key 	= 'orderhistory_id';
		$tbl_suffix = 'orderhistory';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		$nullDate	= $this->_db->getNullDate();

		if (empty($this->date_added) || $this->date_added == $nullDate)
		{
			$date = JFactory::getDate();
			$this->date_added = $date->toSql();
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
        		
        		$model = Citruscart::getClass("CitruscartModelOrders", "models.orders");
        		$model->setId($this->order_id); // this isn't necessary because you specify the requested PK id as a getItem() argument 
        		$order = $model->getItem($this->order_id, true);
        		
        		$helper->sendEmailNotices($order, 'order');
        	}
        }
        return $return;
    }
}
