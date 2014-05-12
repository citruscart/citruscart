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

class CitruscartTableProductAttributeOptionValues extends CitruscartTable 
{
	function CitruscartTableProductAttributeOptionValues ( &$db ) 
	{
		
		$tbl_key 	= 'productattributeoptionvalue_id';
		$tbl_suffix = 'productattributeoptionvalues';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}
	
	/**
	 * Checks row for data integrity.
	 *  
	 * @return unknown_type
	 */
	function check()
	{
		if (empty($this->productattributeoption_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_ATTRIBUTE_OPTION_ASSOCIATION_REQUIRED') );
			return false;
		}
        if (empty($this->productattributeoptionvalue_value))
        {
            $this->setError( JText::_('COM_CITRUSCART_ATTRIBUTE_OPTION_VALUE_REQUIRED') );
            return false;
        }
		return true;
	}
	
    /**
     * Adds context to the default reorder method
     * @return unknown_type
     */
    function reorder($where = '')
    {
        parent::reorder('productattributeoption_id = '.$this->_db->Quote($this->productattributeoption_id) );
    }
}
