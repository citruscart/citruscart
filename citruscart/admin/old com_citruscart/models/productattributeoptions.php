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

class CitruscartModelProductAttributeOptions extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
        $filter          	= $this->getState('filter');
        $filter_id      	= $this->getState('filter_id');
        $filter_attribute   = $this->getState('filter_attribute');
        $filter_parent		= $this->getState('filter_parent');

        if ($filter)
        {
            $key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.productattributeoption_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.productattributeoption_name) LIKE '.$key;
            $where[] = 'LOWER(tbl.productattribute_id) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if (strlen($filter_id))
        {
            $query->where('tbl.productattributeoption_id = '.(int) $filter_id);
        }
        if (strlen($filter_attribute))
        {
            $query->where('tbl.productattribute_id = '.(int) $filter_attribute);
        }
   		if (is_array($filter_parent))
        {
       		$filter_parent = implode(',', $filter_parent);
       		$query->where('tbl.parent_productattributeoption_id IN ('. $filter_parent.')');
       	}
       	else
       	{
       		if(strlen($filter_parent))
        	{
            	$query->where('tbl.parent_productattributeoption_id = '.(int) $filter_parent);
        	}
        }
    }

	protected function _buildQueryJoins(&$query)
	{
		$filter_no_quantity = $this->getState('filter_no_quantity', 0);

		$query->join('LEFT', '#__citruscart_productattributes AS pa ON pa.productattribute_id = tbl.productattribute_id');
		$query->join('LEFT', '#__citruscart_products AS p ON pa.product_id = p.product_id');
	}

    protected function _buildQueryFields( &$query )
    {
    		$fields = array();
       	$fields[] = "tbl.*";
       	$fields[] = "p.product_id, p.product_ships";
        $query->select( $fields );
    }

    public function getNames( $ids )
    {
        $return = array();

        $query = $this->getDBO()->getQuery(true);

        $ids = (array) $ids;
        $filter_id_set = implode("', '", $ids);

        $query->select( "DISTINCT(pao.productattributeoption_name)" );
        $query->from( "#__citruscart_productattributeoptions AS pao" );
        $query->where( "pao.productattributeoption_id IN ('" . $filter_id_set . "')" );

        $db = $this->getDBO();
        $db->setQuery((string) $query);

        $return = $db->loadColumn();
        sort($return);

        return $return;
    }

    public function getList($refresh = false){

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("tbl.*")->from("#__citruscart_productattributeoptions AS tbl");
		$this->_buildQueryFields($query);
		$this->_buildQueryJoins($query);
		$this->_buildQueryWhere($query);
		$db->setQuery($query);
		return $list = $db->loadObjectList();
     }
}
