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

class CitruscartModelTaxrates extends CitruscartModelBase 
{
	protected function _buildQueryWhere(&$query)
	{
		$filter               = $this->getState('filter');
		$filter_id            = $this->getState('filter_id');
		$filter_geozone       = $this->getState('filter_geozone');
		$filter_taxclassid	  = $this->getState('filter_taxclassid');
		
		if ($filter) 
		{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.tax_rate_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.geozone_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.tax_class_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.tax_rate) LIKE '.$key;
			$where[] = 'LOWER(tbl.tax_rate_description) LIKE '.$key;
				
			$query->where('('.implode(' OR ', $where).')');
		}
		
		if (strlen($filter_id))
		{
			$query->where('tbl.tax_rate_id = '.$this->_db->q($filter_id));
		}
		
	    if (strlen($filter_taxclassid))
        {
            $query->where('tbl.tax_class_id = '.$this->_db->q($filter_taxclassid));
        }
        
	    if (strlen($filter_geozone))
        {
            $query->where('tbl.geozone_id = '.$this->_db->q($filter_geozone));
        }
	}
    
	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_geozones AS g ON g.geozone_id = tbl.geozone_id');
		$query->join('LEFT', '#__citruscart_taxclasses AS c ON c.tax_class_id = tbl.tax_class_id');
	}
	
	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " g.geozone_name AS geozone_name ";
		$field[] = " c.tax_class_name AS taxclass_name ";
		
		$query->select( $this->getState( 'select', 'tbl.*' ) );		
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
		
		
		// If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }
		
		foreach($list as $item)
		{
			$item->link = "index.php?option=com_citruscart&controller=taxrates&view=taxrates&tmpl=component&task=edit&classid=$item->tax_class_id&id=$item->tax_rate_id";
		}
		
		return $list;
	}

	/*
	 * Gets data about taxes at a certain level
	 * 
	 * @params $level						level of taxes
	 * @params $geozone_id 			ID of a geozone (null means all)
	 * @params $tax_class_id 		ID of a tax class (null means all)
	 * @params $tax_type				for the future use
	 * @params $update					update cached info
	 * 
	 * @return Array with rows from Citruscart_taxrates table
	 */
	function getTaxRatesAtLevel( $level, $geozone_id = null, $tax_class_id = null, $tax_type = null, $update = false )
	{
		static $taxrates = null; // static array for caching results
		if( $taxrates === null )
			$taxrates = array();
			
		if( !$geozone_id )
			$geozone_id = -1;
		if( !$tax_class_id )
			$tax_class_id = -1;
			
		if( isset( $taxrates[$tax_class_id][$geozone_id][$level] ) && !$update )
			return $taxrates[$tax_class_id][$geozone_id][$level];
	
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$db = JFactory::getDbo();
		$q = new CitruscartQuery();
		$q->select( array( 'tax_rate_id', 'geozone_id', 'tax_class_id', 'tax_rate', 'tax_rate_description', 'level' ) );
		$q->from( '#__citruscart_taxrates' );
		$q->where( 'level = '.( int )$level );
		if( $geozone_id > 0 )
			$q->where( 'geozone_id = '.( int )$geozone_id );
		if( $tax_class_id > 0 )
			$q->where( 'tax_class_id = '.( int )$tax_class_id );
		$q->order( 'tax_rate_description' );
		
		$db->setQuery( $q );
		$items = $db->loadObjectList();
		
		$taxrates[$tax_class_id][$geozone_id][$level] = $items;
		return $taxrates[$tax_class_id][$geozone_id][$level];
	}
	
}
