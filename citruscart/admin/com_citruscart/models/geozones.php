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

class CitruscartModelGeozones extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter = $this->getState('filter');
		$filter_id_from = $this->getState('filter_id_from');
		$filter_id_to   = $this->getState('filter_id_to');
		$filter_name    = $this->getState('filter_name');
		$filter_geozonetype    = $this->getState('filter_geozonetype');

		if ($filter)
		{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.geozone_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.geozone_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.geozone_description) LIKE '.$key;
			$where[] = 'LOWER(tbl.geozonetype_id) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
		}
		if (strlen($filter_id_from))
		{
			if (strlen($filter_id_to))
			{
				$query->where('tbl.geozone_id >= '.(int) $filter_id_from);
			}
			else
			{
				$query->where('tbl.geozone_id = '.(int) $filter_id_from);
			}
		}
		if (strlen($filter_id_to))
		{
			$query->where('tbl.geozone_id <= '.(int) $filter_id_to);
		}
		if ($filter_name)
		{
			$key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.geozone_name) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
		}
		if (strlen($filter_geozonetype))
		{
			$query->where('tbl.geozonetype_id = '.$this->_db->q($filter_geozonetype));
		}

	}

	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_geozonetypes AS t ON t.geozonetype_id = tbl.geozonetype_id');
	}

	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " t.geozonetype_name";
		
		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$values = $query->select( $field );
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
				
		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_citruscart&controller=geozones&view=geozones&task=edit&id='.$item->geozone_id;
			$item->link_zones = 'index.php?option=com_citruscart&view=geozones&task=selectzones&tmpl=component&id='.$item->geozone_id;
			$item->link_plugins = 'index.php?option=com_citruscart&view=geozones&task=selectplugins&type='.$item->geozonetype_id.'&tmpl=component&id='.$item->geozone_id;
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
	
	    $model = DSCModel::getInstance('ZoneRelations', 'CitruscartModel');
	    $model->clearCache();
	}
}
