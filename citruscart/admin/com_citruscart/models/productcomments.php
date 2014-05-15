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

class CitruscartModelProductComments extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter_product  = $this->getState('filter_product');
		$filter_productid  = $this->getState('filter_productid');
		$filter_id_from	= $this->getState('filter_id_from');
        $filter_id_to	= $this->getState('filter_id_to');
        $filter_name	= $this->getState('filter_name');
        $filter_enabled		= $this->getState('filter_enabled');
        $filter_reported     = $this->getState('filter_reported');

		if (strlen($filter_product))
        {
            $query->where('tbl.product_id = '.(int) $filter_product);
        }

        if (strlen($filter_productid))
        {
            $query->where('tbl.product_id = '.(int) $filter_productid);
        }

		if (strlen($filter_id_from))
        {
        	if (strlen($filter_id_to))
        	{
        		$query->where('tbl.productcomment_id >= '.(int) $filter_id_from);
        	}
        		else
        	{
        		$query->where('tbl.productcomment_id = '.(int) $filter_id_from);
        	}
       	}

		if (strlen($filter_id_to))
        {
        	$query->where('tbl.productcomment_id <= '.(int) $filter_id_to);
       	}

        if(strlen($filter_name))
        {
        	$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
        	$query->where('LOWER(p.product_name) LIKE '.$key);
        }

		if (strlen($filter_enabled))
        {
        	$query->where('tbl.productcomment_enabled = '.$this->_db->q($filter_enabled));
       	}

       	if (strlen($filter_reported))
        {
            if ($filter_reported > 0)
            {
                $query->where('tbl.reported_count > 0');
            }
            else
            {
                $query->where('tbl.reported_count = 0');
            }
        }
	}

	/**
	 * for joining tables products and users
	 */
	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_products AS p ON p.product_id = tbl.product_id');
		$query->join('LEFT', '#__users AS m ON m.id = tbl.user_id');
	}

	protected function _buildQueryFields(&$query)
	{
		$field = array();

		$field[] = " p.product_name AS product_name ";
		$field[] = " m.name AS username ";

		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$query->select( $field );

	}

	public function getList($refresh = false)
	{
		
		if (empty( $this->_list ))
		{
			$query = $this->getQuery(true);
		
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		$list = $this->_list;
		
		foreach($list as $item)
		{

			$item->link = 'index.php?option=com_citruscart&view=productcomments&task=edit&id='.$item->productcomment_id;
		}
        return $list;
	}
}