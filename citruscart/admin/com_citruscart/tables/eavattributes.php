<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/


/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableEavAttributes extends CitruscartTable
{
	function __construct(&$db)
	{
		$tbl_key 	= 'eavattribute_id';
		$tbl_suffix = 'eavattributes';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
    function check()
    {      
    	if (empty($this->eavattribute_label))
        {
            $this->setError( JText::_('COM_CITRUSCART_LABEL_REQUIRED') );
            return false;
        }  
        if (empty($this->eaventity_type))
        {
            $this->setError( JText::_('COM_CITRUSCART_ENTITY_TYPE_REQUIRED') );
            return false;
        }
    	if (empty($this->eavattribute_type))
        {
            $this->setError( JText::_('COM_CITRUSCART_TYPE_REQUIRED') );
            return false;
        }
        if (empty($this->eavattribute_alias)) 
        {
            $this->eavattribute_alias = $this->eavattribute_label;
        }
        $this->eavattribute_alias = $this->stringDBSafe($this->eavattribute_alias);
        
        if ($this->eavattribute_type == 'datetime' && empty($this->eavattribute_format_strftime))
        {
            $this->eavattribute_format_strftime = '%Y-%m-%d %H:%M:%S';
        }
        
        if ($this->eavattribute_type == 'datetime' && empty($this->eavattribute_format_date))
        {
            $this->eavattribute_format_date = 'Y-m-d H:i:s';
        }
        
        return true;
    }
}
