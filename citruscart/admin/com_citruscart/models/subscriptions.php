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

class CitruscartModelSubscriptions extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter 			   = $this->getState('filter');
       	$filter_subscriptionid = $this->getState('filter_subscriptionid');
        $filter_userid 		   = $this->getState('filter_userid');
        $filter_user 		   = $this->getState('filter_user');
        $filter_orderid 	   = $this->getState('filter_orderid');
        $filter_orderitemid    = $this->getState('filter_orderitemid');
        $filter_enabled 	   = $this->getState('filter_enabled');
        $filter_productid 	   = $this->getState('filter_productid');
        $filter_productname    = $this->getState('filter_type');
        $filter_transactionid  = $this->getState('filter_transactionid');
        $filter_date_from      = $this->getState('filter_date_from');
        $filter_date_to        = $this->getState('filter_date_to');
        $filter_datetype       = $this->getState('filter_datetype');
        $filter_lifetime       = $this->getState('filter_lifetime');
        $filter_id_from        = $this->getState('filter_id_from');
        $filter_id_to          = $this->getState('filter_id_to');
        $filter_orderstate     = $this->getState('filter_orderstate');
        $filter_subnum         = $this->getState('filter_subnum');
        $filter_date_from_expires = $this->getState('filter_date_from_expires');
        $filter_date_to_expires = $this->getState('filter_date_to_expires');
       	if ($filter)
       	{
       		$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
					$where = array();
					$where[] = 'LOWER(tbl.subscription_id) LIKE '.$key;
					$query->where('('.implode(' OR ', $where).')');
       	}

        if (strlen($filter_id_from))
        {
        	if (strlen($filter_id_to))
        	{
        		$query->where('tbl.subscription_id >= '.(int) $filter_id_from);
        	}
        		else
        	{
        		$query->where('tbl.subscription_id = '.(int) $filter_id_from);
        	}
       	}
       	if (strlen($filter_id_to))
        {
        	$query->where('tbl.subscription_id <= '.(int) $filter_id_to);
       	}

        if (strlen($filter_subscriptionid))
        {
            $query->where('tbl.subscription_id = '.$this->_db->q($filter_subscriptionid));
        }

        if (strlen($filter_transactionid))
        {
            $query->where('tbl.transaction_id LIKE '.$this->_db->q('%'.$filter_transactionid.'%'));
        }

        if (strlen($filter_productname))
        {
            $query->where('p.product_name LIKE '.$this->_db->q('%'.$filter_productname.'%'));
        }


        if (strlen($filter_userid))
        {
            $query->where('tbl.user_id = '.$this->_db->q($filter_userid));
        }

        if (strlen($filter_user))
        {
        	if( strcmp((int)$filter_user,$filter_user ) )
            $query->where('u.username LIKE '.$this->_db->q('%'.$filter_user.'%'));
        	else
           	$query->where('tbl.user_id = '.$this->_db->q($filter_user));
        }

       	if (strlen($filter_subnum))
        {
        	$query->where('tbl.sub_number LIKE '.$this->_db->q('%'.$filter_subnum.'%'));
       	}

        if (strlen($filter_orderid))
        {
            $query->where('tbl.order_id = '.$this->_db->q($filter_orderid));
        }

        if (strlen($filter_orderitemid))
        {
            $query->where('tbl.orderitem_id = '.$this->_db->q($filter_orderitemid));
        }

        if (strlen($filter_enabled))
        {
            $query->where('tbl.subscription_enabled = '.$this->_db->q($filter_enabled));
        }

        if (strlen($filter_lifetime))
        {
            $query->where('tbl.lifetime_enabled = '.$this->_db->q($filter_lifetime));
        }

        if (strlen($filter_productid))
        {
            $query->where('tbl.product_id = '.$this->_db->q($filter_productid));
        }

        if (strlen($filter_date_from))
        {
            switch ($filter_datetype)
            {
                case "expires":
                    $query->where("tbl.expires_datetime >= '".$filter_date_from."'");
                  break;
                case "created":
                default:
                    $query->where("tbl.created_datetime >= '".$filter_date_from."'");
                  break;
            }
        }

        if (strlen($filter_date_to))
        {
            switch ($filter_datetype)
            {
                case "expires":
                    $query->where("tbl.expires_datetime <= '".$filter_date_to."'");
                  break;
                case "created":
                default:
                    $query->where("tbl.created_datetime <= '".$filter_date_to."'");
                  break;
            }
        }

    	if (strlen($filter_orderstate))
        {
            $query->where('o.order_state_id = '.$this->_db->q($filter_orderstate));
        }
        if(strlen($filter_date_from_expires)){

        	$query->where('o.date_from_expires= '.$this->_db->q($filter_date_from_expires));

        }

        if(strlen($filter_date_to_expires)){

        	$query->where('o.date_to_expires= '.$this->_db->q($filter_date_to_expires));

        }

    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_orderitems AS oi ON oi.orderitem_id = tbl.orderitem_id');
        $query->join('LEFT', '#__citruscart_products AS p ON oi.product_id = p.product_id');
        $query->join('LEFT', '#__citruscart_orders AS o ON tbl.order_id = o.order_id');
        $query->join('LEFT', '#__users AS u ON u.id = tbl.user_id');
    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();

        $field[] = " p.product_name ";
        $field[] = " p.product_sku ";
        $field[] = " p.product_model ";
        $field[] = " o.* ";
        $field[] = " tbl.* ";
        $field[] = " oi.* ";
        $field[] = " u.name AS user_name ";
        $field[] = " u.username AS user_username ";
        $field[] = " u.email ";

        $query->select( $field );
    }

   public function getList($refresh = false)
    {
        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $list = parent::getList($refresh);

        // If no item in the list, return an array()
        if( empty( $list ) ){
            return array();
        }
        $db = JFactory::getDbo();
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        $q = new CitruscartQuery();
        $q->select( 'order_hash' );
        $q->from( '#__citruscart_orders' );

        Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
        foreach ($list as $item)
        {
            $item->link = 'index.php?option=com_citruscart&view=subscriptions&task=edit&id='.$item->subscription_id;
            $item->link_view = 'index.php?option=com_citruscart&view=subscriptions&task=view&id='.$item->subscription_id;
            $item->history = CitruscartHelperSubscription::getHistory( $item->subscription_id );

            //$q->_where = null;
            $q->where( 'order_id = '.$item->order_id );
            $db->setQuery( $q );
            $item->order_hash = $db->loadResult();
        }

        return $list;
    }

   	public function getItem( $pk=null, $refresh=false, $emptyState=true )
    {
        Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
        if ($item = parent::getItem($pk, $refresh, $emptyState))
        {
            $item->link = 'index.php?option=com_citruscart&view=subscriptions&task=edit&id='.$item->subscription_id;
            $item->link_view = 'index.php?option=com_citruscart&view=subscriptions&task=view&id='.$item->subscription_id;
            $item->history = CitruscartHelperSubscription::getHistory( $item->subscription_id );

            Citruscart::load( 'CitruscartQuery', 'library.query' );
            $q = new CitruscartQuery();
            $q->select( 'order_hash' );
            $q->from( '#__citruscart_orders' );
            $q->where( 'order_id = '.$item->order_id );
            $db = JFactory::getDbo();
            $db->setQuery( $q );
            $item->order_hash = $db->loadResult();
        }

        
		JFactory::getApplication()->triggerEvent( 'onPrepare'.$this->getTable()->get('_suffix'), array( &$item ) );

        return $item;
    }

	/**
	 * Gets list of all subscriptions by issue x-days before expiring
	 *
	 * @$days Number of days before expiring (0 stands for expired now)
	 *
	 * @return List of subscriptions by issue
	 */
	public function getListByIssues( $days = 0 )
	{
		$db = $this->getDBO();
		$date = JFactory::getDate();
		$tz = JFactory::getConfig()->get( 'offset' );
		$date->setTimezone(new DateTimeZone($tz)); //here!
		$today = $date->format( "%Y-%m-%d" );

		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$q = new CitruscartQuery();
		$q->select( 's.*' );
		$q->from( '`#__citruscart_productissues` tbl' );
		$q->join( 'left', '`#__citruscart_subscriptions` s ON s.`product_id` = tbl.`product_id`' );
		$q->join( 'left', '`#__citruscart_orderitems` oi ON s.`orderitem_id` = oi.`orderitem_id`' );
		$q->where( 's.`subscription_enabled` = 1' );
		$q->where( 'oi.`subscription_period_unit` = \'I\'' );

		if( $days ) // x-days before expiring
		{
			$query = " SELECT DATE_ADD('".$today."', INTERVAL '.$days.' DAY) ";
			$db->setQuery( $query );
			$date = Date( 'Y-m-d', strtotime( $db->loadResult() ) );
		}
		else // just expired
		{
			$date = $today;
		}
		$q->where( 'DATE_FORMAT( tbl.`publishing_date`, \'%Y-%m-%d\' ) = \''.$date.'\'' );
		$db->setQuery( (string)$q );
		return $db->loadObjectList();
	}

	/**
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCache()
	{
	    parent::clearCache();
	    self::clearCacheAuxiliary();
	}

	/**
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCacheAuxiliary()
	{
	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );

	    $model = DSCModel::getInstance('SubscriptionHistory', 'CitruscartModel');
	    $model->clearCache();
	}
}
