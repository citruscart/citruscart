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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerOrders extends CitruscartController
{
	var $_order                = null; // a TableOrders() object
	var $initial_order_state   = 1; // Set in constructor
	var $billing_input_prefix  = 'billing_input_';
	var $shipping_input_prefix = 'shipping_input_';

	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'orders');
		$this->registerTask( 'edit', 'view' );
		$this->registerTask( 'prev', 'jump' );
		$this->registerTask( 'next', 'jump' );
		$this->registerTask( 'print', 'view' );
		$this->registerTask( 'new', 'selectUser' );
		$this->registerTask( 'add', 'selectUser' );
		$this->registerTask( 'update_status', 'updateStatus' );
		$this->registerTask( 'resend_email', 'resendEmail' );

		// create the order object
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$this->_order = JTable::getInstance('Orders', 'CitruscartTable');
		$this->initial_order_state = Citruscart::getInstance()->get('pending_order_state', '1'); //pending
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.created_date', 'cmd');
		$state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
		$state['filter_orderstate'] 	= $app->getUserStateFromRequest($ns.'orderstate', 'filter_orderstate', '', '');
		$state['filter_user'] 	      = $app->getUserStateFromRequest($ns.'user', 'filter_user', '', '');
		$state['filter_id_from'] 	= $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
		$state['filter_id_to'] 		= $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
		$state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
		$state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
		$state['filter_datetype']   = $app->getUserStateFromRequest($ns.'datetype', 'filter_datetype', '', '');
		$state['filter_total_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_total_from', '', '');
		$state['filter_total_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_total_to', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * Displays item
	 * @return void
	 */
	function view($cachable=false, $urlparams = false)
	{
		Citruscart::load( 'CitruscartUrl', 'library.url' );

		$model = $this->getModel( $this->get('suffix') );
		$order = $model->getTable( 'orders' );
		$order->load( $model->getId() );
		$orderitems = $order->getItems();
		$row = $model->getItem();

		// Get the shop country name
		$row->shop_country_name = "";
		$countryModel = JModelLegacy::getInstance('Countries', 'CitruscartModel');
		$countryModel->setId(Citruscart::getInstance()->get('shop_country'));
		$countryItem = $countryModel->getItem();
		if ($countryItem && Citruscart::getInstance()->get('shop_country'))
		{
			$row->shop_country_name = $countryItem->country_name;
		}

		// Get the shop zone name
		$row->shop_zone_name = "";
		$zoneModel = JModelLegacy::getInstance('Zones', 'CitruscartModel');
		$zoneModel->setId(Citruscart::getInstance()->get('shop_zone'));
		$zoneItem = $zoneModel->getItem();
		if ($zoneItem && Citruscart::getInstance()->get('shop_zone'))
		{
			$row->shop_zone_name = $zoneItem->zone_name;
		}

		//retrieve user information and make available to page
		if (!empty($row->user_id))
		{
			//get the user information from jos_users and jos_Citruscart_userinfo
			$userModel  = JModelLegacy::getInstance( 'Users', 'CitruscartModel' );
			$userModel->setId($row->user_id);
			$userItem = $userModel->getItem();
			if ($userItem)
			{
				$row->userinfo = $userItem;
			}
		}

		$view   = $this->getView('orders', 'html'  );

		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		if ($this->getTask() == 'print')
		{
			$view->setLayout( 'print' );
		}
		else
		{
			$view->setLayout( 'view' );
		}

		$model->emptyState();
		$this->_setModelState();
		$surrounding = $model->getSurrounding( $model->getId() );
		$view->assign( 'surrounding', $surrounding );

		//START onDisplayOrderItem: trigger plugins for extra orderitem information
		if (!empty($orderitems))
		{
			Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
			$onDisplayOrderItem = CitruscartHelperOrder::onDisplayOrderItems($orderitems);

			$view->assign( 'onDisplayOrderItem', $onDisplayOrderItem );
		}
		//END onDisplayOrderItem

		$config = Citruscart::getInstance();
		$show_tax = $config->get('display_prices_with_tax');
		$view->assign( 'show_tax', $show_tax );
		$view->assign( 'using_default_geozone', false );

		if ($show_tax)
		{
			$geozones = $order->getBillingGeoZones();
			if (empty($geozones))
			{
				// use the default
				$view->assign( 'using_default_geozone', true );
				$table = JTable::getInstance('Geozones', 'CitruscartTable');
				$table->load(array('geozone_id'=>$config->get('default_tax_geozone')));
				$geozones = array( $table );
			}

			Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
			foreach ($orderitems as &$item)
			{
				$taxtotal = ($item->orderitem_tax / $item->orderitem_quantity);
				$item->orderitem_price = $item->orderitem_price + floatval( $item->orderitem_attributes_price ) + $taxtotal;
				$item->orderitem_final_price = $item->orderitem_price * $item->orderitem_quantity;
				$order->order_subtotal += ($taxtotal * $item->orderitem_quantity);
			}
		}
		$view->assign( 'order', $order );
		$view->setTask(true);
		parent::view($cachable, $urlparams);
	}

	/**
	 * Checks in the current item and displays the previous/next one in the list
	 * @return unknown_type
	 */
	function jump()
	{
		$app = JFactory::getApplication();
		$model  = $this->getModel( $this->get('suffix') );
		$row = $model->getTable();
		$row->load( $model->getId() );
		if (isset($row->checked_out) && !JTable::isCheckedOut( JFactory::getUser()->id, $row->checked_out) )
		{
			$row->checkin();
		}
		$task = $app->input->getString( "task" );
		$redirect = "index.php?option=com_citruscart&view=orders";
		Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
		$surrounding = CitruscartHelperOrder::getSurrounding( $model->getId() );
		switch ($task)
		{
			case "prev":
				if (!empty($surrounding['prev']))
				{
					$redirect .= "&task=view&id=".$surrounding['prev'];
				}
				break;
			case "next":
				if (!empty($surrounding['next']))
				{
					$redirect .= "&task=view&id=".$surrounding['next'];
				}
				break;
		}
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 *
	 * Redirect to users view if no user selected
	 *
	 * @return unknown_type
	 */
	function selectUser()
	{
		// clear the session variables
		$app = JFactory::getApplication();
		$user_id= $app->input->get('userid');
		//$user_id = JRequest::getVar( 'userid', '', 'get', 'string' );
		if (empty($user_id))
		{
			$redirect = 'index.php?option=com_citruscart&view=users';
			$redirect = JRoute::_( $redirect, false );
			$this->setRedirect( $redirect, JText::_('COM_CITRUSCART_PLEASE_SELECT_USER_THEN_CLICK_CREATE_ORDER'), 'message' );
		}
		else
		{
			$app->input->set( 'hidemainmenu', '1' );
			$app->input->set( 'layout', 'form' );
			parent::display();
		}
	}

	/**
	 * Loads view for assigning product to order
	 *
	 * @return unknown_type
	 */
	function selectProducts()
	{
		$this->set('suffix', 'products');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_category']   = $app->getUserStateFromRequest($ns.'category', 'filter_category', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$view = $this->getView( 'orders', 'html' );
		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=orders&task=selectproducts&tmpl=component" );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->setLayout( 'selectproducts' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Adds products to the session variable
	 * and closes the select products lightbox
	 *
	 * @return unknown_type
	 */
	function addProducts()
	{
		$app = JFactory::getApplication();
		// get the posted variables
		//$cids = JRequest::getVar('cid', array(0), 'request', 'array');
		$cids =$app->input->get('cid', array(0), 'request', 'array');
		$quantity = $app->input->get('quantity', array(0), 'request', 'array');
		// get the session variables
		$order_products = $this->getSessionVariable('order_products', array());
		$order_quantities = $this->getSessionVariable('order_quantities', array());

		jimport('joomla.utilities.arrayhelper'); //joomla/utilities/arrayhelper.php (line 23)
		$order_products = JArrayHelper::fromObject( $order_products );
		$order_quantities = JArrayHelper::fromObject( $order_quantities );

		if (empty($order_products)) { $order_products = array(); }
		if (empty($order_quantities)) { $order_quantities = array(); }

		foreach ($cids as $cid)
		{
			if (empty($cid)) { continue; }

			if (!in_array($cid, $order_products))
			{
				// Add the posted products to the session variable if it doesn't exist
				$order_products[$cid] = $cid;
				$order_quantities[$cid] = $quantity[$cid];
			}
			else
			{
				$order_products[$cid] = $cid;
				// If it exists, update quantity
				if (!array_key_exists($cid, $order_quantities))
				{
					$order_quantities[$cid] = $quantity[$cid];
				}
				else
				{
					$order_quantities[$cid] += $quantity[$cid];
				}
			}
		}

		// save to the session
		$this->setSessionVariable('order_products', $order_products);
		$this->setSessionVariable('order_quantities', $order_quantities);

		// set the close window variable so the view closes the lightbox
		//JRequest::setVar('windowtask', 'close');
		$app->input->set('windowtask', 'close');

		$model = $this->getModel( $this->get('suffix') );
		$view = $this->getView( 'orders', 'html' );
		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=orders&task=selectproducts&tmpl=component" );
		$view->setModel( $model, true );
		$view->setLayout( 'close' );
		$view->setTask(true);
		$view->display();
	}

	/**
	 * Gets the products from the session variable
	 * and returns formatted HTML
	 *
	 * @return unknown_type
	 */
	function getProducts()
	{
		$row = new JObject();
		// TODO This needs to reflect the order's selected currency
		$row->orderitems = $this->getProductsInfo();

		$this->set('suffix', 'orders');
		$model = $this->getModel( $this->get('suffix') );
		$view   = $this->getView( 'orders', 'html' );
		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->setModel( $model, true );
		$view->assign( 'row', $row );
		$view->assign( 'state', $model->getState() );
		$view->setLayout( 'orderproducts' );

		ob_start();
		$view->setTask(true); $view->display();
		$html = ob_get_contents();
		ob_end_clean();

		$response = array();
		$response['msg'] = $html;
		$response['error'] = '';

		echo ( json_encode( $response ) );

		return;
	}

	/**
	 * Updates product quantities in session based on inputs from form
	 * and returns HTML
	 *
	 * @return unknown_type
	 */
	function updateProductQuantities()
	{
		$app = JFactory::getApplication();
		// get elements from post
		$element_name = $app->input->getString( 'elements');

		$elements = json_decode( preg_replace('/[\n\r]+/', '\n',$element_name  ) );
		// convert elements to array that can be binded
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$values = CitruscartHelperBase::elementsToArray( $elements );

		$cids = $values['products'];
		$quantities = $values['quantity'];
		$checked = $values['_checked'];

		// get the session variables
		$order_products = $this->getSessionVariable('order_products', array());
		$order_quantities = $this->getSessionVariable('order_quantities', array());

		jimport('joomla.utilities.arrayhelper'); //joomla/utilities/arrayhelper.php (line 23)
		$order_products = JArrayHelper::fromObject( $order_products );
		$order_quantities = JArrayHelper::fromObject( $order_quantities );

		foreach ($cids as $cid)
		{
			if (empty($cid)) { continue; }

			$order_quantities[$cid] = $quantities[$cid];
		}

		// save to the session
		$this->setSessionVariable('order_products', $order_products);
		$this->setSessionVariable('order_quantities', $order_quantities);

		return $this->getProducts();
	}

	/**
	 * Removes selected products from session
	 * and returns HTML
	 *
	 * @return unknown_type
	 */
	function removeProducts()
	{
		$app = JFactory::getApplication();

		// get elements from post
		$post = $app->input->get($_POST);

		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $post ) );
		// convert elements to array that can be binded
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$values = CitruscartHelperBase::elementsToArray( $elements );

		$cids = $values['products'];
		$checked = $values['_checked'];

		// get the session variables
		$order_products = $this->getSessionVariable('order_products', array());
		$order_quantities = $this->getSessionVariable('order_quantities', array());

		jimport('joomla.utilities.arrayhelper'); //joomla/utilities/arrayhelper.php (line 23)
		$order_products = JArrayHelper::fromObject( $order_products );
		$order_quantities = JArrayHelper::fromObject( $order_quantities );

		foreach (@$cids as $cid)
		{
			if (empty($cid)) { continue; }

			if (in_array($cid, $checked['products']))
			{
				// remove the product
				unset($order_products[$cid]);
				unset($order_quantities[$cid]);
			}
		}

		// save to the session
		$this->setSessionVariable('order_products', $order_products);
		$this->setSessionVariable('order_quantities', $order_quantities);

		return $this->getProducts();
	}

	/**
	 * Gets the products from the session variable
	 *
	 * @return unknown_type
	 */
	function getProductsInfo( $currency_id=0, $geozone_id=0 )
	{
		// get the session variables
		$order_products = $this->getSessionVariable('order_products', array());
		$order_quantities = $this->getSessionVariable('order_quantities', array());

		jimport('joomla.utilities.arrayhelper'); //joomla/utilities/arrayhelper.php (line 23)
		$order_products = JArrayHelper::fromObject( $order_products );
		$order_quantities = JArrayHelper::fromObject( $order_quantities );

		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$items = array();

		if (empty($order_products)) { $order_products = array(); }
		if (empty($order_quantities)) { $order_quantities = array(); }

		foreach ($order_products as $product)
		{
			unset($productModel);
			$productModel = JModelLegacy::getInstance('Products', 'CitruscartModel');
			$productModel->setId( $product );
			if ($productItem = $productModel->getItem())
			{
				// TODO Push this into the orders object->addItem() method?
				$orderItem = JTable::getInstance('OrderItems', 'CitruscartTable');
				$orderItem->product_id             = $productItem->product_id;
				$orderItem->orderitem_sku          = $productItem->product_sku;
				$orderItem->orderitem_name         = $productItem->product_name;
				$orderItem->orderitem_quantity     = $order_quantities[$product];
				$orderItem->orderitem_price        = $productItem->price;
				$orderItem->orderitem_final_price  = $productItem->price * $orderItem->orderitem_quantity;
				// TODO When do attributes for selected item get set during admin-side order creation?
				array_push($items, $orderItem);
			}
		}

		return $items;
	}

	/**
	 * Calculates the order total line items
	 * and returns formatted HTML
	 * Expects to be called by Ajax
	 *
	 * @return unknown_type
	 */

	function getOrderTotals()
	{
		$app = JFactory::getApplication();
		// get elements from post

		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $app->input->getString( 'elements') ) );
		//$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $app->input->getString( 'elements') ) );

		//$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );

		// convert elements to array that can be binded
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$values = CitruscartHelperBase::elementsToArray( $elements );

		// get the order object so we can populate it
		$order = $this->_order; // a TableOrders object (see constructor)

		// bind what you can from the post
		$order->bind( $values );

		// set the currency
		//TODO: Change this to currency_id in the form
		$order->currency_id = $values['order_currency_id'];

		// set the shipping method
		$order->shipping_method_id = $values['_checked']['shipping_method_id'];

		// set the order's addresses based on the form inputs
		$this->setAddresses( $values );

		// get the items and add them to the order
		$items = $this->getProductsInfo( $order->currency_id, $order->getBillingGeozone() );
		foreach ($items as $item)
		{
			$order->addItem( $item );
		}

		// get the order totals
		$order->calculateTotals();

		$model = $this->getModel( $this->get('suffix') );
		$view = $this->getView( 'orders', 'html' );
		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $order );
		$view->assign( 'shipping_total', $order->getShippingTotal() );
		$view->setLayout( 'ordertotals' );

		ob_start();
		$view->setTask(true); $view->display();
		$html = ob_get_contents();
		ob_end_clean();

		$response = array();
		$response['msg'] = $html;
		$response['error'] = '';

		echo ( json_encode( $response ) );

		return;
	}

	/**
	 * Sets a json_encoded session variable to value
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return void
	 */
	function setSessionVariable($key, $value)
	{
		$session = JFactory::getSession();
		$session->set($key, json_encode($value));
	}

	/**
	 * Gets json_encoded session variable
	 *
	 * @param str $key
	 * @return mixed
	 */
	function getSessionVariable($key, $default=null)
	{
		$session = JFactory::getSession();
		$sessionvalue = $default;
		if ($session->has($key))
		{
			$sessionvalue = $session->get($key);
			if (!empty($sessionvalue))
			{
				$sessionvalue = json_decode($sessionvalue);
			}
		}
		return $sessionvalue;
	}

	/**
	 * Sets the order object's address values based on the form inputs
	 *
	 * @param $values
	 * @return unknown_type
	 */
	function setAddresses( $values )
	{
		$app = JFactory::getApplication();
		$order = $this->_order; // a TableOrders object (see constructor)

		$currency_id            = $values['order_currency_id'];
		$billing_address_id     = $values['billing_address_id'];
		$shipping_address_id    = $values['shipping_address_id'];
		$shipping_method_id     = $values['_checked']['shipping_method_id'];
		$same_as_billing        = $values['_checked']['sameasbilling'];
		$user_id                = $values['user_id'];
		$billing_input_prefix   = $this->billing_input_prefix;
		$shipping_input_prefix  = $this->shipping_input_prefix;

		$billing_zone_id = 0;
		$billingAddressArray = $this->getAddress( $billing_address_id, $billing_input_prefix, $values );
		if (array_key_exists('zone_id', $billingAddressArray))
		{
			$billing_zone_id = $billingAddressArray['zone_id'];
		}

		//SAME AS BILLING
		if ($same_as_billing) //if shipping should be the same as billing
		{
			$shipping_address_id = $billing_address_id;
			$shipping_input_prefix = $billing_input_prefix;
		}

		//SHIPPING ADDRESS: get shipping address from dropdown or form (depending on selection)
		$shipping_zone_id = 0;
		$shippingAddressArray = $this->getAddress($shipping_address_id, $shipping_input_prefix, $values);
		if (array_key_exists('zone_id', $shippingAddressArray))
		{
			$shipping_zone_id = $shippingAddressArray['zone_id'];
		}

		// keep the array for binding during the save process
		$this->_billingAddressArray = $this->filterArrayUsingPrefix($billingAddressArray, '', 'billing_', true);
		$this->_shippingAddressArray = $this->filterArrayUsingPrefix($shippingAddressArray, '', 'shipping_', true);

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$billingAddress = JTable::getInstance('Addresses', 'CitruscartTable');
		$shippingAddress = JTable::getInstance('Addresses', 'CitruscartTable');

		// set the order billing address
		$billingAddress->bind( $billingAddressArray );
		$billingAddress->user_id = $user_id;
		$order->setAddress( $billingAddress, 'billing' );

		// set the order shipping address
		$shippingAddress->bind( $shippingAddressArray );
		$shippingAddress->user_id = $user_id;
		$order->setAddress( $shippingAddress, 'shipping' );
	}

	/**
	 * Gets the address from the form
	 * using the address from the dropdown if selected
	 * otherwise using the address from the inputs
	 *
	 * @param int $address_id
	 * @param string $input_prefix
	 * @param array $form_input_array
	 * @return array
	 */
	function getAddress( $address_id, $input_prefix, $form_input_array )
	{
		$addressArray = array();
		if (!empty($address_id))
		{
			$addressArray = $this->retrieveAddressIntoArray($address_id);
		}
		else
		{
			$addressArray = $this->filterArrayUsingPrefix($form_input_array, $input_prefix, '', false );
		}
		return $addressArray;
	}

	/**
	 * Loads an address row and converts it to an array
	 *
	 * @param unknown_type $address_id
	 * @return unknown_type
	 */
	function retrieveAddressIntoArray( $address_id )
	{
		$model = JModelLegacy::getInstance( 'Addresses', 'CitruscartModel' );
		$model->setId($address_id);
		$item = $model->getItem();
		return get_object_vars( $item );

	}

	/**
	 * Parses array from order form using prefix.
	 * Optionally appends prefix to each key in array.
	 *
	 * @param $oldArray
	 * @param $old_prefix
	 * @param $new_prefix
	 * @param $append
	 * @return unknown_type
	 */
	function filterArrayUsingPrefix( $oldArray, $old_prefix, $new_prefix, $append )
	{
		// create array with input form keys and values
		$address_input = array();

		foreach ($oldArray as $key => $value)
		{
			if (($append) || (strpos($key, $old_prefix) !== false))
			{
				$new_key = '';
				if ($append){$new_key = $new_prefix.$key;}
				else{
					$new_key = str_replace($old_prefix, $new_prefix, $key);
				}
				if (strlen($new_key)>0){
					$address_input[$new_key] = $value;
				}
			}
		}
		return $address_input;
	}

	/**
	 * Saves an item and redirects based on task
	 * @return void
	 */
	function save()
	{
		$app = JFactory::getApplication();
		$values = $app->input->get($_POST);
		//$values = JRequest::get('post');

		// get the order object so we can populate it
		$order = $this->_order; // a TableOrders object (see constructor)

		// bind what you can from the post
		$order->bind( $values );

		// set the currency
		$order->currency_id = $values['order_currency_id'];

		// set the shipping method
		$order->shipping_method_id = $values['shipping_method_id'];

		$order->order_state_id    = $this->initial_order_state;
		$order->ip_address        = $_SERVER['REMOTE_ADDR'];

		// set the order's addresses based on the form inputs
		$this->setAddresses( $values );

		// get the items and add them to the order
		$items = $this->getProductsInfo( $order->currency_id, $order->getBillingGeozone() );
		foreach ($items as $item)
		{
			$order->addItem( $item );
		}

		// get the order totals
		$order->calculateTotals();
		$order->calculateVendorTotals();

		// get the ordernumber
		// $order->generateOrderNumber();

		// TODO Since is all one transaction, is there some way to preserve the ability to rollback if one part of it fails?
		// Can we perform a $row->check() on each individual part (orderinfo, orderitems, etc)
		// before saving *any* of them?

		$error = false;
		// then save the order to the orders table, one for each vendor
		// TODO add each part of this transaction to the orders->save() method?
		$model  = $this->getModel( $this->get('suffix') );

		// TODO Fix each $this->saveOrderSomething to use the orders object
		if ( $order->save() )
		{
			$model->setId( $order->order_id );

			// save the order items
			if (!$this->saveOrderItems())
			{
				// TODO What to do if saving order items fails?
				$error = true;
			}

			// save the order vendors
			if (!$this->saveOrderVendors())
			{
				// TODO What to do if saving order vendors fails?
				$error = true;
			}

			// save the order info
			if (!$this->saveOrderInfo())
			{
				// TODO What to do if saving order info fails?
				$error = true;
			}

			// save the order history
			if (!$this->saveOrderHistory())
			{
				// TODO What to do if saving order history fails?
				$error = true;
			}

			// if the billing address is new & user requests save, then save it
			if (empty($values['billing_address_id']) && $values['_checked']['billing_save_to_address_book'])
			{
				$billingAddress = $order->getBillingAddress();
				if (!$billingAddress->save())
				{
					// TODO What to do if saving address fails?
					$error = true;
					$this->setError( $billingAddress->getError() );
				}
			}

			// the shipping address is new && !same as the billing address,
			// and the user requests save, then save it
			if (empty($values['_checked']['sameasbilling']) && empty($values['shipping_address_id']) && !empty($values['_checked']['shipping_save_to_address_book']))
			{
				$shippingAddress = $order->getShippingAddress();
				if (!$shippingAddress->save())
				{
					// TODO What to do if saving address fails?
					$error = true;
					$this->setError( $shippingAddress->getError() );
				}
			}

			$this->messagetype  = 'message';
			$this->message      = JText::_('COM_CITRUSCART_SAVED');
			if ($error)
			{
				$this->messagetype  = 'notice';
				$this->message .= " :: ".$this->getError();
			}

			JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $order ) );

			$model->clearCache();
		}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$order->getError();
		}

		// clear the session of the stored items
		$this->setSessionVariable('order_products', array());
		$this->setSessionVariable('order_quantities', array());

		$redirect = "index.php?option=com_citruscart";
		$task = $app->input->getString('task');

		switch ($task)
		{
			case "savenew":
				$redirect .= '&view='.$this->get('suffix').'&task=add';
				break;
			case "apply":
				$redirect .= '&view='.$this->get('suffix').'&task=edit&id='.$model->getId();
				break;
			case "save":
			default:
				$redirect .= "&view=".$this->get('suffix');
				break;
		}

		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Saves the Order Info
	 *
	 * @return boolean
	 */
	function saveOrderInfo()
	{
		$order = $this->_order;

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$row = JTable::getInstance('OrderInfo', 'CitruscartTable');
		$row->order_id = $order->order_id;
		$row->bind( $this->_billingAddressArray );
		$row->bind( $this->_shippingAddressArray );

		if (!$row->save())
		{
			$this->setError( $row->getError() );
			return false;
		}
		return true;
	}

	/**
	 * Saves the order history and emails the customer if requested
	 *
	 * @return boolean
	 */
	function saveOrderHistory()
	{
		$app = JFactory::getApplication();
		$order = $this->_order;

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$row = JTable::getInstance('OrderHistory', 'CitruscartTable');
		$row->order_id = $order->order_id;
		$row->order_state_id = $order->order_state_id;

		$row->notify_customer = '1';

		$row->comments = $app->input->post->get('order_history_comments', '', 'post');
		//$row->comments = JRequest::getVar('order_history_comments', '', 'post');

		if (!$row->save())
		{
			$this->setError( $row->getError() );
			return false;
		}
		return true;
	}

	/**
	 * Inserts a record in orderitems for each item in the order
	 *
	 * @return boolean
	 */
	function saveOrderItems()
	{
		$order = $this->_order;
		$items = $order->getItems();

		if (empty($items) || !is_array($items))
		{
			$this->setError( "saveOrderItems:: ".JText::_('COM_CITRUSCART_ITEMS_ARRAY_INVALID') );
			return false;
		}

		$error = false;
		$errorMsg = "";
		foreach ($items as $item)
		{
			$item->order_id = $order->order_id;
			// since they are always the same, why store it twice?
			// $item->order_item_currency_id = $order->currency_id;
			if (!$item->save())
			{
				// track error
				$error = true;
				$errorMsg .= $item->getError();
			}
		}

		if ($error)
		{
			$this->setError( $errorMsg );
			return false;
		}
		return true;
	}

	/**
	 * Inserts a record in ordervendors for each vendor associated with the order
	 *
	 * @return boolean
	 */
	function saveOrderVendors()
	{
		$order = $this->_order;
		$items = $order->getVendors();

		if (empty($items) || !is_array($items))
		{
			// No vendors other than store owner, so just skip this
			//$this->setError( "saveOrderVendors:: ".JText::_('COM_CITRUSCART_VENDORS_ARRAY_INVALID') );
			//return false;
			return true;
		}

		$error = false;
		$errorMsg = "";
		foreach ($items as $item)
		{
			if (empty($item->vendor_id))
			{
				continue;
			}
			$item->order_id = $order->order_id;
			if (!$item->save())
			{
				// track error
				$error = true;
				$errorMsg .= $item->getError();
			}
		}

		if ($error)
		{
			$this->setError( $errorMsg );
			return false;
		}
		return true;
	}

	/**
	 * Updates an order's status, adding a record to its history
	 * and redirects back to the view order page
	 *
	 * @return void
	 */
	function updateStatus()
	{
		$app = JFactory::getApplication();
		$model  = $this->getModel( $this->get('suffix') );
		$row = $model->getTable();
		$row->load( $model->getId() );
		//$row->order_state_id = JRequest::getVar('new_orderstate_id');
		$row->order_state_id = $app->input->getInt('new_orderstate_id');


		$completed_tasks = $app->input->getString('completed_tasks');


		if ($completed_tasks == "on" && empty($row->completed_tasks) )
		{
			Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
			CitruscartHelperOrder::doCompletedOrderTasks( $row->order_id );
			$row->completed_tasks = 1;
		}

		if ( $row->save())
		{
			$model->setId( $row->order_id );
			$this->messagetype  = 'message';
			$this->message      = JText::_('COM_CITRUSCART_ORDER_SAVED');

			$history = JTable::getInstance('OrderHistory', 'CitruscartTable');
			$history->order_id             = $row->order_id;
			$history->order_state_id       = $row->order_state_id;
			$history->notify_customer      = $app->input->getString('new_orderstate_notify');
			$history->comments             = $app->input->getString('new_orderstate_comments');

			if (!$history->save())
			{
				$this->setError( $history->getError() );
				$this->messagetype  = 'notice';

				$this->message      .= " :: ".JText::_('COM_CITRUSCART_ORDERHISTORY_SAVE_FAILED');
			}

			$model->clearCache();


			JFactory::getApplication()->triggerEvent( 'onAfterUpdateStatus'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = "index.php?option=com_citruscart";
		$redirect .= '&view='.$this->get('suffix').'&task=view&id='.$model->getId();
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Displays list of orders to update their status
	 * provide diffrent functions like send mail to user update staus etc.
	 * @return void
	 */
	function delete()
	{
		$app = JFactory::getApplication();
		//$confirmdelete = JRequest::getInt('confirmdelete');
		$confirmdelete = $app->input->getInt('confirmdelete');
		if (!empty($confirmdelete))
		{
			parent::delete();
		}
		else
		{
			//$cids = JRequest::getVar('cid', array(0), 'request', 'array');

			$cids = $app->input->getArray('cid', array(0), 'request', 'array');

			// select only the ids from cid
			$model  = $this->getModel( $this->get('suffix') );

			$query = $model->getQuery();
			$query->where("tbl.order_id IN ('".implode( "', '", $cids )."') ");
			$model->setQuery( $query );

			// create view, assign model, and display
			$view = $this->getView( 'orders', 'html' );
			$view->set( '_controller', 'orders' );
			$view->set( '_view', 'orders' );
			$view->setModel( $model, true );
			$items = $model->getList();
			$view->assign('items', $items);
			$view->assign( 'pagination', $model->getPagination() );
			$view->assign( 'state', $model->getState() );
			$view->setLayout( 'confirmdelete' );
			$view->setTask(true); $view->display();
			$this->footer();
		}
	}

	/**
	 * Displays list of orders to update their status
	 * provide diffrent functions like send mail to user update staus etc.
	 * @return void
	 */
	function batchedit()
	{
		$app = JFactory::getApplication();
		//$cids = JRequest::getVar('cid', array(0), 'request', 'array');

		$cids =$app->input->get('cid', array(0), 'request', 'array');

		// select only the ids from cid
		$model 	= $this->getModel( $this->get('suffix') );

		$query = $model->getQuery();
		$query->where("tbl.order_id IN ('".implode( "', '", $cids )."') ");
		$model->setQuery( $query );

		// create view, assign model, and display
		$view = $this->getView( 'orders', 'html' );
		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->setLayout( 'batchedit' );
		$view->setTask(true);
		$view->display();
		$this->footer();
	}

	/**
	 * This method is updating the all orders on the basis of the action
	 * @return void
	 */

	function updatebatch()
	{
		$app = JFactory::getApplication();
		$model 	= $this->getModel( $this->get('suffix') );

		// Get the list of selected items and their selected params
		$cids 	= $app->input->getArray('cid', array (0), 'post', 'array');
		$sendMails = $app->input->getArray('new_orderstate_notify', array (0), 'post', 'array');
		$completeTasks = $app->input->getArray('completed_tasks', array (0), 'post', 'array');
		$states = $app->input->getArray('new_orderstate_id', array (0), 'post', 'array');
		$comments = $app->input->getArray('new_orderstate_comments', array (0), 'post', 'array');

		// for the updation
		$counter=0;

		foreach ($cids as $orderId)
		{
		 $row = $model->getTable();
		 $row->load($orderId);
		 $row->order_state_id = $states[$counter];
		 $completed_tasks = $completeTasks[$orderId];
		 $is_notify=0;

		 if ($completed_tasks == "on" && empty($row->completed_tasks) )
		 {
		 	Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
		 	CitruscartHelperOrder::doCompletedOrderTasks( $row->order_id );
		 	$row->completed_tasks = 1;
		 }

		 // saving the row
		 if ( $row->save())
		 {
		 	$model->setId( $row->order_id );
		 	$this->messagetype  = 'message';
		 	$this->message      = JText::_('COM_CITRUSCART_ORDER_SAVED');

		 	$history = JTable::getInstance('OrderHistory', 'CitruscartTable');
		 	$history->order_id             = $row->order_id;
		 	$history->order_state_id       = $row->order_state_id;
		 	if($sendMails[$orderId]=="on"){
		 		$is_notify=1;
		 	}
		 	$history->notify_customer      = $is_notify;
		 	$history->comments             = $comments[$counter];

		 	if (!$history->save())
		 	{
		 		$this->setError( $history->getError() );
		 		$this->messagetype  = 'notice';
		 		$this->message      .= " :: ".JText::_('COM_CITRUSCART_ORDERHISTORY_SAVE_FAILED');
		 	}


		 	JFactory::getApplication()->triggerEvent( 'onAfterUpdateStatus'.$this->get('suffix'), array( $row ) );
		 }

		 else
		 {
		 	$this->messagetype  = 'notice';
		 	$this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		 }
		 $counter++;

		}

		$model->clearCache();

		$redirect = "index.php?option=com_citruscart";
		$redirect .= '&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );

	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function resendEmail()
	{
		$app = JFactory::getApplication();
		Citruscart::load( 'CitruscartUrl', 'library.url' );

		$model = $this->getModel( $this->get('suffix') );
		$order = $model->getTable( 'orders' );
		$order->load( $model->getId() );
		$orderitems = $order->getItems();
		$row = $model->getItem();

		// send notice of new order
		Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance('Email');
		$helper->sendEmailNotices($row, 'new_order');

		// track message
		$redirect = "index.php?option=com_citruscart&view=orders";
		$redirect .= "&task=view&id=".$model->getId();
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function editAddresses()
	{
		$app = JFactory::getApplication();
		$app->input->set('layout', 'form_addresses' );
		parent::display();
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function closeEditAddresses()
	{
		$app = JFactory::getApplication();
		$order_id = $app->input->getInt('id',0);
		$redirect = "index.php?option=com_citruscart&view=orders";
		$redirect .= "&task=view&id=" . $order_id;
		$this->redirect = $redirect;
		parent::cancel();
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function saveAddresses()
	{	$app = JFactory::getApplication();
		$redirect = "index.php?option=com_citruscart&view=orders";

		$order_id = $app->input->getInt('id',0);

		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
		$orderinfo = JTable::getInstance('OrderInfo', 'CitruscartTable');
		$orderinfo->load( array('order_id'=>$order_id) );
		if (empty($order_id) || empty($orderinfo->order_id))
		{
			$this->message = JText::_('COM_CITRUSCART_INVALID_ORDER');
			$this->messagetype = 'notice';
			$redirect = JRoute::_( $redirect, false );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		//$post = JRequest::get('post');
		$app = JFactory::getApplication();
		$post = $app->input->get($_POST);
		
		$orderinfo->bind($post);

		// do the countries and zones names
		$country = JTable::getInstance('Countries', 'CitruscartTable');
		$country->load( $post['billing_country_id'] );
		$orderinfo->billing_country_name = $country->country_name;

		$zone = JTable::getInstance('Zones', 'CitruscartTable');
		$zone->load( $post['billing_zone_id'] );
		$orderinfo->billing_zone_name = $zone->zone_name;

		$country->load( $post['shipping_country_id'] );
		$orderinfo->shipping_country_name = $country->country_name;

		$zone->load( $post['shipping_zone_id'] );
		$orderinfo->shipping_zone_name = $zone->zone_name;

		if (!$orderinfo->save())
		{
			$this->message = JText::_('COM_CITRUSCART_SAVE_FAILED') . " - " . $orderinfo->getError();
			$this->messagetype = 'notice';
			$redirect = JRoute::_( $redirect, false );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		$redirect .= "&task=view&id=" . $order_id;
		$this->message = JText::_('COM_CITRUSCART_ADDRESS_CHANGES_SAVED');
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		return;
	}

	function updateStatusTextarea()
	{
		$app = JFactory::getApplication();

		$order_state_selected = $app->input->getInt('orderstate_selected');

		$model = JModelLegacy::getInstance( 'OrderStates', 'CitruscartModel' );
		$model->setId( $order_state_selected );
		$item = $model->getItem();

		if( !$item )
			$desc = '';
		else
			$desc = $item->order_state_description;
		$html='<textarea name="new_orderstate_comments" rows="5" style="width: 100%;">'.$desc.'</textarea>';
		$response = array();
		$response['msg'] = $html;
		$response['error'] = '';
		echo ( json_encode($response) );

		return;
	}

}
