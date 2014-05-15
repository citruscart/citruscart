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

class CitruscartModelSubscriptionHistory extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter             = $this->getState('filter');
       	$filter_subscriptionid     = $this->getState('filter_subscriptionid');
       	$filter_notified    = $this->getState('filter_notified');
       	$filter_type    = $this->getState('filter_type');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.subscriptionhistory_id) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
        if ($filter_subscriptionid)
        {
            $query->where('tbl.subscription_id = '.$this->_db->Quote($filter_subscriptionid));
        }
        
        if ($filter_notified)
        {
            $query->where('tbl.notify_customer = '.$this->_db->Quote($filter_notified));
        }

        if (strlen($filter_type))
        {
            $query->where('tbl.subscriptionhistory_type = '.$this->_db->Quote($filter_type));
        }        
    }
    
    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_subscriptions AS subscriptions ON subscriptions.subscription_id = tbl.subscription_id');   
    }
    
    protected function _buildQueryFields(&$query)
    {
        $field = array();

        $field[] = " tbl.* ";
        $field[] = " subscriptions.* ";

        $query->select( $field );
    }
}