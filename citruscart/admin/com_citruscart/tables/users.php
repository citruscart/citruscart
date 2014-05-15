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

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableUsers extends CitruscartTable 
{
	function CitruscartTableUsers( &$db ) 
	{
		$tbl_key 	= 'id';
		$tbl_suffix = 'users';
		$this->set( '_suffix', $tbl_suffix );
		
		parent::__construct( "#__{$tbl_suffix}", $tbl_key, $db );	
	}
}
