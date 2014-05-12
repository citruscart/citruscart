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

class CitruscartModelOrderItemAttributes extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_orderitemid = $this->getState('filter_orderitemid');
        
       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.orderitemattribute_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.orderitemattribute_name) LIKE '.$key;
						
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
        if ($filter_orderitemid)
        {
            $query->where('tbl.orderitem_id = '.$filter_orderitemid);
        }
       	
    }
    
}