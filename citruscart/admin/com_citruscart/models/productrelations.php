
<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelProductRelations extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter_id = $this->getState('filter_id');
		$filter_product = $this->getState('filter_product');
		$filter_productid = $this->getState('filter_productid');
		$filter_relation = $this->getState('filter_relation');
		$filter_relations = $this->getState('filter_relations');
		$filter_product_from = $this->getState('filter_product_from');
		$filter_product_to = $this->getState('filter_product_to');

		if (strlen($filter_id))
		{
			$query->where('tbl.productrelation_id = '.(int) $filter_id);
		}

		if (strlen($filter_product))
		{
			$query->where(
                '(tbl.product_id_from = '.(int) $filter_product .' OR tbl.product_id_to = '.(int) $filter_product .' )'
                );
		}

		if (strlen($filter_productid))
		{
		    $query->where(
		            '(tbl.product_id_from = '.(int) $filter_productid .' OR tbl.product_id_to = '.(int) $filter_productid .' )'
		    );
		}

		if (strlen($filter_product_from))
		{
			$query->where('tbl.product_id_from = '.(int) $filter_product_from);
		}

		if (strlen($filter_product_to))
		{
			$query->where('tbl.product_id_to = '.(int) $filter_product_to);
		}

		if (strlen($filter_relation))
		{
			$query->where("tbl.relation_type = '$filter_relation'");
		}

		if (is_array($filter_relations))
		{
			$query->where("tbl.relation_type IN ('".implode("', '", $filter_relations)."')");
		}
	}

	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_products AS p_from ON p_from.product_id = tbl.product_id_from');
		$query->join('LEFT', '#__citruscart_products AS p_to ON p_to.product_id = tbl.product_id_to');
	}

	protected function _buildQueryFields(&$query)
	{
		Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
		$date = JFactory::getDate()->toSql();
		$filter_product = $this->getState('filter_product');
		$user = CitruscartHelperBase::getInstance( 'user' );
		if( strlen( $filter_product ) )
			$default_group = $user->getUserGroup( JFactory::getUser()->id, (int)$filter_product );
		else
			$default_group = Citruscart::getInstance()->get('default_user_group', '1');

		$fields = array();
		$fields[] = " p_from.product_name as product_name_from ";
		$fields[] = " p_from.product_sku as product_sku_from ";
		$fields[] = " p_from.product_model as product_model_from ";
		$fields[] = "
            (
            SELECT
                prices.product_price
            FROM
                #__citruscart_productprices AS prices
            WHERE
                prices.product_id = tbl.product_id_from
                AND prices.group_id = '$default_group'
                AND prices.product_price_startdate <= '$date'
                AND (prices.product_price_enddate >= '$date' OR prices.product_price_enddate = '0000-00-00 00:00:00' )
                ORDER BY prices.price_quantity_start ASC
            LIMIT 1
            )
        AS product_price_from ";

		$fields[] = " p_to.product_name as product_name_to ";
		$fields[] = " p_to.product_sku as product_sku_to ";
		$fields[] = " p_to.product_model as product_model_to ";
		$fields[] = "
            (
            SELECT
                prices.product_price
            FROM
                #__citruscart_productprices AS prices
            WHERE
                prices.product_id = tbl.product_id_to
                AND prices.group_id = '$default_group'
                AND prices.product_price_startdate <= '$date'
                AND (prices.product_price_enddate >= '$date' OR prices.product_price_enddate = '0000-00-00 00:00:00' )
                ORDER BY prices.price_quantity_start ASC
            LIMIT 1
            )
        AS product_price_to ";

		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$query->select( $fields );
	}

	public function getList($refresh = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = $this->_buildQuery();
		$db->setQuery($query);
		$list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		$this->_list = $list;
		//$list = parent::getList($refresh);

		// If no item in the list, return an array()
		if( empty( $list ) ){
			return array();
		}

		return $list;
	}
}
