<?php
/**
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelOrderTaxRates extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     	= $this->getState('filter');
       	$filter_orderid	= $this->getState('filter_orderid');
        
       	if ($filter)
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.ordertaxrate_id) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}

       	if ($filter_orderid)
       	{
        	$query->where('tbl.order_id = '.$this->_db->Quote($filter_orderid));
       	}
    }
}