<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelCountries extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');
        $filter_code2    = $this->getState('filter_code2');
        $filter_code3    = $this->getState('filter_code3');
        $filter_enabled  = $this->getState('filter_enabled');
       	
       	if ($filter) 
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.country_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.country_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.country_isocode_2) LIKE '.$key;
			$where[] = 'LOWER(tbl.country_isocode_3) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');			
       	}
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.country_id >= '.(int) $filter_id_from);
            }
                else
            {
                $query->where('tbl.country_id = '.(int) $filter_id_from);
            }
        }
        if (strlen($filter_id_to))
        {
            $query->where('tbl.country_id <= '.(int) $filter_id_to);
        }
        if ($filter_name) 
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.country_name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if (strlen($filter_enabled))
        {
            $query->where('tbl.country_enabled = '.$this->_db->q($filter_enabled));
        }
        if ($filter_code2) 
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_code2 ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.country_isocode_2) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if ($filter_code3) 
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_code3 ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.country_isocode_3) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    protected function _buildQueryOrder(&$query)
    {
        $order      = $this->_db->escape( $this->getState('order') );
        $direction  = $this->_db->escape( strtoupper( $this->getState('direction') ) );

        if ($order == 'ordering')
        {
            $query->order("$order $direction");
            $query->order('ordering ASC');
        }
            elseif (strlen($order))
        {
            $query->order("$order $direction");
        }
    }
    
	public function getList($refresh = false)
	{
		//$list = parent::getList($refresh); 
		
		if (empty( $this->_list ))
		{
			$query = $this->getQuery(true);
				
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		$list = $this->_list;
						
		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_citruscart&controller=countries&view=countries&task=edit&id='.$item->country_id;
		}
		return $list;
	}
	
	public function getDefault()
	{
	    $query = new CitruscartQuery();
	    $query->select( 'tbl.*' );
	    $query->from( $this->getTable()->getTableName() . " AS tbl" );
	    $query->where( "tbl.country_enabled = '1'" );
	    $query->order( "tbl.ordering ASC" );
	    
	    $db = $this->getDBO();
	    $db->setQuery( (string) $query, 0, 1 );
	    if (!$results = $db->loadObjectList())
	    {
	        return false;
	    }
	    
	    return $results[0];
	}

}
