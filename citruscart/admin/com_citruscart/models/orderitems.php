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

Citruscart::load( 'CitruscartModelEav', 'models._baseeav' );

class CitruscartModelOrderItems extends CitruscartModelEav
{

    protected function _buildQueryWhere(&$query)
    {
       	$filter     	= $this->getState('filter');
       	$filter_orderid	= $this->getState('filter_orderid');
       	$filter_userid  = $this->getState('filter_userid');
        $filter_date_from   = $this->getState('filter_date_from');
        $filter_date_to     = $this->getState('filter_date_to');
        $filter_datetype    = $this->getState('filter_datetype');
        $filter_recurs  = $this->getState('filter_recurs');
        $filter_productid  = $this->getState('filter_productid');
        $filter_productname  = $this->getState('filter_product_name');
        $filter_manufacturer_name  = $this->getState('filter_manufacturer_name');
        $filter_manufacturer_id  = $this->getState('filter_manufacturer_id');
        $filter_subscriptions_date_from = $this->getState('filter_subscriptions_date_from');
        $filter_subscriptions_date_to = $this->getState('filter_subscriptions_date_to');
        $filter_subscriptions_datetype = $this->getState('filter_subscriptions_datetype');
        $filter_orderstates = $this->getState('filter_orderstates');
        $filter_paymentstatus = $this->getState('filter_paymentstatus') ;
        $filter_id_from = $this->getState( 'filter_id_from' );
        $filter_id_to = $this->getState( 'filter_id_to' );

        if ($filter)
       	{
					$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

					$where = array();
					$where[] = 'LOWER(tbl.orderitem_id) LIKE '.$key;
					$where[] = 'LOWER(tbl.orderitem_name) LIKE '.$key;

					$query->where('('.implode(' OR ', $where).')');
       	}

				if ( strlen( $filter_id_from ) )
				{
					if ( strlen( $filter_id_to ) )
					{
						$query->where( 'tbl.orderitem_id >= ' . ( int ) $filter_id_from );
					}
					else
					{
						$query->where( 'tbl.orderitem_id = ' . ( int ) $filter_id_from );
					}
				}
       	else if( strlen( $filter_id_to ) )
       	{
						$query->where( 'tbl.orderitem_id <= ' . ( int ) $filter_id_to );
       	}

       	if ($filter_productname)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_productname ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.orderitem_name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }

    	if ($filter_manufacturer_name)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_manufacturer_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(m.manufacturer_name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }

       	if ($filter_manufacturer_id )
       	{
        	$query->where('m.manufacturer_id = '.$this->_db->q($filter_manufacturer_id));
       	}

       	if ($filter_orderid)
       	{
        	$query->where('tbl.order_id = '.$this->_db->q($filter_orderid));
       	}

       	if (strlen($filter_recurs))
        {
            $query->where('tbl.orderitem_recurs = 1');
        }

        if (strlen($filter_date_from))
        {
            switch ($filter_datetype)
            {
                case "shipped":
                    $query->where("o.shipped_date >= '".$filter_date_from."'");
                  break;
                case "modified":
                    $query->where("o.modified_date >= '".$filter_date_from."'");
                  break;
                case "created":
                default:
                    $query->where("o.created_date >= '".$filter_date_from."'");
                  break;
            }
        }
        if (strlen($filter_date_to))
        {
            switch ($filter_datetype)
            {
                case "shipped":
                    $query->where("o.shipped_date <= '".$filter_date_to."'");
                  break;
                case "modified":
                    $query->where("o.modified_date <= '".$filter_date_to."'");
                  break;
                case "created":
                default:
                    $query->where("o.created_date <= '".$filter_date_to."'");
                  break;
            }
        }

    	if (strlen($filter_subscriptions_date_from))
        {
            switch ($filter_subscriptions_datetype)
            {
                case "expires":
                    $query->where("sb.expires_datetime >= '".$filter_subscriptions_date_from."'");
                  break;
                case "created":
                default:
                    $query->where("sb.created_datetime >= '".$filter_subscriptions_date_from."'");
                  break;
            }
        }
        if (strlen($filter_subscriptions_date_to))
        {
            switch ($filter_subscriptions_datetype)
            {
                case "expires":
                    $query->where("sb.expires_datetime <= '".$filter_subscriptions_date_to."'");
                  break;
                case "created":
                default:
                    $query->where("sb.created_datetime <= '".$filter_subscriptions_date_to."'");
                  break;
            }
        }

        if (strlen($filter_userid))
        {
            $query->where('o.user_id = '.$this->_db->q($filter_userid));
        }

        if (strlen($filter_productid))
        {
            $query->where('tbl.product_id = '.$this->_db->q($filter_productid));
        }

    	if (is_array($filter_orderstates) && !empty($filter_orderstates))
        {
            $query->where('s.order_state_id IN('.implode(",", $filter_orderstates).')' );
        }

    	if ( strlen($filter_paymentstatus) )
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_paymentstatus ) ) ).'%');
            $query->where( 'LOWER(op.transaction_status) LIKE '.$key );
        }
    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();

        $field[] = " tbl.* ";
        $field[] = " p.product_name ";
        $field[] = " p.product_sku ";
        $field[] = " p.product_model ";
        $field[] = " p.product_params ";
        $field[] = " p.product_article ";
        $field[] = " o.* ";
        $field[] = " s.* ";
        $field[] = " m.manufacturer_name ";
        $field[] = " sb.created_datetime AS subscription_created_datetime";
        $field[] = " sb.expires_datetime AS subscription_expires_datetime";
        $field[] = " op.transaction_status";
		$field[] = " u.name AS user_name ";
		$field[] = " u.username AS user_username ";
		$field[] = " u.email ";
		$field[] = " ui.phone_1 ";
		$field[] = " ui.fax ";
		$field[] = " ui.first_name as first_name";
		$field[] = " ui.last_name as last_name";
		$field[] = " ui.email as userinfo_email";

        $query->select( $field );
    }

    protected function _buildQueryJoins(&$query)
    {
    	$query->join('LEFT', '#__citruscart_products AS p ON tbl.product_id = p.product_id');
        $query->join('LEFT', '#__citruscart_orders AS o ON tbl.order_id = o.order_id');
        $query->join('LEFT', '#__citruscart_orderstates AS s ON s.order_state_id = o.order_state_id');
        $query->join('LEFT', '#__citruscart_manufacturers AS m ON m.manufacturer_id = p.manufacturer_id');
        $query->join('LEFT', '#__citruscart_subscriptions AS sb ON sb.orderitem_id = tbl.orderitem_id');
        $query->join('LEFT', '#__citruscart_orderpayments AS op ON op.order_id = o.order_id');
		$query->join('LEFT', '#__citruscart_userinfo AS ui ON ui.user_id = o.user_id');
		$query->join('LEFT', '#__users AS u ON u.id = o.user_id');
    }

	public function getList($refresh = false, $getEav = true, $options = array())
	{
		$list = parent::getList($refresh,$getEav,$options);

		// If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }

		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_citruscart&view=orderitems&task=edit&id='.$item->orderitem_id;
		}
		return $list;
	}
}