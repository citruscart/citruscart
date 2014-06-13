<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelProductIssues extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter_id	        = $this->getState('filter_id');
		$filter_product_id	        = $this->getState('filter_product_id');
		$filter_productid	        = $this->getState('filter_productid');
		
		if (strlen($filter_id))
		{
			$query->where('tbl.product_issue_id = '.(int) $filter_id);
		}
		
		if (strlen($filter_product_id))
		{
			$query->where('tbl.product_id = '.(int) $filter_product_id);
		}
		
		if (strlen($filter_productid))
		{
		    $query->where('tbl.product_id = '.(int) $filter_productid);
		}
	}

	/**
	 * Builds a generic ORDER BY clause based on the model's state
	 */
	protected function _buildQueryOrder(&$query)
	{
		$order      = $this->_db->escape( $this->getState('order') );
		$direction  = $this->_db->escape( strtoupper( $this->getState('direction') ) );

		if (strlen($order))
		{
			$query->order("$order $direction");
		}
		else
		{
			$query->order("publishing_date ASC");
		}
	}

	public function getList($refresh = false)
	{
		if (empty( $this->_list ) || $refresh)
		{
			$list = parent::getList($refresh);
			$nullDate = JFactory::getDbo()->getNullDate();

			if ( empty( $list ) ) {
				return array();
			}

			foreach ($list as $item)
			{
				// convert working dates to localtime for display
				$item->publishing_date = ($item->publishing_date != $nullDate) ? JHTML::_( "date", $item->publishing_date, '%Y-%m-%d %H:%M:%S' ) : $item->publishing_date;
			}

			$this->_list = $list;
		}

		return $this->_list;
	}

	/**
	 * Gets an item for displaying (as opposed to saving, which requires a JTable object)
	 * using the query from the model
	 *
	 * @return database->loadObject() record
	 */
	public function getItem($pk = NULL, $refresh = false, $emptyState = true) 
	{
		if (empty( $this->_item ))
		{
			$query = $this->getQuery();
			$this->_db->setQuery( (string) $query );
			$this->_item = $this->_db->loadObject();
			if (is_object($this->_item))
			{
				$nullDate = JFactory::getDbo()->getNullDate();
// 			convert working dates to localtime for display
				$this->_item->publishing_date = ($this->_item->publishing_date != $nullDate) ? JHTML::_( "date", $this->_item->product_price_startdate, '%Y-%m-%d %H:%M:%S' ) : $this->_item->publishing_date;
			}
		}

		
		JFactory::getApplication()->triggerEvent( 'onPrepare'.$this->getTable()->get('_suffix'), array( &$this->_item ) );

		return $this->_item;
	}


	/*
	 * Gets ID of the last issue for a product (or create enough empty entries to get the ID)
	 * @param $product_id
	 * @param $interval Number of issues
	 * @param $date Date from which we want to start looking for issues
	 *
	 * @return ID of the issue
	 */
	public function getEndIssueId( $product_id, $interval, $date = null )
	{
		if( $date === null )
		{
			$date = JFactory::getDate();
			$date->setOffset( -JFactory::getConfig()->getValue( 'config.offset' ) );
			$date = $date->toFormat( "%Y-%m-%d 00:00:00" );
		}
		$db = $this->getDBO();
		$q = 'SELECT `product_issue_id`, `publishing_date` FROM `#__citruscart_productissues` WHERE '.
	  		' `product_id` = '.$product_id.' AND '.
	   		' `publishing_date` > \''.$date.'\' '.
	    	' ORDER BY `publishing_date` ASC LIMIT 0,'.$interval;
		$db->setQuery( $q );
		
		$list = $db->loadObjectList();
		$c = count( $list );
		if( $list == false || ( $c  < $interval ) ) // not enough issues -> we need to create the rest of them
		{
			if( $list == false )
			{
				$date_start = $date;
				$to_create = $interval;
			}
			else 
			{
				$date_start = $list[$c-1]->publishing_date;
				$to_create = $interval - $c;
			}
				
			$to_create++;
			for( $i = 1; $i < $to_create; $i++ )
			{
				$q = 'INSERT INTO `#__citruscart_productissues` (`product_id`, `issue_num`, `volume_num`, `publishing_date`) VALUES '.
						 ' ('.$product_id.', \'\', \'\', (SELECT DATE_ADD(\''.$date_start.'\', INTERVAL '.$i.' DAY) ) )';
				$db->setQuery( $q );
				$db->query();
			}
			$last_id = $db->insertid(); // get the latest ID
		}
		else // we have enough entries so pick the last one and go
		{
			$last_id = $list[$interval-1]->product_issue_id;			
		}
		return $last_id;
	}
}
