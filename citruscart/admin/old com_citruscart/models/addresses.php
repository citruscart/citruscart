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

class CitruscartModelAddresses extends CitruscartModelBase
{
    protected $_objectClass = 'CitruscartTableAddresses';

    protected function _buildQueryWhere(&$query)
    {
       	$filter   			= $this->getState('filter');
       	$filter_deleted		= $this->getState('filter_deleted');
       	$filter_userid		= $this->getState('filter_userid');
		$filter_addressid	= $this->getState('filter_addressid');
		$filter_shippingid  = $this->getState('filter_shippingid');
		$filter_isdefaultbilling  = $this->getState('filter_isdefaultbilling');
		$filter_isdefaultshipping = $this->getState('filter_isdefaultshipping');
		$filter_user		= $this->getState('filter_user');
		$filter_address		= $this->getState('filter_address');

       	if ($filter)
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.address_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.first_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.last_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.middle_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.address_1) LIKE '.$key;
			$where[] = 'LOWER(tbl.address_2) LIKE '.$key;
			$where[] = 'LOWER(tbl.postal_code) LIKE '.$key;
			$where[] = 'LOWER(c.country_name) LIKE '.$key;
			$where[] = 'LOWER(z.zone_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.phone_1) LIKE '.$key;
			$where[] = 'LOWER(tbl.phone_2) LIKE '.$key;

			$query->where('('.implode(' OR ', $where).')');
       	}

       	if ($filter_address)
       	{
       	    $key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_address ) ) ).'%');
       	    $where = array();
       	    $where[] = 'LOWER(tbl.first_name) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.last_name) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.middle_name) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.address_1) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.address_2) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.city) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.postal_code) LIKE '.$key;
       	    $where[] = 'LOWER(c.country_name) LIKE '.$key;
       	    $where[] = 'LOWER(z.zone_name) LIKE '.$key;

       	    $query->where('('.implode(' OR ', $where).')');
       	}

        if (strlen($filter_deleted))
        {
        	$query->where('tbl.is_deleted = '.$this->_db->q($filter_deleted));
       	}

		if ($filter_addressid){
			$query->where('tbl.address_id = '.$this->_db->q($filter_addressid));
		}
       	if (strlen($filter_userid))
       	{
        	$query->where('tbl.user_id = '.$this->_db->q($filter_userid));
       	}
       	if ($filter_shippingid)
       	{
        	$query->where('tbl.is_default_shipping = 1');
       	}

        if ($filter_isdefaultbilling)
        {
            $query->where('tbl.is_default_billing = 1');
        }
        if ($filter_isdefaultshipping)
        {
            $query->where('tbl.is_default_shipping = 1');
        }

        if (strlen($filter_user))
        {
            $key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_user ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(u.name) LIKE '.$key;
            $where[] = 'LOWER(u.email) LIKE '.$key;
            $where[] = 'LOWER(u.username) LIKE '.$key;

            $query->where('('.implode(' OR ', $where).')');
        }
    }

	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " tbl.* ";
		$field[] = " c.country_name as country_name ";
		$field[] = " z.zone_name as zone_name ";
		$field[] = " c.country_isocode_2 as country_code ";
		$field[] = " z.code as zone_code ";

		$query->select( $field );
	}

	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_countries c ON c.country_id = tbl.country_id');
		$query->join('LEFT', '#__citruscart_zones AS z ON z.zone_id = tbl.zone_id');

		$filter_user = $this->getState('filter_user');
		if (strlen($filter_user)) {
		    $query->join('LEFT', '#__users AS u ON tbl.user_id = u.id');
		}
	}

	/**
	 * Set basic properties for the item, whether in a list or a singleton
	 *
	 * @param unknown_type $item
	 * @param unknown_type $key
	 * @param unknown_type $refresh
	 */
	protected function prepareItem( &$item, $key=0, $refresh=false )
	{		
	    $item->link = 'index.php?option=com_citruscart&view=addresses&task=edit&id='.$item->address_id;
	    
	    /* 
	     * check extra fields empty or not 
	     */
	    if (!empty($item->extra_fields))
	    {
	        $extra_fields = new DSCParameter(trim($item->extra_fields));
	        $extra_fields = $extra_fields->toArray();
	        foreach($extra_fields as $k => $v)
	        {
	            $item->$k = $v;
	        }
	    }
		
	    /*
	     * refer parent prepareItem method. 
	     */
	    parent::prepareItem( $item, $key, $refresh );
	}
	
	/*public function getItem( $pk=null, $refresh=false, $emptyState=true){

		$db = JFactory::getDbo();
		$query= $db->getQuery(true);
		$query->select("*")->from("#__citruscart_addresses");
		$query->where("address_id=".$db->q($pk));
		$db->setQuery($query);
		return $row=$db->loadObject();
	}*/
	
	public function getList($refresh = false){
		
		if (empty( $this->_list ))
		{
			$query = $this->getQuery(true);
		
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		$list = $this->_list;
		
		foreach($list as $item)
		{			
			$item->link = 'index.php?option=com_citruscart&view=addresses&task=edit&id='.$item->address_id;
		}
		return $list;
		
		/*
		$db = JFactory::getDbo();
		$query= $db->getQuery(true);
		$query->select("*")->from("#__citruscart_addresses");
		$this->_buildQueryWhere($query);
		$db->setQuery($query);
		return $row=$db->loadObjectList();
		*/
	}
}