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
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTableEav', 'tables._baseeav' );

class CitruscartTableOrderItems extends CitruscartTableEav 
{
	function CitruscartTableOrderItems ( &$db ) 
	{
		$tbl_key 	= 'orderitem_id';
		$tbl_suffix = 'orderitems';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		$this->_linked_table = 'products';
		$this->_linked_table_key_name = 'product_id';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	public function check()
	{
        $nullDate	= $this->_db->getNullDate();
		if (empty($this->modified_date) || $this->modified_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->modified_date = $date->toSql();
		}
		
	    // be sure that product_attributes is sorted numerically
        if ($product_attributes = explode( ',', $this->orderitem_attributes ))
        {
            sort($product_attributes);
            $this->orderitem_attributes = implode(',', $product_attributes);
        }
        
		return true;
	}
	
	public function store( $updateNulls=false )
	{
		$this->_linked_table_key = $this->product_id;
		return parent::store($updateNulls);
	}
	
	public function delete( $oid=null )
	{
	    if ($attributes = $this->getAttributes( $oid )) 
	    {
	        DSCTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
	        $table = DSCTable::getInstance('OrderItemAttributes', 'CitruscartTable');
	        foreach ($attributes as $attribute) 
	        {
	            if (!$table->delete( $attribute->orderitemattribute_id )) 
	            {
	                $this->setError( $table->getError() );
	            }
	        }
	    }
	    
	    $deleteItem = parent::delete( $oid );
	    
	    return parent::check();
	}
	
	public function getAttributes( $oid=null )
	{
	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }
	    
	    if (empty($this->$k)) 
	    {
	        return array();
	    }
	    
	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
	    $model = DSCModel::getInstance( 'OrderitemAttributes', 'CitruscartModel' );
	    $model->setState('filter_orderitemid', $this->$k );
	    return $model->getList();
	}
}
