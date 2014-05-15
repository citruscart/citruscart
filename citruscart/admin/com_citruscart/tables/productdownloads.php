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

class CitruscartTableProductDownloads extends CitruscartTable 
{
	function CitruscartTableProductDownloads ( &$db ) 
	{
		
		$tbl_key 	= 'productdownload_id';
		$tbl_suffix = 'productdownloads';
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
	    if (empty($this->order_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_ORDER_ID_REQUIRED') );
            return false;
        }
        // TODO This is technically unnecessary because of the join you can do with productfile_id, maybe remove it eventually?
	    if (empty($this->product_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_PRODUCT_ID_REQUIRED') );
            return false;
        }
	    $nullDate   = $this->_db->getNullDate();
        if (empty($this->productdownload_startdate) || $this->productdownload_startdate == $nullDate)
        {
            $date = JFactory::getDate();
            $this->productdownload_startdate = $date->toSql();
        }
        // if the enddate is 0000-00-00 then the download never expires
		return true;
	}
	
}
