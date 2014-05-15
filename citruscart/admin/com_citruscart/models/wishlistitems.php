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

class CitruscartModelWishlistItems extends CitruscartModelEav
{
	protected function _buildQueryWhere(&$query)
	{
		$filter_user     = $this->getState('filter_user');
		$filter_session  = $this->getState('filter_session');
		$filter_product  = $this->getState('filter_product');
		$filter_date_from	= $this->getState('filter_date_from');
		$filter_date_to		= $this->getState('filter_date_to');
		$filter_name	= $this->getState('filter_name');
        $filter_ids	= $this->getState('filter_ids');
        $filter_wishlist	= $this->getState('filter_wishlist');
		$filter_privacy = $this->getState( 'filter_privacy' );
		$filter_search  = $this->getState( 'filter_search', '' );
		$filter_search_any  = $this->getState( 'filter_search_any', '' );
		$filter_search_all  = $this->getState( 'filter_search_all', '' );
 
		if (strlen($filter_user))
		{
			$query->where('tbl.user_id = '.$this->_db->Quote($filter_user));
		}

		if (strlen($filter_session))
		{
			$query->where( "tbl.session_id = ".$this->_db->Quote($filter_session));
		}

		if (!empty($filter_product))
		{
			$query->where('tbl.product_id = '.(int) $filter_product);
		}

		if (strlen($filter_date_from))
		{
			$query->where("tbl.last_updated >= '".$filter_date_from."'");
		}

		if (strlen($filter_date_to))
		{
			$query->where("tbl.last_updated <= '".$filter_date_to."'");
		}

		if (strlen($filter_name))
		{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
			$query->where('LOWER(p.product_name) LIKE '.$key);
		}

		if (!empty($filter_ids) && is_array($filter_ids))
        {
        	$query->where('tbl.wishlist_item_id IN('.implode(",", $filter_ids).')' );
        }
        
        if (strlen($filter_wishlist))
        {
		  $query->where('tbl.wishlist_id = '. $this->_db->Quote( (int) $filter_wishlist));
        }
		if( !empty( $filter_privacy ) ) {
			if( is_array( $filter_privacy ) ) {
				$query->where( 'tbl.privacy IN ('.implode( ',', $filter_privacy ).')' );
			} else {
				$query->where( 'tbl.privacy = '. (int)$filter_privacy );        
			}
		}
		
		if( strlen( $filter_search ) ) {
			$key = $this->_db->Quote( '%' . $this->_db->escape( trim( strtolower( $filter_search ) ) ) . '%' );
			$where = array( );
			$where[] = 'LOWER(u.last_name) LIKE ' . $key;
			$where[] = 'LOWER(u.first_name) LIKE ' . $key;
			$where[] = 'LOWER(u.middle_name) LIKE ' . $key;
			$where[] = 'LOWER(uj.name) LIKE ' . $key;
			
			if( empty( $filter_user ) ) {
				$query->where( '(' . implode( ' OR ', $where ) . ') AND tbl.wishlist_id = 0 ' );
			} else {
				$query->where( '(' . implode( ' OR ', $where ) . ') AND tbl.wishlist_id = 0  AND ( tbl.user_id <> '.$this->_db->quote((int)$filter_user).' )' );        
			}
		}
		
		if ( !empty( $filter_search_all ) )
		{
			$words = explode( ' ', $filter_search_all );
			foreach ($words as $word)
			{
				$key = $this->_db->Quote( '%' . $this->_db->escape( trim( strtolower( $word ) ) ) . '%' );
				$where = array( );
				$where[] = 'LOWER(u.last_name) LIKE ' . $key;
				$where[] = 'LOWER(u.first_name) LIKE ' . $key;
				$where[] = 'LOWER(u.middle_name) LIKE ' . $key;
				$where[] = 'LOWER(uj.name) LIKE ' . $key;
				
				if( empty( $filter_user ) ) {
					$query->where( '(' . implode( ' OR ', $where ) . ') AND tbl.wishlist_id = 0 ' );
				}  else {
					$query->where( '(' . implode( ' OR ', $where ) . ') AND tbl.wishlist_id = 0  AND ( tbl.user_id <> '.$this->_db->quote((int)$filter_user).' )' );          
				}
			}
		}
		
		if ( !empty( $filter_search_any ) )
		{
			$words = explode( ' ', $filter_search_any );
			$wheres = array( );
			foreach ($words as $word)
			{
				$key = $this->_db->Quote( '%' . $this->_db->escape( trim( strtolower( $word ) ) ) . '%' );
				$where = array( );
				$where[] = 'LOWER(u.last_name) LIKE ' . $key;
				$where[] = 'LOWER(u.first_name) LIKE ' . $key;
				$where[] = 'LOWER(u.middle_name) LIKE ' . $key;
				$where[] = 'LOWER(uj.name) LIKE ' . $key;
				
				$wheres[] = '(' . implode( ' OR ', $where ) . ')';
			}
			
			if (!empty($wheres)) 
			{
				$stmt = '(' . implode( ' OR ', $wheres ) . ') AND tbl.wishlist_id = 0 ';
				if( !empty( $filter_user ) ) {
					$stmt .= ' AND ( tbl.user_id <> '.$this->_db->quote((int)$filter_user).' )';
				}
				$query->where($stmt);
			}
		}
	}

