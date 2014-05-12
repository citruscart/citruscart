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

class CitruscartTableTaxrates extends CitruscartTable 
{
	function CitruscartTableTaxrates ( &$db ) 
	{
		
		$tbl_key 	= 'tax_rate_id';
		$tbl_suffix = 'taxrates';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	/**
	 * Checks the table object for integrity
	 * @return unknown_type
	 */
	function check()
	{
		if (empty($this->tax_rate))
		{
			$this->setError( "Tax Rate Required" );
			return false;
		}
	    if (empty($this->geozone_id))
        {
            $this->setError( "GeoZone Required" );
            return false;
        }
		$nullDate	= $this->_db->getNullDate();
		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}
		return true;
	}
}
