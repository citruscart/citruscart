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

class CitruscartModelProductDownloadLogs extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
    	$filter          = $this->getState('filter');
        $filter_id	     = $this->getState('filter_id');
        $filter_productfile  = $this->getState('filter_productfile');
        $filter_user       = $this->getState('filter_user');

        if ($filter) 
        {
            $key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.productdownloadlog_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.productfile_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.user_id) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        
		if (strlen($filter_id))
        {
            $query->where('tbl.productdownloadlog_id = '.(int) $filter_id);
       	}
        if (strlen($filter_productfile))
        {
            $query->where('tbl.productfile_id = '.(int) $filter_productfile);
        }
        if (strlen($filter_user))
        {
            $query->where('tbl.user_id = '.(int) $filter_user);
        }
    }
}
