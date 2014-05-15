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
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelCredits extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_type    = $this->getState('filter_type');
        $filter_user    = $this->getState('filter_user');
        $filter_userid  = $this->getState('filter_userid');
        $filter_enabled  = $this->getState('filter_enabled');
        $filter_orderid = $this->getState('filter_orderid');
        $filter_withdraw = $this->getState('filter_withdraw');
        $filter_date_from   = $this->getState('filter_date_from');
        $filter_date_to     = $this->getState('filter_date_to');
        $filter_datetype    = $this->getState('filter_datetype');
        $filter_amount_from  = $this->getState('filter_amount_from');
        $filter_amount_to    = $this->getState('filter_amount_to');
        
       	if ($filter) 
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.credit_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.credit_comments) LIKE '.$key;
			$where[] = 'LOWER(tbl.credit_code) LIKE '.$key;
			$where[] = 'LOWER(tbl.credit_type) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');			
       	}
       	
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.credit_id >= '.(int) $filter_id_from);
            }
                else
            {
                $query->where('tbl.credit_id = '.(int) $filter_id_from);
            }
        }
        
        if (strlen($filter_id_to))
        {
            $query->where('tbl.credit_id <= '.(int) $filter_id_to);
        }
        
        if ($filter_type) 
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_type ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.credittype_code) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }

        if ($filter_user) 
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.user_id) LIKE '.$key;
            $where[] = 'LOWER(u.name) LIKE '.$key;
            $where[] = 'LOWER(u.email) LIKE '.$key;
            $where[] = 'LOWER(u.username) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');         
        }
        
        if ($filter_userid) 
        {
            $query->where('tbl.user_id = '.$filter_userid);
        }

        if (strlen($filter_orderid))
        {
            $query->where('tbl.order_id = '.$this->_db->q($filter_orderid));
        }

        if (strlen($filter_withdraw))
        {
            $query->where('tbl.credit_withdrawable = '.$this->_db->q($filter_withdraw));
        }
        
        if (strlen($filter_enabled))
        {
            $query->where('tbl.credit_enabled = '.$this->_db->q($filter_enabled));
        }
        
        if (strlen($filter_date_from))
        {
            switch ($filter_datetype)
            {
                case "created":
                default:
                    $query->where("tbl.created_date >= '".$filter_date_from."'");
                  break;
            }
        }
        
        if (strlen($filter_date_to))
        {
            switch ($filter_datetype)
            {
                case "created":
                default:
                    $query->where("tbl.created_date <= '".$filter_date_to."'");
                  break;
            }
        }
        
        if (strlen($filter_amount_from))
        {
            $query->having("tbl.credit_amount >= '". $filter_amount_from ."'");
        }
        
        if (strlen($filter_amount_to))
        {
            $query->having("tbl.credit_amount <= '". $filter_amount_to ."'");
        }
    }
    
    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__users AS u ON u.id = tbl.user_id');
        $query->join('LEFT', '#__citruscart_credittypes AS ct ON ct.credittype_code = tbl.credittype_code');
    }
    
    protected function _buildQueryFields(&$query)
    {
        $field = array();

        $field[] = " tbl.* ";
        $field[] = " u.name AS user_name ";
        $field[] = " u.username AS user_username "; 
        $field[] = " u.email ";
        $field[] = " ct.credittype_name";

        $query->select( $field );
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
			$item->link = 'index.php?option=com_citruscart&controller=credits&view=credits&task=edit&id='.$item->credit_id;
		}
		return $list;
	}
}