	protected function _buildQueryJoins(&$query)
	{
		$query->join('LEFT', '#__citruscart_products AS p ON tbl.product_id = p.product_id');
		$query->join('LEFT', '#__citruscart_wishlists AS wl ON tbl.wishlist_id = wl.wishlist_id');

		$filter_search  = $this->getState( 'filter_search', '' );
		$filter_search_any  = $this->getState( 'filter_search_any', '' );
		$filter_search_all  = $this->getState( 'filter_search_all', '' );
		if( !empty( $filter_search ) || !empty( $filter_search_all ) || !empty( $filter_search_any ) ) {
			$query->join('LEFT', '#__citruscart_userinfo AS u ON u.user_id = tbl.user_id');      
			$query->join('LEFT', '#__users AS uj ON uj.id = tbl.user_id');      
		}
	}

	protected function _buildQueryFields(&$query)
	{
		$field = array();
		$field[] = " p.product_name ";
		$field[] = " p.product_sku ";
		$field[] = " p.product_full_image ";
		$field[] = " p.product_ships ";
		$field[] = " p.product_weight ";
		$field[] = " p.product_length ";
		$field[] = " p.product_width ";
		$field[] = " p.product_height ";
		$field[] = " p.product_recurs ";
		$field[] = " p.product_enabled ";
		$field[] = " p.product_notforsale ";
		$field[] = " p.product_rating ";
		$field[] = " p.quantity_restriction ";
		$field[] = " p.quantity_min ";
		$field[] = " p.quantity_max ";
		$field[] = " p.quantity_step ";
		$field[] = " p.tax_class_id ";
		$field[] = " p.recurring_payments ";
		$field[] = " p.recurring_period_interval ";
		$field[] = " p.recurring_period_unit ";
		$field[] = " p.recurring_trial ";
		$field[] = " p.recurring_trial_period_interval ";
		$field[] = " p.recurring_trial_period_unit ";
		$field[] = " p.recurring_trial_price ";
		$field[] = " p.subscription_prorated ";
		$field[] = " p.subscription_prorated_date ";
		$field[] = " p.subscription_prorated_charge ";
		$field[] = " p.subscription_prorated_term ";
		$field[] = " p.subscription_period_unit ";
		$field[] = " p.product_params ";
		$field[] = " wl.wishlist_name ";

		// This subquery returns the default price for the product and allows for sorting by price
		$date = JFactory::getDate()->toSql();

		$default_group = Citruscart::getInstance()->get('default_user_group', '1');
		$filter_group = (int) $this->getState('filter_group');

		if (empty($filter_group))
		{
			$filter_group = $default_group;
		}

		$field[] = "
			(
			SELECT
				prices.product_price
			FROM
				#__citruscart_productprices AS prices
			WHERE
				prices.product_id = tbl.product_id
				AND prices.group_id = '$filter_group'
				AND prices.product_price_startdate <= '$date'
				AND (prices.product_price_enddate >= '$date' OR prices.product_price_enddate = '0000-00-00 00:00:00' )
				ORDER BY prices.price_quantity_start ASC
			LIMIT 1
			)
		AS product_price ";

		$filter_search  = $this->getState( 'filter_search', '' );
		$filter_search_any  = $this->getState( 'filter_search_any', '' );
		$filter_search_all  = $this->getState( 'filter_search_all', '' );
		if( !empty( $filter_search ) || !empty( $filter_search_all ) || !empty( $filter_search_any ) ) {
			$field[] = 'tbl.user_id';
			$field[] = 'u.first_name';
			$field[] = 'u.middle_name';
			$field[] = 'u.last_name';
			$field[] = 'uj.name AS `user_joomla_name`';
			$field[] = 'p.created_date';
		}
		
		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$query->select( $field );
	}

	

