<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableCoupons extends CitruscartTable
{
	function __construct( &$db )
	{
		
		$tbl_key = 'coupon_id';
		$tbl_suffix = 'coupons';
		$this->set( '_suffix', $tbl_suffix );
		$name = 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}
	
	function check( )
	{
		$db = $this->getDBO( );
		$nullDate = $db->getNullDate( );
		if ( empty( $this->created_date ) || $this->created_date == $nullDate )
		{
			$date = JFactory::getDate( );
			$this->created_date = $date->toSql( );
		}
		if ( empty( $this->modified_date ) || $this->modified_date == $nullDate )
		{
			$date = JFactory::getDate( );
			$this->modified_date = $date->toSql( );
		}
		$this->filterHTML( 'coupon_name' );
		if ( empty( $this->coupon_name ) )
		{
			$this->setError( JText::_('COM_CITRUSCART_NAME_REQUIRED') );
			return false;
		}
		$this->filterHTML( 'coupon_code' );
		if (empty($this->coupon_code) && $this->coupon_automatic != 1)
        {
            $this->setError( JText::_('COM_CITRUSCART_CODE_REQUIRED') );
            return false;
        }
        if($this->coupon_group == 'shipping' && $this->coupon_type != "0")
        {
        	$this->setError( JText::_('COM_CITRUSCART_SHIPPING_CAN_ONLY_BE_PER_ORDER') );
            return false;
        }
		return true;
	}
	
	/**
	 * Stores the object
	 * @param object
	 * @return boolean
	 */
	function store($updateNulls=false) 
	{
		$date = JFactory::getDate( );
		$this->modified_date = $date->toSql( );
		$store = parent::store($updateNulls );
		return $store;
	}
}
