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

class CitruscartModelProductCategories extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter_product_id      = $this->getState('filter_product_id');
       	$filter_product_name    = $this->getState('filter_product_name');
       	$filter_category_id     = $this->getState('filter_category_id');
       	$filter_category_name   = $this->getState('filter_category_name');

        if ($filter_product_id)
        {
            $query->where('tbl.product_id = '.$this->_db->Quote($filter_product_id));
        }

        if ($filter_category_id)
        {
            $query->where('tbl.category_id = '.$this->_db->Quote($filter_category_id));
        }

        if ($filter_product_name)
        {
            $key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_product_name ) ) ).'%');
        
            $where = array();
            $where[] = 'LOWER(p.product_name) LIKE '.$key;
            	
            $query->where('('.implode(' OR ', $where).')');
        }
        
        if ($filter_category_name)
        {
            $key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_category_name ) ) ).'%');
        
            $where = array();
            $where[] = 'LOWER(c.category_name) LIKE '.$key;
            	
            $query->where('('.implode(' OR ', $where).')');
        }
        
    }
    
    protected function _buildQueryJoins(&$query)
    {
        $query->join('INNER', '#__citruscart_categories AS c ON c.category_id = tbl.category_id');
        $query->join('INNER', '#__citruscart_products AS p ON p.product_id = tbl.product_id');
    }
    
    protected function _buildQueryFields( &$query )
    {
        $fields = array();
        $fields[] = $this->getState( 'select', 'tbl.*' );
        $fields[] = 'c.*';

        $query->select( $fields );
    }
    
    /**
     * Set basic properties for the item, whether in a list or a singleton
     *
     * @param unknown_type $item
     * @param unknown_type $key
     * @param unknown_type $refresh
     */
    protected function prepareItem( &$item, $key=0, $refresh=false )
    {
        if (empty($item->category_alias) && !empty($item->category_name)) {
            $item->category_alias = JFilterOutput::stringURLSafe($item->category_name);
        }
    }
}