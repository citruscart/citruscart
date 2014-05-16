<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelOrderHistory extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter             = $this->getState('filter');
       	$filter_orderid     = $this->getState('filter_orderid');
       	$filter_notified    = $this->getState('filter_notified');

       	if ($filter)
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.orderhistory_id) LIKE '.$key;

			$query->where('('.implode(' OR ', $where).')');
       	}

        if ($filter_orderid)
        {
            $query->where('tbl.order_id = '.$this->_db->q($filter_orderid));
        }

        if ($filter_notified)
        {
            $query->where('tbl.notify_customer = '.$this->_db->q($filter_notified));
        }


    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_orderstates AS orderstates ON orderstates.order_state_id = tbl.order_state_id');

    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();

        $field[] = " tbl.* ";
        $field[] = " orderstates.* ";

        $query->select( $field );
    }

    public function getList($refresh=false){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("tbl.*")->from("#__citruscart_orderhistory as tbl");
		$this->_buildQueryJoins($query);
		$this->_buildQueryFields($query);
		$this->_buildQueryWhere($query);
		$db->setQuery($query);
		return $result = $db->loadObjectList();
    }

}