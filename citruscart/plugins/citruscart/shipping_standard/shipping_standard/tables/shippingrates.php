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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableShippingRates extends CitruscartTable
{
	function CitruscartTableShippingRates ( &$db )
	{
        $tbl_key    = 'shipping_rate_id';
        $tbl_suffix = 'shippingrates';
        $this->set( '_suffix', $tbl_suffix );
        $name       = 'citruscart';

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
        if (empty($this->shipping_method_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_SHIPPING_METHOD_REQUIRED') );
            return false;
        }
        if (empty($this->geozone_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_GEOZONE_REQUIRED') );
            return false;
        }

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