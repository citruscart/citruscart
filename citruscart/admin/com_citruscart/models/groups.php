<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelGroups extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter     = $this->getState('filter');
		$filter_id_from	= $this->getState('filter_id_from');
		$filter_id_to	= $this->getState('filter_id_to');
		$filter_name	= $this->getState('filter_name');
		$enabled		= $this->getState('filter_enabled');
						
		if ($filter)
		{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.group_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.group_name) LIKE '.$key;
				
			$query->where('('.implode(' OR ', $where).')');
		}
		 
		if (strlen($filter_id_from))
		{
			if (strlen($filter_id_to))
			{
				$query->where('tbl.group_id >= '.(int) $filter_id_from);
			}
			else
			{
				$query->where('tbl.group_id = '.(int) $filter_id_from);
			}
		}
		if (strlen($filter_id_to))
		{
			$query->where('tbl.group_id <= '.(int) $filter_id_to);
		}
								
		if (strlen($filter_name))
		{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
			$query->where('LOWER(tbl.group_name) LIKE '.$key);
		}
	}

	protected function _buildQueryOrder( &$query )
	{
		$order      = $this->_db->escape( $this->getState('order') );
		$direction  = $this->_db->escape( strtoupper($this->getState('direction') ) );
		if ($order){
			$query->order("$order $direction");
		}
		else{
			$query->order("tbl.ordering ASC");
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
			$item->link = 'index.php?option=com_citruscart&controller=groups&view=groups&task=edit&id='.$item->group_id;
		}
		return $list;
	}
	
	/**
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCache()
	{
	    parent::clearCache();
	    self::clearCacheAuxiliary();
	}
	
	/**
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCacheAuxiliary()
	{
	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
	
	    $model = DSCModel::getInstance('UserGroups', 'CitruscartModel');
	    $model->clearCache();
	}
}
