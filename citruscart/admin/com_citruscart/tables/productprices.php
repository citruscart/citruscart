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

class CitruscartTableProductprices extends CitruscartTable
{
	function CitruscartTableProductprices ( &$db )
	{

		$tbl_key 	= 'product_price_id';
		$tbl_suffix = 'productprices';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

	/**
	 * Checks row for data integrity.
	 * Assumes working dates have been converted to local time for display,
	 * so will always convert working dates to GMT
	 *
	 * @return unknown_type
	 */
	function check()
	{
		if (empty($this->product_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_ASSOCIATION_REQUIRED') );
			return false;
		}

		$nullDate = $this->_db->getNullDate();
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$CitruscartHelperBase = new CitruscartHelperBase();
        $this->product_price_startdate = ($this->product_price_startdate != $nullDate) ? $CitruscartHelperBase->getOffsetDate( $this->product_price_startdate ) : $this->product_price_startdate;
        $this->product_price_enddate = ($this->product_price_enddate != $nullDate) ? $CitruscartHelperBase->getOffsetDate( $this->product_price_enddate ) : $this->product_price_enddate;

		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}

		$date = JFactory::getDate();
		$this->modified_date = $date->toSql();

		return true;
	}
}
