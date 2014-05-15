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


class CitruscartModelShippingMethods extends CitruscartModelBase
{
    public $cache_enabled = false;

    protected function _buildQueryWhere(&$query)
    {
		$filter         = $this->getState('filter');
		$filter_id_from = $this->getState('filter_id_from');
		$filter_id_to   = $this->getState('filter_id_to');
		$filter_name    = $this->getState('filter_name');
		$filter_enabled = $this->getState('filter_enabled');
		$filter_taxclass = $this->getState('filter_taxclass');
		$filter_order = $this->getState('filter_order');
		$filter_shippingtype = $this->getState('filter_shippingtype');
		$filter_subtotal = $this->getState('filter_subtotal');

        if ($filter)
        {
            $key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.shipping_method_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.shipping_method_name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }

        if (strlen($filter_enabled))
        {
            $query->where('tbl.shipping_method_enabled = '.$filter_enabled);
        }

        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.shipping_method_id >= '.(int) $filter_id_from);
            }
                else
            {
                $query->where('tbl.shipping_method_id = '.(int) $filter_id_from);
            }
        }

        if (strlen($filter_id_to))
        {
            $query->where('tbl.shipping_method_id <= '.(int) $filter_id_to);
        }

        if (strlen($filter_name))
        {
            $key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
            $query->where('LOWER(tbl.shipping_method_name) LIKE '.$key);
        }

        if (strlen($filter_taxclass))
        {
            $query->where('tbl.tax_class_id = '.(int) $filter_taxclass);
        }

        if (strlen($filter_shippingtype))
        {
            $query->where('tbl.shipping_method_type = '.(int) $filter_shippingtype);
        }

    	if ( strlen($filter_subtotal ))
        {
            $query->where('tbl.subtotal_minimum <= '. $filter_subtotal);
            $query->where('( ( tbl.subtotal_maximum = 0.00000 ) OR ( tbl.subtotal_maximum = -1 ) OR ( ( tbl.subtotal_maximum != 0.00000 AND tbl.subtotal_maximum != -1 ) AND ( tbl.subtotal_maximum >= '.$filter_subtotal.' ) ) )');
        }
    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_taxclasses AS taxclass ON tbl.tax_class_id = taxclass.tax_class_id');
    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();
        $field[] = " taxclass.tax_class_name ";

        $query->select( $this->getState( 'select', 'tbl.*' ) );
        $query->select( $field );
    }

    public function getList($refresh = false)
    {
        //$list = parent::getList($refresh);

        $db = JFactory::getDbo();
        $query=$db->getQuery(true);
        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryOrder($query);
        $db->setQuery($query);
        $list = $db->loadObjectList();

        // If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }
        foreach($list as $item)
        {
            $item->link = 'index.php?option=com_citruscart&view=shipping&task=view&id='.@$_GET['id'].'&shippingTask=view&sid='.$item->shipping_method_id;
        }
        return $list;
    }
}
