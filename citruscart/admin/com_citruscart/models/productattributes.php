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

class CitruscartModelProductAttributes extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
    	$filter          		= $this->getState('filter');
        $filter_id	     		= $this->getState('filter_id');
        $filter_product  		= $this->getState('filter_product');
        $filter_parent_option	= $this->getState('filter_parent_option');

        $filter_productid  		= $this->getState('filter_productid');
        if (strlen($filter_productid)) {
            $filter_product = $filter_productid;
        }

        if ($filter)
        {
            $key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.productattribute_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.productattribute_name) LIKE '.$key;
            $where[] = 'LOWER(tbl.product_id) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
		if(is_array($filter_id))
		{
			foreach($filter_id as &$fid){
				$fid = (int)$fid;
			}
			$query->where('tbl.productattribute_id IN ('. implode(',', $filter_id).')');
		}
		else
		{
			if (strlen($filter_id))
	        {
	            $query->where('tbl.productattribute_id = '.(int) $filter_id);
	       	}
		}

        if (strlen($filter_product))
        {
            $query->where('tbl.product_id = '.(int) $filter_product);
        }

    	if (is_array($filter_parent_option))
        {
       		$filter_parent_option = implode(',', $filter_parent_option);
       		if ($filter_parent_option) {
       		    $query->where('tbl.parent_productattributeoption_id IN ('. $filter_parent_option.')');
       		}
       	}
       	else
       	{
       		if(strlen($filter_parent_option))
        	{
            	$query->where('tbl.parent_productattributeoption_id = '.(int) $filter_parent_option);
        	}
        }
    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();
        $field[] = "
        (
            SELECT GROUP_CONCAT(options.productattributeoption_name ORDER BY options.ordering ASC SEPARATOR ', ')
            FROM
                #__citruscart_productattributeoptions AS options
            WHERE
                options.productattribute_id = tbl.productattribute_id
        )
        AS option_names_csv ";

        $query->select( $this->getState( 'select', 'tbl.*' ) );
        $query->select( $field );
    }

    public function getList($refresh = false, $getEav = true, $options = array()){
    	$db= JFactory::getDbo();
    	$query = $db->getQuery(true);
    	$query ->select('tbl.*')->from("#__citruscart_productattributes as tbl");
    	$this->_buildQueryWhere($query);
    	$this->_buildQueryFields($query);
    	$db->setQuery($query);
    	$list = $db->loadObjectList();
    	return $list;
    }


}
