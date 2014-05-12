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

class CitruscartModelVendors extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
       	$enabled	= $this->getState('filter_enabled');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.vendor_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.vendor_name) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
    	if (strlen($enabled))
        {
          	$query->where('tbl.vendor_enabled = '.$this->_db->Quote($enabled));
       	}
    }
     
}
