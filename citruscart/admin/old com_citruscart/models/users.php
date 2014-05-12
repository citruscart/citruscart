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

class CitruscartModelUsers extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
       	$block	 	= $this->getState('filter_block');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');
        $filter_subnum    = $this->getState('filter_subnum');
        $filter_username    = $this->getState('filter_username');
        $filter_email    = $this->getState('filter_email');
        $filter_group    = $this->getState('filter_group');    
                
       	if ($filter)
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
			
			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.name) LIKE '.$key;
			$where[] = 'LOWER(tbl.username) LIKE '.$key;
			$where[] = 'LOWER(tbl.email) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
       	}
        if (strlen($block))
        {
        	$query->where('tbl.block = '.$this->_db->q($block));
       	}
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.id >= '.(int) $filter_id_from);
            }
                else
            {
                $query->where('tbl.id = '.(int) $filter_id_from);
            }
        }
        if (strlen($filter_id_to))
        {
            $query->where('tbl.id <= '.(int) $filter_id_to);
        }
        if ($filter_name)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if ($filter_username)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_username ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.username) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if ($filter_email)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_email ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.email) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        
        if (strlen($filter_group))
        {
            $query->where('g.group_id = '.(int) $filter_group);
        }

       	if (strlen($filter_subnum))
        {
        	$query->where('ui.sub_number LIKE '.$this->_db->q('%'.$filter_subnum.'%'));
       	}

       /*	$app = JFactory::getApplication();
       	$group_id = $app->input->get('id');
       	echo $group_id;
       	exit;*/
    }

	protected function _buildQueryJoins(&$query)
	{	
		$filter_group    = $this->getState('filter_usergroup');
				
		$query->join('LEFT', '#__citruscart_userinfo AS ui ON ui.user_id = tbl.id');
		
		if( strlen( $filter_group ) )
			$query->join('LEFT', '#__citruscart_usergroupxref AS ug ON ( ug.user_id = tbl.id AND ug.group_id = '.( int )$filter_group.')');
		else		
		$query->join('LEFT', '#__citruscart_usergroupxref AS ug ON ug.user_id = tbl.id');
		$query->join('LEFT', '#__citruscart_groups AS g ON ug.group_id = g.group_id');
	}
					
	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " ui.user_info_id AS user_info_id ";
		$field[] = " ui.company AS company ";
		$field[] = " ui.title AS title ";
		$field[] = " ui.last_name AS last_name ";
		$field[] = " ui.first_name AS first_name ";
		$field[] = " ui.middle_name AS middle_name ";
		$field[] = " ui.phone_1 AS phone_1 ";
		$field[] = " ui.phone_2 AS phone_2 ";
		$field[] = " ui.fax AS fax ";
		$field[] = " ui.sub_number AS sub_number ";
		$field[] = " g.group_id ";
		$field[] = " g.group_name ";
		$field[] = " g.group_description ";
									
		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$query->select( $field );
	}
	
	public function getSurrounding( $id )
	{
		$return = array();
		$return["prev"] = '';
		$return["next"] = '';
	
		if (empty($id))
		{
			return $return;
		}
	
		$prev = $this->getState('prev');
		$next = $this->getState('next');
		if (strlen($prev) || strlen($next))
		{
			$return["prev"] = $prev;
			$return["next"] = $next;
			return $return;
		}
	
		$db = $this->getDBO();
		$key = $this->getTable()->getKeyName();
	
		$this->setState('select', 'tbl.' . $key );
		$query = $this->getQuery( true );
	
		$query->select( '@rownum := @rownum+1 as rownum' );
		$query->join( ' ', ' (SELECT @rownum := 0) r ' );
	
		$rowset_query = (string) $query;
		
		$db->setQuery($rowset_query);
		
		$rowset = $db->loadObjectList();
	
		/*$q2 = "
		 SELECT x.rownum INTO @midpoint FROM (
		 		$rowset_query
		 ) x WHERE x.$key = '$id';
		";
		$db->setQuery( $q2 );
		$db->query();
		 
	
		$q2_5 = "SELECT @midpoint;
		";
		$db = JFactory::getDBO();
		$db->setQuery( $q2_5 );
		$id_rownum = $db->loadResult();
		echo "<p>Row Number of this ID:</p>". Publications::dump( $id_rownum );
	
	
		/*$q3 = "
		SELECT x.* FROM (
				$rowset_query
		) x
		WHERE x.rownum BETWEEN @midpoint - 1 AND @midpoint + 1;
		";
		$db->setQuery( $q3 );
		$rowset = $db->loadObjectList();
		*/
		$count = count($rowset);
	
		$found = false;
		$prev_id = '';
		$next_id = '';
	
		JArrayHelper::sortObjects( $rowset, 'rownum', '1' );
	
		for ($i=0; $i < $count && empty($found); $i++)
		{
		$row = $rowset[$i];
		if ($row->$key == $id)
		{
		$found = true;
		$prev_num = $i - 1;
		$next_num = $i + 1;
		if (!empty($rowset[$prev_num]->$key)) {
		$prev_id = $rowset[$prev_num]->$key;
		}
		if (!empty($rowset[$next_num]->$key)) {
			$next_id = $rowset[$next_num]->$key;
		}
		}
		}
	
		$return["prev"] = $prev_id;
		$return["next"] = $next_id;
		return $return;
	}	
	
	public function getList($refresh = false)
	{				
		if (empty( $this->_list ))
		{
			$query = $this->getQuery(true);
					 
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		
		$list = $this->_list;
		
		foreach($list as $item)
		{		
			$item->link = 'index.php?option=com_citruscart&controller=users&view=users&task=view&id='.$item->id;
			$item->link_createorder = 'index.php?option=com_citruscart&view=orders&task=add&userid='.$item->id;
		}
		return $list;
	}
			
	
}
