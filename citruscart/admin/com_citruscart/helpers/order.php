<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport( 'joomla.html.parameter' );
class CitruscartHelperOrder extends CitruscartHelperBase
{
	/**
	 * This is a wrapper method for after an orderpayment has been received
	 * that performs acts such as:
	 * enabling file downloads, removing items from cart,
	 * updating product quantities, etc
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function setOrderPaymentReceived( $order_id )
	{
		$errors = array();
		$error = false;

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$order = JTable::getInstance('Orders', 'CitruscartTable');
		$order->load( $order_id );
		$lang = JFactory::getLanguage();
		$lang->load( 'com_citruscart', JPATH_ADMINISTRATOR );

		if (empty($order->order_id))
		{
			// TODO we must make sure this class is always instantiated
			$this->setError( JText::_('COM_CITRUSCART_INVALID_ORDER_ID') );
			return false;
		}

		// optionally email the user
		$row = JTable::getInstance('OrderHistory', 'CitruscartTable');
		$row->order_id = $order_id;
		$row->order_state_id = $order->order_state_id;
		$row->notify_customer = Citruscart::getInstance()->get( 'autonotify_onSetOrderPaymentReceived', '0');
		$row->comments = JText::_('COM_CITRUSCART_PAYMENT_RECEIVED');
		if (!$row->save())
		{
			$errors[] = $row->getError();
			$error = true;
		}

		// Fire an onAfterSetOrderPaymentReceived event

		JFactory::getApplication()->triggerEvent( 'onAfterSetOrderPaymentReceived', array( $order_id ) );

		// Do orderTasks
		CitruscartHelperOrder::doCompletedOrderTasks( $order_id );

		if ($error)
		{
			$this->setError( implode( '<br/>', $errors ) );
			return false;
		}
		return true;
	}

	/**
	 * This would cancel an order
	 * and undo everything done by setOrderPaymentReceived()
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function cancelOrder( $order_id )
	{
		return true;
	}

	/**
	 * After a checkout has been completed
	 * and a payment has been received (instant)
	 * run this method to enable product downloads
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function enableProductDownloads( $order_id )
	{
		$error = false;
		$errorMsg = "";

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$productsModel = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );

		$model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
		$model->setId( $order_id );
		$order = $model->getItem();
		if ($order->orderitems)
		{
			foreach ($order->orderitems as $orderitem)
			{
				// if this orderItem product has productfiles that are enabled and only available when product is purchased
				$model = JModelLegacy::getInstance( 'ProductFiles', 'CitruscartModel' );
				$model->setState( 'filter_product', $orderitem->product_id );
				$model->setState( 'filter_enabled', 1 );
				//$model->setState( 'filter_purchaserequired', 1 ); //we still show the downloable file in the My Downloads area if the user completed the checkout
				if (!$items = $model->getList())
				{
					continue;
				}

				// then create a productdownloads table object
				foreach ($items as $item)
				{
					$productDownload = JTable::getInstance('ProductDownloads', 'CitruscartTable');
					$productDownload->product_id = $orderitem->product_id;
					$productDownload->productfile_id = $item->productfile_id;
					// Download Maximum Number is respective of the quantity purchased
					$productDownload->productdownload_max = ($item->max_download) * ($orderitem->orderitem_quantity);
					$productDownload->order_id = $order->order_id;
					$productDownload->user_id = $order->user_id;
					if (!$productDownload->save())
					{
						// track error
						$error = true;
						$errorMsg .= $productDownload->getError();
						// TODO What to do with this error
					}
				}
			}
		}
	}

	/**
	 * After a checkout has been completed
	 * and a payment has been received (instant) or scheduled (offline)
	 * run this method to update product quantities for the order
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function updateProductQuantities( $order_id, $delta='-' )
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$productsModel = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
		$model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
		$model->setId( $order_id );
		$order = $model->getItem();
		if (!empty($order->orderitems) && empty($order->quantities_updated))
		{
			foreach ($order->orderitems as $orderitem)
			{
				// update quantities
				// TODO Update quantity based on vendor_id
				$product = JTable::getInstance('ProductQuantities', 'CitruscartTable');
				$product->load( array('product_id'=>$orderitem->product_id, 'vendor_id'=>'0', 'product_attributes'=>$orderitem->orderitem_attributes), true, false);

				$productsTable = JTable::getInstance( 'Products', 'CitruscartTable' );
				$productsTable->load($orderitem->product_id);


				// Check if it has inventory enabled
				if (!$productsTable->product_check_inventory  || empty($product->product_id))
				{
					// do not update quantities
					continue;
				}

				switch ($delta)
				{
					case "+":
						$new_quantity = $product->quantity + $orderitem->orderitem_quantity;
						break;
					case "-":
					default:
						$new_quantity = $product->quantity - $orderitem->orderitem_quantity;
						break;
				}

				// no product made infinite accidentally
				if ($new_quantity < 0)
				{
					$new_quantity = 0;
				}
				$product->quantity = $new_quantity;
				$product->save();

				// send mail to notify low quantity
				$config = Citruscart::getInstance();
				$low_stock_notify_enabled = $config->get('low_stock_notify', '0');
				$low_stock_notify_value   = $config->get('low_stock_notify_value', '0');

				if ( ( $low_stock_notify_enabled ) && ( $new_quantity <= ( ( int ) $low_stock_notify_value ) ) )
				{
					Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
					$helper = CitruscartHelperBase::getInstance( 'Email' );
					$helper->sendEmailLowQuanty( $product->productquantity_id );
				}
			}

			$row = $model->getTable();
			$row->load(array('order_id'=>$order->order_id));
			$row->quantities_updated = 1;
			$row->store();
		}

	}

	/**
	 * Finds the prev & next items in a list of orders
	 *
	 * @param $id   product id
	 * @return array( 'prev', 'next' )
	 */
	function getSurrounding( $id )
	{
		$return = array();

		/* Get the application */
		$app = JFactory::getApplication();

		$prev = $app->input->getInt('prev');

		$next = $app->input->getInt('next');

		//$prev = intval( JRequest::getVar( "prev" ) );
		//$next = intval( JRequest::getVar( "next" ) );

		if ($prev || $next)
		{
			$return["prev"] = $prev;
			$return["next"] = $next;
			return $return;
		}


		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
		$ns = $app->getName().'::'.'com.citruscart.model.'.$model->getTable()->get('_suffix');
		$state = array();

		$config = Citruscart::getInstance();

		$state['limit']     = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
		$state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.created_date', 'cmd');
		$state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
		$state['filter_orderstate']     = $app->getUserStateFromRequest($ns.'orderstate', 'filter_orderstate', '', '');
		$state['filter_user']         = $app->getUserStateFromRequest($ns.'user', 'filter_user', '', '');
		$state['filter_userid']         = $app->getUserStateFromRequest($ns.'userid', 'filter_userid', '', '');
		$state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
		$state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
		$state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
		$state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
		$state['filter_datetype']   = $app->getUserStateFromRequest($ns.'datetype', 'filter_datetype', '', '');
		$state['filter_total_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_total_from', '', '');
		$state['filter_total_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_total_to', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		$rowset = $model->getList();

		$found = false;
		$prev_id = '';
		$next_id = '';

		for ($i=0; $i < count($rowset) && empty($found); $i++)
		{
			$row = $rowset[$i];
			if ($row->order_id == $id)
			{
				$found = true;
				$prev_num = $i - 1;
				$next_num = $i + 1;
				if (isset($rowset[$prev_num]->order_id)) { $prev_id = $rowset[$prev_num]->order_id; }
				if (isset($rowset[$next_num]->order_id)) { $next_id = $rowset[$next_num]->order_id; }

			}
		}

		$return["prev"] = $prev_id;
		$return["next"] = $next_id;
		return $return;
	}

	/**
	 * Returns a DSCParameter Formatted string representing the currency
	 *
	 * @param $currency_id currency_id
	 * @return $string DSCParameter formatted string
	 */

	public static function currencyToParameters($currency_id)
	{
		if (!is_numeric($currency_id)) {
    		return false;
		}

		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance('Currencies', 'CitruscartModel' );
		$table = $model->getTable();

		// Load the currency
		if (!$table->load($currency_id)) {
    		return false;
		}

		// Convert this into a DSCParameter formatted string
		// a bit rough, but works smoothly and is extensible (works even if you add another parameter to the curremcy table
		$currency_parameters = $table;
		unset($table);
		unset($currency_parameters->currency_id);
		unset($currency_parameters->created_date);
		unset($currency_parameters->modified_date);
		unset($currency_parameters->currency_enabled);

		$param = new DSCParameter('');
		$param->loadObject($currency_parameters);

		return $param->__toString();
	}

	/**
	 * This method for after an orderpayment has been received when the admin click on the
	 * that performs acts such as:
	 * enabling file downloads
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function doCompletedOrderTasks( $order_id )
	{
		$errors = array();
		$error = false;

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$order = JTable::getInstance('Orders', 'CitruscartTable');
		$order->load( $order_id );

		if (empty($order->order_id))
		{
			// TODO we must make sure this class is always instantiated
			$this->setError( JText::_('COM_CITRUSCART_INVALID_ORDER_ID') );
			return false;
		}

		// Fire an doCompletedOrderTasks event

		JFactory::getApplication()->triggerEvent( 'doCompletedOrderTasks', array( $order_id ) );

		// 0. Enable One-Time Purchase Subscriptions
		CitruscartHelperOrder::enableNonRecurringSubscriptions( $order_id );

		// 1. Update quantities
		CitruscartHelperOrder::updateProductQuantities( $order_id, '-' );

		// 2. remove items from cart
		Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
		CitruscartHelperCarts::removeOrderItems( $order_id );

		// 3. add productfiles to product downloads
		CitruscartHelperOrder::enableProductDownloads( $order_id );

		// 4. do SQL queries
		$helper = CitruscartHelperBase::getInstance( 'Sql' );
		$helper->processOrder( $order_id );

		// register commission if amigos is installed
		$helper = CitruscartHelperBase::getInstance( 'Amigos' );
		$helper->createCommission( $order_id );

		// change ticket limits if billets is installed
		$helper = CitruscartHelperBase::getInstance( 'Billets' );
		$helper->processOrder( $order_id );

		// add to JUGA Groups if JUGA installed
		$helper = CitruscartHelperBase::getInstance( 'Juga' );
		$helper->processOrder( $order_id );

		// change core ACL if set
		$helper = CitruscartHelperBase::getInstance( 'User' );
		$helper->processOrder( $order_id );

		// do Ambra Subscriptions Integration processes
		$helper = CitruscartHelperBase::getInstance( 'Ambrasubs' );
		$helper->processOrder( $order_id );

		// increase the hit counts for coupons in the order
		$helper = CitruscartHelperBase::getInstance( 'Coupon' );
		$helper->processOrder( $order_id );

		if ($error)
		{
			$this->setError( implode( '<br/>', $errors ) );
			return false;
		}
		else
		{
			$order->completed_tasks = '1';
			$order->store();
			return true;
		}
	}

	/**
	 * Gets an order, formatted for email
	 *
	 * return html
	 */
	public static function getOrderHtmlForEmail( $order_id )
	{
		$app = JFactory::getApplication();
		JPluginHelper::importPlugin( 'citruscart' );

		JLoader::register( "CitruscartViewOrders", JPATH_SITE."/components/com_citruscart/views/orders/view.html.php" );

		// tells JView to load the front-end view, and enable template overrides
		$config = array();
		$config['base_path'] = JPATH_SITE."/components/com_citruscart";
		if ($app->isAdmin())
		{
			// finds the default Site template
			$db = JFactory::getDbo();
            if (version_compare(JVERSION, '1.6.0', 'ge')) {
                // Joomla! 1.6+ code here
                $db -> setQuery("SELECT `template` FROM #__template_styles WHERE `home` = '1' AND `client_id` = '0';");
            } else {
                // Joomla! 1.5 code here
                $db -> setQuery("SELECT `template` FROM #__templates_menu WHERE `menuid` = '0' AND `client_id` = '0';");
            }

			$template = $db->loadResult();

			jimport('joomla.filesystem.file');
			if (JFile::exists(JPATH_SITE.'/templates/'.$template.'/html/com_citruscart/orders/email.php'))
			{
				// (have to do this because we load the same view from the admin-side Orders view, and conflicts arise)
				$config['template_path'] = JPATH_SITE.'/templates/'.$template.'/html/com_citruscart/orders';
			}
		}
		$view = new CitruscartViewOrders( $config );

		$model = Citruscart::getClass("CitruscartModelOrders", "models.orders");
		$model->setId( $order_id );
		$order = $model->getItem();

		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', false);
		$view->setModel( $model, true );
		$view->assign( 'order', $order );
		$view->setLayout( 'email' );

		// Perform the requested task
		ob_start();
		$view->display();
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * After a checkout has been completed
	 * and a payment has been received (instant)
	 * run this method to enable
	 * any non-recurring subscriptions that were created when the order was saved
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function enableNonRecurringSubscriptions( $order_id )
	{
		$error = false;
		$errorMsg = "";

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
		$model->setId( $order_id );
		$model_issues = null;
		$order = $model->getItem();
		if ($order->orderitems)
		{
			foreach ($order->orderitems as $orderitem)
			{
				// if this orderItem created a subscription, enable it
				if (!empty($orderitem->orderitem_subscription))
				{
					// these are only for one-time payments that create subscriptions
					// recurring payment subscriptions are handled differently - by the payment plugins
					$subscription = JTable::getInstance('Subscriptions', 'CitruscartTable');
					$subscription->load( array( 'orderitem_id'=>$orderitem->orderitem_id ) );
					if (!empty($subscription->subscription_id))
					{
						$subscription->subscription_enabled = '1';
						Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
						$product = CitruscartHelperProduct::load( $subscription->product_id, true, false );

						if( $product->subscription_period_unit == 'I' ) // subscription by issue => calculate ID of the end issue (create the rest of them if they dont exist)
						{
							$model_issues = JModelLegacy::getInstance( 'ProductIssues', 'CitruscartModel' );
							$subscription->subscription_issue_end_id = $model_issues->getEndIssueId( $subscription->product_id, $product->subscription_period_interval );
						}
						if (!$subscription->save())
						{
							// track error
							$error = true;
							$errorMsg .= $subscription->getError();
							// TODO What to do with this error
						}
						else
						{

							JFactory::getApplication()->triggerEvent( 'onAfterEnableSubscription', array( $subscription ) );
						}
					}
				}
			}
		}
	}

	public static function onDisplayOrderItems($orderitems)
	{
		//trigger the onDisplayOrderItem for each orderitem


		$onDisplayOrderItem = array();
		$index = 0;
		foreach( $orderitems as $orderitem)
		{
			ob_start();
			JFactory::getApplication()->triggerEvent( 'onDisplayOrderItem', array( $index, $orderitem ) );
			$orderItemContents = ob_get_contents();
			ob_end_clean();
			if (!empty($orderItemContents))
			{
				$onDisplayOrderItem[$index] = $orderItemContents;
			}
			$index++;
		}

		return $onDisplayOrderItem;
	}

	/*
	 * Method to display order number or order ID (in case there is no order number)
	 *
	 * @param $order CitruscartTableOrder object
	 *
	 * @return string Order number (or order ID in case of order number is not present)
	 */
	public static function displayOrderNumber( $order )
	{
		return empty( $order->order_number ) ? $order->order_id : $order->order_number;
	}

	/**
	 * Method to get date of the first or the last order
	 *
	 * @access private
	 * @return void
	 */
	public static function getDateMarginalOrder( $states, $order = 'ASC' )
	{
		$db = JFactory::getDbo();
		$today = CitruscartHelperBase::getToday();

		$q = new CitruscartQuery();
		$q->select( 'tbl.created_date AS date' );
		$q->from( '#__citruscart_orders AS tbl' );
		$q->where(" tbl.order_state_id IN ( ".$states." ) " );
		$q->order(" tbl.created_date ".$order );

		$db->setQuery( (string) $q );
		$return = $db->loadObject();
		if( $return )
			$return = $return->date;
		else
			$return = $today;

		return $return;
	}

	/**
	 * Method to calculate hash which will be used to access guest invoices
	 *
	 * @param $order		Object of type CitruscartTableOrders
	 *
	 * @return	String containing the calculated hash
	 */
	public static function getHashInvoice( $order )
	{
		$secret = Citruscart::getInstance()->get( 'secret_word', '' );
		$hash = $order->shipping_method_id.$order->order_total.$order->order_id.$order->ip_address.$secret.
						$order->order_state_id.$order->completed_task.$order->user_id;

		return sha1( base64_encode( $hash ) );
	}
}
