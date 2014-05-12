<?php
/**
 * @version	1.5
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelProductAttributeOptionValues extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
        $filter          	= $this->getState('filter');
        $filter_id      	= $this->getState('filter_id');
        $filter_option   = $this->getState('filter_option');
        $filter_product  		= $this->getState('filter_product');
        $filter_field = $this->getState('filter_field');

        if ($filter)
        {
            $key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.productattributeoption_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.productattributeoptionvalue_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.productattributeoptionvalue_value) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if (strlen($filter_id))
        {
            $query->where('tbl.productattributeoptionvalue_id = '.(int) $filter_id);
        }
        if (strlen($filter_option))
        {
            $query->where('tbl.productattributeoption_id = '.(int) $filter_option);
        }

        if (strlen($filter_product))
        {
            $query->where('p.product_id = '.(int) $filter_product);
        }

        if (strlen($filter_field))
        {
            $query->where('tbl.productattributeoptionvalue_field = '. $this->getDBO()->Quote($filter_field) );
        }
    }

    protected function _buildQueryJoins(&$query)
    {
        $filter_product = $this->getState('filter_product');
        if (strlen($filter_product))
        {
            $query->join('INNER', '#__citruscart_productattributeoptions AS pao ON pao.productattributeoption_id = tbl.productattributeoption_id');
            $query->join('INNER', '#__citruscart_productattributes AS pa ON pa.productattribute_id = pao.productattribute_id');
            $query->join('INNER', '#__citruscart_products AS p ON pa.product_id = p.product_id');
        }
    }
}
