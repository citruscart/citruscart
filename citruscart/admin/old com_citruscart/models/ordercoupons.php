<?php
/**
 * @version	1.5
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelOrderCoupons extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter             = $this->getState('filter');
       	$filter_orderid     = $this->getState('filter_orderid');
       	$filter_coupon      = $this->getState('filter_coupon');
       	$filter_user        = $this->getState('filter_user');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.ordercoupon_id) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
        if ($filter_orderid)
        {
            $query->where('tbl.order_id = '.$this->_db->Quote($filter_orderid));
        }

        if ($filter_coupon)
        {
            $query->where('tbl.coupon_id = '.$this->_db->Quote($filter_coupon));
        }

        if ($filter_user)
        {
            $query->where('o.user_id = '.$this->_db->Quote($filter_user));
        }
    }
    
    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_orders AS o ON o.order_id = tbl.order_id');
    }
}