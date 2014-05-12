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

class CitruscartTableProductAttributes extends CitruscartTable
{
	function __construct( &$db )
	{

		$tbl_key 	= 'productattribute_id';
		$tbl_suffix = 'productattributes';
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
		if (empty($this->product_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_ASSOCIATION_REQUIRED') );
			return false;
		}
        if (empty($this->productattribute_name))
        {
            $this->setError( JText::_('COM_CITRUSCART_ATTRIBUTE_NAME_REQUIRED') );
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
        parent::reorder('product_id = '.$this->_db->Quote($this->product_id) );
    }

    /**
     * Run function after saving
     */
    function save($src='', $orderingFilter = '', $ignore = '')
    {
        if ($return = parent::save($src, $orderingFilter, $ignore))
        {
            Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
            $helper = CitruscartHelperBase::getInstance( 'product' );
            $helper->doProductQuantitiesReconciliation( $this->product_id, '0' );
        }

        return $return;
    }

    /**
     * Run function after deleteing
     */
    function delete( $oid=null, $doReconciliation=true )
    {
        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }

        if ($doReconciliation)
        {
            if ($oid)
            {
                $row = JTable::getInstance('ProductAttributes', 'CitruscartTable');
                $row->load( $oid );
                $product_id = $row->product_id;
            }
            else
            {
                $product_id = $this->product_id;
            }
        }

        $db = $this->getDBO();
        $db->setQuery('SET foreign_key_checks = 0;');
        $db->query();

        if ($return = parent::delete( $oid ))
        {
            DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = DSCModel::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
            $model->setState('filter_attribute', $this->$k );
            if ($items = $model->getList())
            {
                $table = $model->getTable();
                foreach ($items as $item)
                {
                    if (!$table->delete( $item->productattributeoption_id ))
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

        $db->setQuery('SET foreign_key_checks = 1;');
        $db->query();

        return parent::check();
    }

}
