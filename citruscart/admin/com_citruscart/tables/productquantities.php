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

class CitruscartTableProductQuantities extends CitruscartTable 
{
    function CitruscartTableProductQuantities( &$db ) 
    {
        $tbl_key    = 'productquantity_id';
        $tbl_suffix = 'productquantities';
        $this->set( '_suffix', $tbl_suffix );
        $name       = 'citruscart';
        
        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );   
    }
	
	
	function check()
	{
		if (empty($this->product_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_REQUIRED') );
			return false;
		}
		
		// be sure that product_attributes is sorted numerically
		if ($product_attributes = explode( ',', $this->product_attributes ))
		{
			sort($product_attributes);
			$this->product_attributes = implode(',', $product_attributes);
		}
		
		return true;
	}
}
