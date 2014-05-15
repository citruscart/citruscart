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

class CitruscartModelProductQuantities extends CitruscartModelBase
{
    public $cache_enabled = false;
     
    protected function _buildQueryWhere(&$query)
    {
        $filter_id	= $this->getState('filter_id');
       	$filter_product	    = $this->getState('filter_productid');       	
        $filter_vendorid	= $this->getState('filter_vendorid');
        $filter_attributes  = $this->getState('filter_attributes');
        $filter_quantity_from   = $this->getState('filter_quantity_from');
        $filter_quantity_to = $this->getState('filter_quantity_to');
        
        $filter_productid  		= $this->getState('filter_productid');
        if (strlen($filter_productid)) {
            $filter_product = $filter_productid;
        }
                
		if (strlen($filter_id))
        {
            $query->where('tbl.productquantity_id = '.(int) $filter_id);
       	}
    	if (strlen($filter_product))
        {
            $query->where('tbl.product_id = '.(int) $filter_product);
       	}
        if (strlen($filter_vendorid))
        {
            $query->where('tbl.vendor_id = '.(int) $filter_vendorid);
        }
        if (strlen($filter_quantity_from))
        {
            $query->where('tbl.quantity >= '.(int) $filter_quantity_from);
        }
        if (strlen($filter_quantity_to))
        {
            $query->where('tbl.quantity <= '.(int) $filter_quantity_to);
        }
        if (strlen($filter_attributes))
        {
        	$query->where(" tbl.product_attributes = '$filter_attributes' ");
       	}
    }
        	
	public function getList($emptyState = true)
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		
		$list = parent::getList($emptyState); 
		
		// If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }
		
		foreach($list as $item)
		{
		    if ($product_attributes = explode( ',', $item->product_attributes ))
	        {
	        	$product_attribute_names = array();
	            foreach ($product_attributes as $pao_id)
	            {
	            	$pao = JTable::getInstance( 'ProductAttributeOptions', 'CitruscartTable' );
	            	$pao->load( $pao_id );
	            	$product_attribute_names[] = JText::_( $pao->productattributeoption_name );
	            }
	            $item->product_attribute_names = implode(', ', $product_attribute_names);
	        }

		}
		return $list;
	}
}
