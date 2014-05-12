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

class CitruscartModelUserGroups extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
        $filter_user = $this->getState('filter_user');
        $filter_group = $this->getState('filter_group');
		        
		if (strlen($filter_user))
        {
            $query->where('tbl.user_id = '.(int) $filter_user);
       	}

        if (strlen($filter_group))
        {
            $query->where('tbl.group_id = '.(int)$filter_group);
        }
    }
    
    /* protected function buildQuery(){
    	
    	$db = JFactory::getDbo();
    	
    } */
	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " tbl.* ";		
		$field[] = " g.ordering as ordering ";
		
		$query->select( $field );
	}    
    
	protected function _buildQueryJoins(&$query)
	{		
		$query->join('LEFT', '#__citruscart_groups AS g ON g.group_id = tbl.group_id');    		
	}
    
	public function getList($refresh = false)
	{
		//$list = parent::getList($refresh); 
		if (empty( $this->_list ) || $reload)
		{
			$query = $this->getQuery(true);
				
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		$list = $this->_list;
				        
		return $list;
	}
}
