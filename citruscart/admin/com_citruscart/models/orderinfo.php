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

class CitruscartModelOrderInfo extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
       	$filter_orderid     = $this->getState('filter_orderid');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.orderinfo_id) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
       	if ($filter_orderid)
       	{
       	    $query->where('tbl.order_id = '.$this->_db->Quote($filter_orderid));
       	}
    }
        	
    protected function _buildQueryOrder(&$query)
    {
		$order      = $this->_db->escape( $this->getState('order') );
       	$direction  = $this->_db->escape( strtoupper($this->getState('direction') ) );
		if ($order){
       		$query->order("$order $direction");
       	}
       	else{
       			$query->order("tbl.orderinfo_id ASC");
       	}
    }
}