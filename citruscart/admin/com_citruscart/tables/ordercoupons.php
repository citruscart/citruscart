<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableOrderCoupons extends CitruscartTable
{
    public $_increase_coupon_uses = true;
    
	function CitruscartTableOrderCoupons ( &$db )
	{
		$tbl_key 	= 'ordercoupon_id';
		$tbl_suffix = 'ordercoupons';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}
	
	function check()
	{
	    // TODO Check order_id and coupon_id?
		return true;
	}
	
	function save($src='', $orderingFilter = '', $ignore = '')
	{
	    if ($return = parent::save($src, $orderingFilter, $ignore))
	    {
	        if ($this->_increase_coupon_uses) 
	        {
	            $coupon = JTable::getInstance( 'Coupons', 'CitruscartTable' );
	            $coupon->load( array( 'coupon_id'=>$this->coupon_id ) );
	            $coupon->coupon_uses = $coupon->coupon_uses + 1;
	            if (!$coupon->save())
	            {
	                JFactory::getApplication()->enqueueMessage( $coupon->getError() );
	            }	            
	        }
	    }
	    
	    return $return;
	}
}