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

class CitruscartModelZones extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter     = $this->getState('filter');
		$filter_id_from = $this->getState('filter_id_from');
		$filter_id_to   = $this->getState('filter_id_to');
		$filter_name    = $this->getState('filter_name');
		$filter_code    = $this->getState('filter_code');
		$filter_countryid	= $this->getState('filter_countryid');
		$filter_geozoneid     = $this->getState('filter_geozoneid');

		if ($filter)
		{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.zone_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.zone_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.code) LIKE '.$key;
				
			$query->where('('.implode(' OR ', $where).')');
		}
		if (strlen($filter_id_from))
		{
			if (strlen($filter_id_to))
			{
				$query->where('tbl.zone_id >= '.(int) $filter_id_from);
			}
			else
			{
				$query->where('tbl.zone_id = '.(int) $filter_id_from);
			}
		}
		if (strlen($filter_id_to))
		{
			$query->where('tbl.zone_id <= '.(int) $filter_id_to);
		}
		if ($filter_name)
		{
			$key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.zone_name) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
		}
		if ($filter_code)
		{
			$key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_code ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.code) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
		}
		if (strlen($filter_countryid))
		{
			$query->where('tbl.country_id = '.$this->_db->q($filter_countryid));
		}
		if (strlen($filter_geozoneid))
		{
			$query->where('zr.geozone_id = '.$this->_db->q($filter_geozoneid));
		}
	}

	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_countries AS c ON c.country_id = tbl.country_id');

		$filter_geozoneid     = $this->getState('filter_geozoneid');
		if (strlen($filter_geozoneid))
		{
			$query->join('LEFT', '#__citruscart_zonerelations AS zr ON zr.zone_id = tbl.zone_id');
			$query->group( 'tbl.zone_id' );
		}
	}

	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " c.country_name AS country_name ";

		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$query->select( $field );
	}

	public function getList($refresh = false)
	{
		//$list = parent::getList($refresh);

		if (empty( $this->_list))
		{
			$query = $this->getQuery(true);
				
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		$list = $this->_list;
		
		// If no item in the list, return an array()
		if( empty( $list ) ){
			return array();
		}

		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_citruscart&controller=zones&view=zones&task=edit&id='.$item->zone_id;
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
