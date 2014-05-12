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

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableProductAttributeOptions extends CitruscartTable 
{
	function CitruscartTableProductAttributeOptions ( &$db ) 
	{
		
		$tbl_key 	= 'productattributeoption_id';
		$tbl_suffix = 'productattributeoptions';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}
	
	/**
	 * Checks row for data integrity.
	 *  
	 * @return unknown_type
	 */
	function check()
	{
		if (empty($this->productattribute_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_ATTRIBUTE_ASSOCIATION_REQUIRED') );
			return false;
		}
        if (empty($this->productattributeoption_name))
        {
            $this->setError( JText::_('COM_CITRUSCART_ATTRIBUTE_OPTION_NAME_REQUIRED') );
            return false;
        }
		return true;
	}
	
    /**
     * Adds context to the default reorder method
     * @return unknown_type
     */
    function reorder($where = '')
    {
        parent::reorder('productattribute_id = '.$this->_db->Quote($this->productattribute_id) );
    }

    /**
     * Run function when saving
     * @see Citruscart/admin/tables/CitruscartTable#save()
     */
    function save($src='', $orderingFilter = '', $ignore = '')
    {
    	if ($return = parent::save( $src, $orderingFilter, $ignore))
    	{
            $pa = JTable::getInstance('ProductAttributes', 'CitruscartTable');
            $pa->load( $this->productattribute_id );
            
            Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
            $helper = CitruscartHelperBase::getInstance( 'product' );
            $helper->doProductQuantitiesReconciliation( $pa->product_id );
    	}
        
    	return $return;
    }
    
    /**
     * Run function when deleting
     * @see Citruscart/admin/tables/CitruscartTable#save()
     */
    function delete( $oid=null, $doReconciliation=true )
    {
        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }
        
        if ($doReconciliation) 
        {
            $pa = JTable::getInstance('ProductAttributes', 'CitruscartTable');
            if ($oid)
            {
                $row = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
                $row->load( $oid );

                $pa->load( $row->productattribute_id );                
            }
            else
            {
                $pa->load( $this->productattribute_id );
            }
            $product_id = $pa->product_id;
        }
        
        if ($return = parent::delete( $oid ))
        {
            DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = DSCModel::getInstance( 'ProductAttributeOptionValues', 'CitruscartModel' );
            $model->setState('filter_option', $this->$k );
            if ($items = $model->getList())
            {
                $table = $model->getTable();
                foreach ($items as $item)
                {
                    if (!$table->delete( $item->productattributeoptionvalue_id ))
                    {
                        $this->setError( $table->getError() );
                    }
                }
            }
            
            if ($doReconciliation) 
            {
                Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
                CitruscartHelperProduct::doProductQuantitiesReconciliation( $product_id );                
            }
        }
        
        return parent::check();
    }
	
}
