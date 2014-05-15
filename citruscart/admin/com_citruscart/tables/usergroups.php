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

class CitruscartTableUserGroups extends CitruscartTableXref 
{
	/** 
	 * @param $db
	 * @return unknown_type
	 */
	function __construct( &$db ) 
	{
		$keynames = array();
		$keynames['user_id']  = 'user_id';
        $keynames['group_id'] = 'group_id';
        $this->setKeyNames( $keynames );
		        
		$tbl_key 	= 'user_id';
		$tbl_suffix = 'usergroupxref';
		$name 		= 'citruscart';
			
		$this->set( '_tbl_key', $tbl_key );
		$this->set( '_suffix', $tbl_suffix );
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		if (empty($this->group_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_GROUP_REQUIRED') );
			return false;
		}
		if (empty($this->user_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_USER_REQUIRED') );
			return false;
		}
		
		return true;
	}
}
