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

class CitruscartTableProductIssues extends CitruscartTable
{
	function CitruscartTableProductIssues ( &$db )
	{

		$tbl_key 	= 'product_issue_id';
		$tbl_suffix = 'productissues';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

	/**
	 * Checks row for data integrity.
	 * Assumes working dates have been converted to local time for display,
	 * so will always convert working dates to GMT
	 *
	 * @return unknown_type
	 */
	function check()
	{
		if (empty($this->product_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_ASSOCIATION_REQUIRED') );
			return false;
		}

		$offset = JFactory::getConfig()->getValue( 'config.offset' );
		if( isset( $this->publishing_date ) )
		{
			$this->publishing_date = date( 'Y-m-d H:i:s', strtotime( CitruscartHelperBase::getOffsetDate( $this->publishing_date, -$offset ) ) );
		}


		$nullDate = $this->_db->getNullDate();
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}

		$date = JFactory::getDate();
		$this->modified_date = $date->toSql();
		$act = strtotime( Date( 'Y-m-d', strtotime( $this->publishing_date ) ) );
					
		$db = $this->_db;
		if( empty( $this->product_issue_id ) ) // add at the end
		{
			$q = 'SELECT `publishing_date` FROM `#__citruscart_productissues` WHERE `product_id`='.$this->product_id.' ORDER BY `publishing_date` DESC LIMIT 1';
			$db->setQuery( $q );
			$next = $db->loadResult();
			if( $next === null )
				return true;
			$next = strtotime( $next );
			if( $act <= $next )
			{
				$this->setError( JText::_('COM_CITRUSCART_PUBLISHING_DATE_IS_NOT_PRESERVING_ISSUE_ORDER').' - '.$this->publishing_date );
				return false;
			}
		}
		else
		{
			$q = 'SELECT `publishing_date` FROM `#__citruscart_productissues` WHERE `product_issue_id`='.$this->product_issue_id;
			$db->setQuery( $q );
			$original = $db->loadResult();
			if( $act == strtotime( Date( 'Y-m-d', strtotime( $original ) ) ) )
				return true;

			$q = 'SELECT `publishing_date` FROM `#__citruscart_productissues` WHERE `product_id`='.$this->product_id.' AND `publishing_date` < \''.$original.'\' ORDER BY `publishing_date` DESC LIMIT 1';
			$db->setQuery( $q );
			$prev = $db->loadResult();
			$q = 'SELECT `publishing_date` FROM `#__citruscart_productissues` WHERE `product_id`='.$this->product_id.' AND `publishing_date` > \''.$original.'\' ORDER BY `publishing_date` ASC LIMIT 1';
			$db->setQuery( $q );
			$next = $db->loadResult();
			
			if( $prev === null )
				$prev = 0;
			else
				$prev = strtotime( $prev );
			if( $next )
				$next = strtotime( $next );
	
			if( ( $prev >= $act ) || ( $next && $next <= $act ) )
			{
				$this->setError( JText::_('COM_CITRUSCART_PUBLISHING_DATE_IS_NOT_PRESERVING_ISSUE_ORDER').' - '.$this->publishing_date );
				return false;
			}
		}
		return true;
	}
}