    protected function prepareItem( &$item, $key=0, $refresh=false )
    {	
        $row = $this->getTable();
        $row->bind( $item );
        $item->available = $row->isAvailable();
        parent::prepareItem( $item, $key, $refresh );
    }    

	public function getList($refresh=false, $getEav = true, $options = array())
	{
		static $pa, $pao;

		if (empty($pa)) { $pa = array(); }
		if (empty($pao)) { $pao = array(); }

		Citruscart::load( "CitruscartHelperUser", 'helpers.user' );
		Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
		$user_helper = CitruscartHelperBase::getInstance( 'User' );
		$product_helper = CitruscartHelperBase::getInstance( 'Product' );

		if (empty( $this->_list )  || $refresh )
		{
			DSCTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$items = parent::getList($refresh);

			// If no item in the list, return an array()
			if( empty( $items ) ){
				return array();
			}
				
			foreach($items as $item)
			{
			    if (empty($item->product_qty)) { $item->product_qty = '1'; }
				$filter_group = $user_helper->getUserGroup( JFactory::getUser()->id, $item->product_id );

				// at this point, ->product_price holds the default price for the product,
				// but the user may qualify for a discount based on volume or date, so let's get that price override
				$item->product_price_override = $product_helper->getPrice( $item->product_id, $item->product_qty, $filter_group , JFactory::getDate()->toSql() );

				//checking if we do price override
				$item->product_price_override->override = true;

				if (!empty($item->product_price_override))
				{
					$item->product_price = $item->product_price_override->product_price;
				}

				if ($item->product_recurs)
				{
					$item->recurring_price = $item->product_price;
					if( $item->subscription_prorated )
					{
						$result = CitruscartHelperSubscription::calculateProRatedTrial( $item->subscription_prorated_date,
						$item->subscription_prorated_term,
						$item->recurring_period_unit,
						$item->recurring_trial_price,
						$item->subscription_prorated_charge
						);
						$item->product_price = $result['price'];
						$item->recurring_trial_price = $result['price'];
						$item->recurring_trial_period_interval = $result['interval'];
						$item->recurring_trial_period_unit = $result['unit'];
						$item->recurring_trial = $result['trial'];
					}
					else
						if ($item->recurring_trial)
						{
							$item->product_price = $item->recurring_trial_price;
						}
				}

				$item->product_parameters = new DSCParameter( $item->product_params );

				$item->orderitem_attributes_price = '0.00000';
				$attributes_names = array();
				if(!empty($item->product_attributes))
				{
					$item->attributes = array(); // array of each selected attribute's object
					$attibutes_array = explode(',', $item->product_attributes);
					foreach ($attibutes_array as $attrib_id)
					{
						if (empty($pao[$attrib_id]))
						{
							// load the attrib's object
							$pao[$attrib_id] = DSCTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
							$pao[$attrib_id]->load( $attrib_id );
						}
						$table = $pao[$attrib_id];

						// update the price
						// + or -
						if($table->productattributeoption_prefix != '=')
						{
							$item->product_price = $item->product_price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
							// store the attribute's price impact
							$item->orderitem_attributes_price = $item->orderitem_attributes_price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
							$item->product_price_override->override = true;
						}
						// only if prefix is =
						else
						{
							// assign the product attribute price as the product price
							//then set the orderitem_attributes_price to 0.0000
							$item->product_price = $table->productattributeoption_price; //
							// store the attribute's price impact
							$item->orderitem_attributes_price = "0.00000";
							$item->product_price_override->override = false;
						}
							
						$item->orderitem_attributes_price = number_format($item->orderitem_attributes_price, '5', '.', '');
						$item->product_sku .= $table->productattributeoption_code;
							
						// store a csv of the attrib names, built by Attribute name + Attribute option name
						if (empty($pa[$table->productattribute_id]))
						{
							$pa[$table->productattribute_id] = DSCTable::getInstance('ProductAttributes', 'CitruscartTable');
							$pa[$table->productattribute_id]->load( $table->productattribute_id );
						}
						$atable = $pa[$table->productattribute_id];

						if (!empty($atable->productattribute_id))
						{
							$name = JText::_($atable->productattribute_name) . ': ' . JText::_( $table->productattributeoption_name );
							$attributes_names[] = $name;
						}
						else
						{
							$attributes_names[] = JText::_( $table->productattributeoption_name );
						}
					}


					// Could someone explain to me why this is necessary?
					if ($item->orderitem_attributes_price >= 0)
					{
						// formatted for storage in the DB
						$item->orderitem_attributes_price = "+$item->orderitem_attributes_price";
					}
				}

				$item->attributes_names = implode(', ', $attributes_names);
			}

			$this->_list = $items;
		}
			
		return $this->_list;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $old_sessionid
	 * @param unknown_type $user_id
	 * @return return_type
	 */
	public function setUserForSessionItems( $old_sessionid, $user_id )
	{
	    $query = new CitruscartQuery();
	    $query->update( '#__citruscart_wishlistitems' );
	    $query->set( "user_id = '" . $user_id . "'" );
	    $query->where( "session_id = '" . $old_sessionid . "'" );
	    $query->where( "user_id = '0'" );
	    
	    $db = $this->getDBO();
	    $db->setQuery( (string) $query );
	    if ($db->query())
	    {
	        $affected = $db->getAffectedRows();
	        /* 
	         * Only controller should impact UI.
	         * Send the controller a data object with all the impacted items and let it handle the UI impact 
	         * 
	        if ($affected > 0)
	        {
	            $lang = JFactory::getLanguage();
	            $lang->load( 'com_citruscart' );
                Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
                $router = new CitruscartHelperRoute();
    		    $url = "index.php?option=com_citruscart&view=wishlists&Itemid=".$router->findItemid( array('view'=>'wishlists') );
        		$message = JText::sprintf( JText::_('COM_CITRUSCART_ADDED_TO_WISHLIST'), $url );
	            JFactory::getApplication()->enqueueMessage( $message );
	        }
	        */
	    }

	    $this->clearSessionIds();
	    $this->mergeUserItems( $user_id );
	}
	
	public function clearSessionIds() 
	{
	    $query = new CitruscartQuery();
	    $query->update( '#__citruscart_wishlistitems' );
	    $query->set( "session_id = ''" );
	    $query->where( "user_id > 0" );
	    
	    $db = $this->getDBO();
	    $db->setQuery( (string) $query );
	    if (!$db->query()) 
	    {
	        return false;
	    }

	    return true;
	}
	
	public function mergeUserItems( $user_id )
	{
	    $table = $this->getTable();
	    
	    $this->emptyState();
	    $this->setState('filter_user', $user_id );
	    if ($items = $this->getList(true)) 
	    {
	        $done = array();
	        foreach ($items as $item) 
	        {
	            $key = $item->product_id . "." . $item->product_attributes;
	            if (empty($done[$key])) 
	            {
	                $done[$key] = $item;
	            } 
	            else 
	            {
	                // delete the current item, but only if it was last updated farther in the past than the current item
	                $to_delete = $item->wishlist_id;
	                if ($item->last_updated > $done[$key]->last_updated) 
	                {
	                    $to_delete = $done[$key]->wishlist_id;
	                    $done[$key] = $item;
	                }
                    $table->delete($to_delete);
	            }
	        }
	    }
	    
	}
	
	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	public function deleteExpiredSessionItems()
	{
	    $db = JFactory::getDBO();
	
	    Citruscart::load( 'CitruscartQuery', 'library.query' );
	    Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
	    $helper = new CitruscartHelperBase();
	    $query = new CitruscartQuery();
	
	    $query->select( "tbl.session_id" );
	    $query->from( "#__session AS tbl" );
	    $db->setQuery( (string) $query );
	    $results = $db->loadAssocList();
	    $session_ids = $helper->getColumn($results, 'session_id');
	
	    $query = new CitruscartQuery();
	    $query->delete();
	    $query->from( "#__citruscart_wishlistitems" );
	    $query->where( "`user_id` = '0'" );
	    $query->where( "`session_id` NOT IN('" . implode( "', '", $session_ids) . "')" );
	
	    $db->setQuery( (string) $query );
	    if (!$db->query())
	    {
	        $this->setError( $db->getErrorMsg() );
	        return false;
	    }
	
	    $date = JFactory::getDate();
	    $now = $date->toSql();
	
	    // Update config to say this has been done already
	    JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
	    $config = JTable::getInstance( 'Config', 'CitruscartTable' );
	    $config->load( array( 'config_name'=>'last_deleted_expired_sessionwishlists') );
	    $config->config_name = 'last_deleted_expired_sessionwishlists';
	    $config->value = $now;
	    $config->save();
	    return true;
	}	
}