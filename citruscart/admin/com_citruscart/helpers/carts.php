<?php
/*------------------------------------------------------------------------
 # com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );
Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

class CitruscartHelperCarts extends CitruscartHelperBase
{
	/**
	 * Adds an item to the cart
	 *
	 * @param $item
	 * @return unknown_type
	 */
	public function addItem( $item )
	{
		$session = JFactory::getSession();
		$user = JFactory::getUser();

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$table = JTable::getInstance( 'Carts', 'CitruscartTable' );

		// first, determine if this product+attribute+vendor(+additonal_keys) exists in the cart
		// if so, update quantity
		// otherwise, add as new item
		// return the cart object with cart_id (to be used by plugins, etc)

		$keynames = array();
		$item->user_id = (empty($item->user_id)) ? $user->id : $item->user_id;
		$keynames['user_id'] = $item->user_id;
		if (empty($item->user_id))
		{
			$keynames['session_id'] = $session->getId();
		}
		$keynames['product_id'] = $item->product_id;
		$keynames['product_attributes'] = $item->product_attributes;

		// fire plugin event: onGetAdditionalCartKeyValues
		// this event allows plugins to extend the multiple-column primary key of the carts table
		$additionalKeyValues = CitruscartHelperCarts::getAdditionalKeyValues( $item, null, null );
		if (!empty($additionalKeyValues))
		{
			$keynames = array_merge($keynames, $additionalKeyValues);
		}
		
		
		$table->product_id = $item->product_id;
		if ($table->load( $keynames, true, true ) )
		{
			$table->product_qty = $table->product_qty + $item->product_qty;
		}
		else
		{
			foreach($item as $key=>$value)
			{
				if(property_exists($table, $key))
				{
					$table->set($key, $value);
				}
			}
		}

		// Now for Eavs!!
		Citruscart::load( 'CitruscartHelperEav', 'helpers.eav' );
		$eavs = CitruscartHelperEav::getAttributes('products', $item->product_id, false, 2 );

		if(count($eavs))
		{
			foreach($eavs as $eav)
			{
				// Search for user edtable fields & user submitted value
				if(array_key_exists($eav->eavattribute_alias, $item))
				{
					$key = $eav->eavattribute_alias;
					$table->set($key, $item->$key);
				}
			}
		}

		$date = JFactory::getDate();
		$table->last_updated = $date->toSql();
		$table->session_id = $session->getId();

		if (!$table->save())
		{
			JError::raiseNotice('updateCart', $table->getError());
		}
		else
		{
			CitruscartHelperCarts::fixQuantities();
		}

		return $table;
	}

	/**
	 * TODO Remove this and all references to it
	 * because all carts now use the one carts model
	 *
	 * @deprecated as of version 6.3, will be removed in v6.4
	 *
	 * Fetches the name of the cart model to use
	 * @return string
	 */
	public static function getSuffix()
	{
		return 'Carts';
	}

	/**
	 *
	 * @param unknown_type $user_id
	 * @param unknown_type $session_id
	 * @return unknown_type
	 */
	function updateUserCartItemsSessionId( $user_id, $session_id )
	{
		$db = JFactory::getDBO();

		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();

		$query->update( "#__citruscart_carts" );
		$query->set( "`session_id` = '$session_id' " );
		$query->where( "`user_id` = '$user_id'" );
		$db->setQuery( (string) $query );
		if (!$db->query())
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 *
	 * @param $session_id
	 * @return unknown_type
	 */
	function deleteSessionCartItems( $session_id )
	{
		$db = JFactory::getDBO();

		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();

		$query->delete();
		$query->from( "#__citruscart_carts" );
		$query->where( "`session_id` = '$session_id' " );
		$query->where( "`user_id` <= '0'" );
		$db->setQuery( (string) $query );
		if (!$db->query())
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 *
	 * @param $session_id
	 * @param $user_id
	 * @return unknown_type
	 */
	function mergeSessionCartWithUserCart( $session_id, $user_id )
	{
		Citruscart::load( 'CitruscartHelperEav', 'helpers.eav' );
		$date = JFactory::getDate();
		$session = JFactory::getSession();
	  
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );
		$model->setState( 'filter_user_leq', '0' );
		$model->setState( 'filter_session', $session_id );
		$session_cartitems = $model->getList();
		$this->deleteSessionCartItems( $session_id );

		if (!empty($session_cartitems))
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$table = JTable::getInstance( 'Carts', 'CitruscartTable' );
			foreach ($session_cartitems as $session_cartitem)
			{
				$keynames = array();
				$keynames['user_id'] = $user_id;
				$keynames['product_id'] = $session_cartitem->product_id;
				$keynames['product_attributes'] = $session_cartitem->product_attributes;

				// fire plugin event: onGetAdditionalCartKeyValues
				//this event allows plugins to extend the multiple-column primary key of the carts table
				// load EAVs from the previous cart
				$additionalKeyValues = CitruscartHelperCarts::getAdditionalKeyValues( $session_cartitem, null, null );
				if (!empty($additionalKeyValues))
				{
					$keynames = array_merge($keynames, $additionalKeyValues);
				}
				
				$table->product_id = $session_cartitem->product_id;
				if ($table->load($keynames))
				{
					// the quantity as set in the session takes precedence
					$table->product_qty = $session_cartitem->product_qty;
				}
				else
				{
					
				    $eavs = CitruscartHelperEav::getAttributes( 'products', $session_cartitem->product_id );
					foreach ( $eavs as $eav )
					{
						$table->{$eav->eavattribute_alias} = CitruscartHelperEav::getAttributeValue( $eav, 'carts', $session_cartitem->cart_id, true, false ) ;
					}
					
					foreach($session_cartitem as $key=>$value)
					{
						if(property_exists($table, $key))
						{
							$table->set($key, $value);
						}
					}
					// this is a new cartitem, so set cart_id = 0
					$table->cart_id = '0';
				}

				$table->user_id = $user_id;
				$table->session_id = $session->getId();
				$table->last_updated = $date->toSql();

				if (!$table->save())
				{
					JError::raiseNotice('updateCart', $table->getError());
				}				
				$table->cart_id = '0';
			}
		}
	}

	/**
	 * Smartly updates the carts db table,
	 * updating quantity if a product_id+product_attributes entry exists for the user
	 * otherwise creating a new entry
	 *
	 * @deprecated as of version 6.3, will be removed in v6.4.0
	 *
	 * @param array
	 * @param boolean
	 * @param string
	 */
	function updateCart($cart = array(), $sync = false, $old_sessionid='', $new_userid='' )
	{
		$session = JFactory::getSession();
		$user = JFactory::getUser();

		if ($sync)
		{
			// get the cart based on session id
			if (!empty($old_sessionid))
			{
				$session_id2use = $old_sessionid;
			}
			else
			{
				$session_id2use = $session->getId();
			}
				
			JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
			$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );
			$model->setState( 'filter_user', '0' );
			$model->setState( 'filter_session', $session_id2use );
			$cart = $model->getList();
			$user_id = empty($new_userid) ? JFactory::getUser()->id : $new_userid;
			CitruscartHelperCarts::updateUserCartItemsSessionId( $user_id, $session_id );
		}

		if (!empty($cart))
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			foreach ($cart as $item)
			{
				$table = JTable::getInstance( 'Carts', 'CitruscartTable' );
				$user_id = empty($new_userid) ? JFactory::getUser()->id : $new_userid;
				$item->user_id = (empty($item->user_id)) ? $user_id : $item->user_id;

				$keynames = array();
				$keynames['user_id'] = $item->user_id;
				if (empty($item->user_id))
				{
					$keynames['session_id'] = $session->getId();
				}
				$keynames['product_id'] = $item->product_id;
				$keynames['product_attributes'] = $item->product_attributes;

				// fire plugin event: onGetAdditionalCartKeyValues
				//this event allows plugins to extend the multiple-column primary key of the carts table
				$additionalKeyValues = CitruscartHelperCarts::getAdditionalKeyValues( $item, null, null );
				if (!empty($additionalKeyValues))
				{
					$keynames = array_merge($keynames, $additionalKeyValues);
					//$table->setKeyNames($keynames); // not necessary
				}

				if ($table->load($keynames))
				{
					if ($sync)
					{
						// if syncing, the quantity as set in the session takes precedence
						$table->product_qty = $item->product_qty;
					}
					else
					{
						$table->product_qty = $table->product_qty + $item->product_qty;
					}
				}
				else
				{
					foreach($item as $key=>$value)
					{
						if(property_exists($table, $key))
						{
							$table->set($key, $value);
						}
					}
					$table->session_id = $session->getId();
				}

				$date = JFactory::getDate();
				$table->last_updated = $date->toSql();
				if (!$table->save())
				{
					JError::raiseNotice('updateCart', $table->getError());
				}
			}
		}

		CitruscartHelperCarts::fixQuantities();

		return true;
	}

	/**
	 * Remove the Item from the cart
	 *
	 * @param  session id
	 * @param  user id
	 * @param  product id
	 * @return null
	 */
	function removeCartItem( $session_id, $user_id=0, $product_id )
	{
		$db = JFactory::getDBO();

		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();
		$query->from( "#__citruscart_carts" );
		if (empty($user_id))
		{
			$query->where( "`session_id` = '$session_id' " );
		}
		$query->where( "`user_id` = '".$user_id."'" );
		$query->where( "`product_id` = '".$product_id."'" );

		$q_select = clone( $query );
		$q_select->select( 'cart_id, product_id' );
		$db->setQuery( (string) $q_select );
		$items = $db->loadObjectList();
		
		$query->delete();
		$db->setQuery( (string) $query );
		// TODO Make this report errors and return boolean
		$db->query();

		
		for( $i = 0, $c = count( $items ); $i < $c; $i++ )
		{
			JFactory::getApplication()->triggerEvent( 'onRemoveFromCart', array( $items[$i] ) );
		}
		return null;
	}

	/**
	 * Given an order_id, will remove the order's items from the user's cart
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function removeOrderItems( $order_id )
	{
		// load the order to get the user_id
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$cart = JTable::getInstance( 'Carts', 'CitruscartTable' );
		$model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
		$model->setId( $order_id );
		$order = $model->getItem();
		if (!empty($order->order_id))
		{
			
			// foreach orderitem
			foreach ($order->orderitems as $orderitem)
			{
				// remove from user's cart
				$ids = array('user_id'=>$order->user_id, 'product_id'=>$orderitem->product_id, 'product_attributes'=>$orderitem->orderitem_attributes );
				$cart->delete( $ids );
				JFactory::getApplication()->triggerEvent( 'onRemoveOrderItem', array( $orderitem ) );
			}
		}
	}

	/**
	 * Adjusts cart quantities based on availability
	 *
	 * @return unknown_type
	 */
	function fixQuantities()
	{
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$product = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
		$tableProduct = JTable::getInstance( 'Products', 'CitruscartTable' );

		$suffix = strtolower( CitruscartHelperCarts::getSuffix() );
		$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );

		switch ($suffix)
		{
			case 'sessioncarts':
			case 'carts':
			default:
				$user = JFactory::getUser();
				if (empty($user->id))
				{
					$session = JFactory::getSession();
					$model->setState('filter_session', $session->getId());
				}
				else
				{
					$model->setState('filter_user', $user->id);
				}
				 
				$cart = $model->getList( false, true );
				if (!empty($cart))
				{
					foreach ($cart as $cartitem)
					{
						$keynames = array();
						$keynames['user_id'] = $cartitem->user_id;
						if (empty($cartitem->user_id))
						{
							$keynames['session_id'] = $cartitem->session_id;
						}
						$keynames['product_id'] = $cartitem->product_id;
						$keynames['product_attributes'] = $cartitem->product_attributes;

						$tableProduct->load( $cartitem->product_id, true, false );
						if ($tableProduct->quantity_restriction )
						{
							$quantity = $cartitem->product_qty;
							$min = $tableProduct->quantity_min;
							$max = $tableProduct->quantity_max;

							if( $max )
							{
								if ($cartitem->product_qty > $max )
								{
									$quantity = $max;
								}
							}
							if( $min )
							{
								if ($cartitem->product_qty < $min )
								{
									$quantity = $min;
								}
							}
							// load table to adjust quantity in cart
							$table = JTable::getInstance( 'Carts', 'CitruscartTable' );
							//$table->load($keynames);
							$table->load( array('cart_id' => $cartitem->cart_id ), true, false );
							$table->product_id = $cartitem->product_id;
							$table->product_attributes = $cartitem->product_attributes;
							$table->user_id = $cartitem->user_id;
							$table->session_id = $cartitem->session_id;
							// adjust the cart quantity
							$table->product_qty = $quantity;
							$table->save();
						}

						if (empty($tableProduct->product_check_inventory))
						{
							// if this item doesn't check inventory, skip it
							continue;
						}

						$product->load( array('product_id'=>$cartitem->product_id, 'vendor_id'=>'0', 'product_attributes'=>$cartitem->product_attributes), true, false);
						if ($cartitem->product_qty > $product->quantity )
						{
							// enqueu a system message
							JFactory::getApplication()->enqueueMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $cartitem->product_name, $cartitem->product_qty ));

							// load table to adjust quantity in cart
							$table = JTable::getInstance( 'Carts', 'CitruscartTable' );
							$table->load($keynames, true, false);
							$table->product_id = $cartitem->product_id;
							$table->product_attributes = $cartitem->product_attributes;
							$table->user_id = $cartitem->user_id;
							$table->session_id = $cartitem->session_id;
							// adjust the cart quantity
							$table->product_qty = $product->quantity;
							$table->save();
						}
					}
				}

				break;
		}
	}

	/**
	 * Briefly, this method "converts" the items in the cart to a order Object
	 *
	 * @return array of OrderItem
	 */
	public static function getProductsInfo()
	{
	    Citruscart::load( "CitruscartHelperCarts", 'helpers.carts' );
	    $carthelper = new CitruscartHelperCarts();
	    	    
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		$product_helper = CitruscartHelperBase::getInstance( 'Product' );
	  
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel');

		$session = JFactory::getSession();
		$session_id = $session->getId();
		$user = JFactory::getUser();
		$model->setState('filter_user', $user->id );
		if (empty($user->id))
		{
			$model->setState('filter_session', $session_id );
		}

		Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
		$user_helper = CitruscartHelperBase::getInstance( 'User' );
		$filter_group = $user_helper->getUserGroup($user->id);
		$model->setState('filter_group', $filter_group );

		$cartitems = $model->getList();

		$productitems = array();
		foreach ($cartitems as $cartitem)
		{
			unset($productModel);
			$productModel = JModelLegacy::getInstance('Products', 'CitruscartModel');
			$filter_group = $user_helper->getUserGroup($user->id, $cartitem->product_id);
			$productModel->setState('filter_group', $filter_group );
			$productModel->setId($cartitem->product_id);
			if ($productItem = $productModel->getItem(false))
			{
				$productItem->price = $productItem->product_price = (isset($cartitem->product_price_override->override) ? !$cartitem->product_price_override->override : "") ? $cartitem->product_price : $productItem->price;

				//we are not overriding the price if its a recurring && price
				if(!$productItem->product_recurs && isset($cartitem->product_price_override->override) ? $cartitem->product_price_override->override : "")
				{
					// at this point, ->product_price holds the default price for the product,
					// but the user may qualify for a discount based on volume or date, so let's get that price override
					// TODO Shouldn't we remove this?  Is it necessary?  $cartitem has already done this in the carts model!
					$productItem->product_price_override = $product_helper->getPrice( $productItem->product_id, $cartitem->product_qty, $filter_group, JFactory::getDate()->toSql() );
					if (!empty($productItem->product_price_override))
					{
						$productItem->product_price = $productItem->product_price_override->product_price;
					}
				}

				if($productItem->product_check_inventory)
				{
					// using a helper file,To determine the product's information related to inventory
					$availableQuantity = $product_helper->getAvailableQuantity( $productItem->product_id, $cartitem->product_attributes );
					if( $availableQuantity->product_check_inventory && $cartitem->product_qty >$availableQuantity->quantity && $availableQuantity->quantity >=1) {
						JFactory::getApplication()->enqueueMessage(JText::sprintf("COM_CITRUSCART_CART_QUANTITY_ADJUSTED",$productItem->product_name, $cartitem->product_qty, $availableQuantity-> quantity ));
						$cartitem->product_qty = $availableQuantity->quantity;
					}

					// removing the product from the cart if it's not available
					if ($availableQuantity->quantity == 0)
					{
						if (empty($cartitem->user_id))
						{
							$carthelper->removeCartItem( $session_id, $cartitem->user_id, $cartitem->product_id );
						}
						else
						{
							$carthelper->removeCartItem( $cartitem->session_id, $cartitem->user_id, $cartitem->product_id );
						}
						JFactory::getApplication()->enqueueMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE") . " " .$productItem->product_name );
						continue;
					}
				}
				// TODO Push this into the orders object->addItem() method?
				$orderItem = JTable::getInstance('OrderItems', 'CitruscartTable');
				$orderItem->cart_id													= $cartitem->cart_id;
				$orderItem->product_id                      = $productItem->product_id;
				$orderItem->orderitem_sku                   = $cartitem->product_sku;
				$orderItem->orderitem_name                  = $productItem->product_name;
				$orderItem->orderitem_quantity              = $cartitem->product_qty;
				$orderItem->orderitem_price                 = $cartitem->product_price - $cartitem->orderitem_attributes_price;
				$orderItem->orderitem_attributes            = $cartitem->product_attributes;
				$orderItem->orderitem_attribute_names       = $cartitem->attributes_names;
				$orderItem->orderitem_attributes_price      = $cartitem->orderitem_attributes_price;
                $orderItem->orderitem_attributes_weight     = $cartitem->orderitem_attributes_weight;
                $orderItem->orderitem_weight                = $cartitem->product_weight;
				$orderItem->orderitem_final_price           = ($orderItem->orderitem_price + $orderItem->orderitem_attributes_price) * $orderItem->orderitem_quantity;
				$orderItem->orderitem_recurs                = $productItem->product_recurs;
				if( $productItem->product_recurs )
				{
					$orderItem->recurring_price                 = $productItem->recurring_price;
					$orderItem->recurring_payments              = $productItem->recurring_payments;
					$orderItem->recurring_period_interval       = $productItem->recurring_period_interval;
					$orderItem->recurring_period_unit           = $productItem->recurring_period_unit;
					$orderItem->recurring_trial                 = $productItem->recurring_trial;
					$orderItem->recurring_trial_period_interval = $productItem->recurring_trial_period_interval;
					$orderItem->recurring_trial_period_unit     = $productItem->recurring_trial_period_unit;
					$orderItem->recurring_trial_price           = $productItem->recurring_trial_price;	
				}
				
				
				
				$results = JFactory::getApplication()->triggerEvent( "onGetAdditionalOrderitemKeyValues", array( $cartitem ) );
				foreach ($results as $result)
				{
					foreach($result as $key=>$value)
					{
						$orderItem->set($key,$value);
					}
				}
				//adding plugin event, this is useful for editing the item before it is added for checkout from the cart. makes support for variable pricing
				 

	          	 JFactory::getApplication()->triggerEvent( "onAddOrderitemFromCart", array( $orderItem , $cartitem) );
			
				// TODO When do attributes for selected item get set during admin-side order creation?
				array_push($productitems, $orderItem);
			}
		}
		return $productitems;
	}

	/**
	 *
	 * Given a user_id or session_id,
	 * Will determine if the cart has a recurring item in it
	 * @param $cart_id
	 */
	function hasRecurringItem( $cart_id, $id_type='user_id' )
	{
		// get the cart's items
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );

		switch ($id_type)
		{
			case "session":
			case "session_id":
				$model->setState('filter_session', $cart_id);
				break;
			case "user":
			case "user_id":
			default:
				$model->setState('filter_user', $cart_id);
				break;
		}

		$cart_items = $model->getList( false, false );
		if (empty($cart_items))
		{
			return false;
		}

		// foreach
		foreach ($cart_items as $item)
		{
			if ($item->product_recurs)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Confirms that item can be added to cart
	 *
	 * @param $item     A CartItem object (equiv to a row in the __carts table)
	 * @param $cart_id
	 * @param $id_type
	 * @return unknown_type
	 */
	function canAddItem( $item, $cart_id, $id_type='user_id' )
	{
		$cart = array();
		$ordered_items = array();
		$active_subs = array();

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

		// does this cart item have any dependencies?
		// if not, return true
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
		$model->setState('filter_product_from', $item->product_id);
		$model->setState('filter_relations', array('requires', 'requires_past', 'requires_current') );
		if (!$relations = $model->getList())
		{
			return true;
		}

		// get the cart's items as well as user info (if logged in)
		$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );
		switch ($id_type)
		{
			case "session":
			case "session_id":
				$model->setState('filter_session', $cart_id);
				break;
			case "user":
			case "user_id":
			default:
				$model->setState('filter_user', $cart_id);
				// get the user's ordered items
				$oi_model = JModelLegacy::getInstance( 'OrderItems', 'CitruscartModel' );
				$oi_model->setState( 'filter_userid', $cart_id );
				if ($oi = $oi_model->getList())
				{
					foreach ($oi as $oi_item)
					{
						$ordered_items[] = $oi_item->product_id;
					}
				}
				// get the user's active subscriptions
				$subs_model = JModelLegacy::getInstance( 'Subscriptions', 'CitruscartModel' );
				$subs_model->setState("filter_userid", $cart_id );
				$subs_model->setState("filter_enabled", 1);
				if ($subs = $subs_model->getList())
				{
					foreach ($subs as $sub_item)
					{
						$active_subs[] = $sub_item->product_id;
					}
				}
				break;
		}

		// get the cart items

		$cart_items = $model->getList( false, false);
		if (!empty($cart_items))
		{
			// foreach
			foreach ($cart_items as $citem)
			{
				$cart[] = $citem->product_id;
			}
		}

		// $cart is now an array of product_ids that are in the cart
		// $active_subs is now an array of product_ids that are active subscriptions
		// $ordered_items is now an array of product_ids the user has purchased

		// foreach dependency, check that it is met
		foreach ($relations as $relation)
		{
			// switch relation_type
			switch ($relation->relation_type)
			{
				case "requires":
					// cart must already have required item in it
					if (!in_array($relation->product_id_to, $cart))
					{
						$this->setError( $relation->product_name_to . " " .JText::_('COM_CITRUSCART_IS_REQUIRED') );
						return false;
					}
					break;
				case "requires_past":
					// cart must already have required item in it
					// or user must have purchased it some time in the past
					if (!in_array($relation->product_id_to, $cart) && !in_array($relation->product_id_to, $ordered_items))
					{
						$this->setError( $relation->product_name_to . " " .JText::_('COM_CITRUSCART_IS_REQUIRED') );
						return false;
					}
					break;
				case "requires_current":
					// cart must already have required item in it
					// or user must have active subscription
					if (!in_array($relation->product_id_to, $cart) && !in_array($relation->product_id_to, $active_subs))
					{
						$this->setError( $relation->product_name_to . " " .JText::_('COM_CITRUSCART_IS_REQUIRED') );
						return false;
					}
					break;
			}
		}

		return true;
	}

	/**
	 * Checks the integrity of a cart
	 * to make sure all dependencies are met
	 *
	 * @param $cart_id
	 * @param $id_type
	 * @return unknown_type
	 */
	function checkIntegrity( $cart_id, $id_type='user_id' )
	{
		$user_id = 0;
		$session_id = '';

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

		// get the cart's items as well as user info (if logged in)
		$model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel' );
		switch ($id_type)
		{
			case "session":
			case "session_id":
				$model->setState('filter_session', $cart_id);
				$session_id = $cart_id;
				break;
			case "user":
			case "user_id":
			default:
				$model->setState('filter_user', $cart_id);
				$user_id = $cart_id;
				break;
		}

		$carthelper = new CitruscartHelperCarts();

		// get the cart items
		$cart_items = $model->getList( false, false);
		if (!empty($cart_items))
		{
			// foreach
			foreach ($cart_items as $citem)
			{
				if (!$carthelper->canAddItem( $citem, $cart_id, $id_type ))
				{
					JFactory::getApplication()->enqueueMessage( JText::_('COM_CITRUSCART_REMOVING_ITEM_FROM_CART_FOR_FAILED_DEPENDENCIES') . " - " . $citem->product_name, 'message' );
					$carthelper->removeCartItem($session_id, $user_id, $citem->product_id);
				}
			}
		}
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param $item
	 * @param $posted_values
	 * @param $index
	 * @return unknown_type
	 */
	static function getAdditionalKeyValues( $item, $posted_values, $index = null )
	{
		$keynames = array();
		
		$results = JFactory::getApplication()->triggerEvent( "onGetAdditionalCartKeyValues", array( $item, $posted_values, $index ) );  
		if (!empty($results))
		{
			foreach($results as $additionalKeyValues)
			{
				foreach($additionalKeyValues as $key=>$value)
				{
					$keynames[$key] = $value;
				}
			}
		}
		return $keynames;
	}
}