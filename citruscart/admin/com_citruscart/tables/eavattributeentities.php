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

Citruscart::load( 'CitruscartTableXref', 'tables._basexref' );

class CitruscartTableEavAttributeEntities extends CitruscartTableXref 
{
	/** 
	 * @param $db
	 * @return unknown_type
	 */
	function __construct( &$db ) 
	{
		$keynames = array();
		$keynames['eaventity_id']  = 'eaventity_id';
		$keynames['eaventity_type']  = 'eaventity_type';
        $keynames['eavattribute_id'] = 'eavattribute_id';
        $this->setKeyNames( $keynames );
                
		$tbl_key 	= 'eaventity_id';
		$tbl_suffix = 'eavattributeentityxref';
		$name 		= 'citruscart';
		
		$this->set( '_tbl_key', $tbl_key );
		$this->set( '_suffix', $tbl_suffix );
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		if (empty($this->eavattribute_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_CATEGORY_REQUIRED') );
			return false;
		}
		if (empty($this->eaventity_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_ENTITY_REQUIRED') );
			return false;
		}
		if (empty($this->eaventity_type))
		{
			$this->setError( JText::_('COM_CITRUSCART_ENTITY_TYPE_REQUIRED') );
			return false;
		}
		
		return true;
	}
}
