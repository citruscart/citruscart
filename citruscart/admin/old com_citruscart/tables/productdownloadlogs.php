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

class CitruscartTableProductDownloadLogs extends CitruscartTable 
{
	function CitruscartTableProductDownloadLogs ( &$db ) 
	{
		
		$tbl_key 	= 'productdownloadlog_id';
		$tbl_suffix = 'productdownloadlogs';
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
		
		if (empty($this->productfile_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_FILE_ID_REQUIRED') );
			return false;
		}
        if (empty($this->user_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_USER_ID_REQUIRED') );
            return false;
        }
	    $nullDate   = $this->_db->getNullDate();
        if (empty($this->productdownloadlog_datetime) || $this->productdownloadlog_datetime == $nullDate)
        {
            $date = JFactory::getDate();
            $this->productdownloadlog_datetime = $date->toSql();
        }
        if (empty($this->productdownloadlog_ipaddress))
        {
        	/* Get the application */
            $app = JFactory::getApplication();
            $this->productdownloadlog_ipaddress = $app->input->get( 'REMOTE_ADDR', '', 'SERVER' );
        	//$this->productdownloadlog_ipaddress = JRequest::getVar( 'REMOTE_ADDR', '', 'SERVER' );
        }
		return true;
	}
}
