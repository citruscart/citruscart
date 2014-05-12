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

class CitruscartTableProductRelations extends CitruscartTable 
{
    function CitruscartTableProductRelations( &$db ) 
    {
        $tbl_key    = 'productrelation_id';
        $tbl_suffix = 'productrelations';
        $this->set( '_suffix', $tbl_suffix );
        $name       = 'citruscart';
        
        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );   
    }
	
	function check()
	{
		if (empty($this->product_id_from))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_FROM_REQUIRED') );
			return false;
		}

		if (empty($this->product_id_to))
        {
            $this->setError( JText::_('COM_CITRUSCART_PRODUCT_TO_REQUIRED') );
            return false;
        }
        
	    if (empty($this->relation_type))
        {
            $this->setError( JText::_('COM_CITRUSCART_RELATION_TYPE_REQUIRED') );
            return false;
        }
		
		return true;
	}
}
