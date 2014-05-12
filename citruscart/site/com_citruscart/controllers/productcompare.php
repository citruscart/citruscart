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

Citruscart::load( 'CitruscartHelperProductCompare', 'helpers.compare' );

class CitruscartControllerProductCompare extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		if(!Citruscart::getInstance()->get('enable_product_compare', '1'))
		{
			JFactory::getApplication()->redirect( JRoute::_( 'index.php?option=com_citruscart&view=products' ), JText::_('COM_CITRUSCART_PRODUCT_COMPARE_DISABLED') );
			return;
		}

		$this->set('suffix', 'productcompare');
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$model = $this->getModel( $this->get('suffix') );
		$user = JFactory::getUser();

		$state['filter_user'] = $user->id;
		if (empty($user->id))
		{
			$session = JFactory::getSession();
			$state['filter_session'] = $session->getId();
		}

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * (non-PHPdoc)
	 * see Citruscart/admin/CitruscartController::display()
	 */
	function display($cachable=false, $urlparams = false)
	{
		$input = JFactory::getApplication()->input;
		$input->set( 'view', $this->get('suffix') );
		$view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
		$model  = $this->getModel( $this->get('suffix') );
		$this->_setModelState();
		$items = $model->getList();

		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		$view->assign( 'items', $items );
		$view->set('hidemenu', true);
		$view->set('_doTask', true);
		$view->setModel( $model, true );
		$view->setLayout('default');
		$view->display();
		$this->footer();
		return;
	}

	/**
	 * Adds an item to a User's Product Compare
	 * whether in the session or the db
	 *
	 */
	function addProductToCompare()
	{
		$input= JFactory::getApplication()->input;
		// saving the session id which will use to update the cart
		$session = JFactory::getSession();
		$userid = JFactory::getUser()->id;

		// After login, session_id is changed by Joomla, so store this for reference
		$session->set( 'old_sessionid', $session->getId() );

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		$product_id = $input->getInt( 'product_id' );
		$add = $input->getInt( 'add', 1 );

		//deleting product to compare
		if(!$add)
		{
			$db = JFactory::getDBO();
			Citruscart::load( 'CitruscartQuery', 'library.query' );
			$query = new CitruscartQuery();
			$query->delete();
			$query->from( "#__citruscart_productcompare" );
			$query->where( "`product_id` = '$product_id' " );
			$query->where( "`session_id` = '".$session->getId()."' " );
			$query->where( "`user_id` = '$userid'" );
			$db->setQuery( (string) $query );
			if (!$db->query())
			{
				$response['msg'] = $helper->generateMessage($db->getErrorMsg());
				$response['error'] = '1';
				return false;
			}
		}
		else
		{
			Citruscart::load( 'CitruscartHelperProductCompare', 'helpers.productcompare' );
			$compare_helper = new CitruscartHelperProductCompare();

			//check limit
			$compareLimit = $compare_helper->checkLimit();
			if(!$compareLimit)
			{
				Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
				$helper = CitruscartHelperBase::getInstance();
				$limit = Citruscart::getInstance()->get('compared_products', '5');
				$response['msg'] = $helper->generateMessage( JText::sprintf( "COM_CITRUSCART_ONLY_N_PRODUCTS_CAN_BE_ADDED_TO_COMPARE", $limit ) );
				$response['error'] = '1';
				echo json_encode($response);
				return;
			}

			// create cart object out of item properties
			$item = new JObject;
			$item->user_id     = $userid;
			$item->product_id  = (int) $product_id;

			// add the item to the product comparison
			$compare_item = $compare_helper->addItem( $item );
		}


		//load user compared items
		$model  = $this->getModel( $this->get('suffix') );
		$model->setState('filter_user', $userid );
		if (empty($user->id))
		{
			$model->setState('filter_session', $session->getId() );
		}

		$items = $model->getList();

		//TODO: make it to call a view
		$response['msg'] .= '<ul>';
		foreach($items as $item)
		{
			$table = JTable::getInstance('Products', 'CitruscartTable');
			$table->load(array('product_id'=> $item->product_id));
			$response['msg'] .= '<li>';
			$response['msg'] .= '<a href="'.JRoute::_('index.php?option=com_citruscart&view=products&task=view&id='.$item->product_id).'">';
			$response['msg'] .= $table->product_name;
			$response['msg'] .= '</a>';
			$response['msg'] .= '</li>';
		}
		$response['msg'] .= '</ul>';

		echo json_encode($response);
		return;
	}

	function remove()
	{
		$id = $input->getInt('id',0);
		$model = $this->getModel($this->get('suffix'));
		$table= $model->getTable();
		$table->delete($id);
		$redirect = JRoute::_( "index.php?option=com_citruscart&view=productcompare#Citruscart-compare", false );
		$this->setRedirect( $redirect );
	}

	/**
	 * Displays the cart, expects to be called via ajax
	 *
	 * @return unknown_type
	 */
	function displayCart()
	{
		JLoader::import( 'com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components' );

		jimport( 'joomla.application.module.helper' );

		$modules    = JModuleHelper::_load();
		if (empty($modules))
		{
			echo ( json_encode( array('msg'=>'') ) );
			return;
		}

		foreach ($modules as $module)
		{
			if ($module->module == 'mod_citruscart_cart')
			{
				$mainframe = JFactory::getApplication();
				$mainframe->setUserState( 'mod_usercart.isAjax', '1' );

				echo ( json_encode( array('msg'=>JModuleHelper::renderModule($module)) ) );
				return;
			}
		}

		echo ( json_encode( array('msg'=>'') ) );
		return;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function update()
	{
		$input= JFactory::getApplication()->input;
		$model 	= $this->getModel( strtolower(CitruscartHelperCarts::getSuffix()) );
		$this->_setModelState();

		$user = JFactory::getUser();
		$session = JFactory::getSession();

		$cids = $input->get('cid', array(0), '', 'ARRAY');
		$product_attributes = $input->get('product_attributes', array(0), '', 'ARRAY');
		$quantities = $input->get('quantities', array(0), '', 'ARRAY');
		$post = $input->getArray($_POST);

		$msg = JText::_('COM_CITRUSCART_QUANTITIES_UPDATED');

		$remove = $input->getString('remove');
		if ($remove)
		{
			foreach ($cids as $cart_id=>$product_id)
			{
				//            	$keynames = explode('.', $key);
				//            	$attributekey = $keynames[0].'.'.$keynames[1];
				//            	$index = $keynames[2];
				$row = $model->getTable();

				//main cartitem keys
				$ids = array('user_id'=>$user->id, 'cart_id'=>$cart_id);

				// fire plugin event: onGetAdditionalCartKeyValues
				//this event allows plugins to extend the multiple-column primary key of the carts table
				$additionalKeyValues = CitruscartHelperCarts::getAdditionalKeyValues( null, $post, $index );
				if (!empty($additionalKeyValues))
				{
					$ids = array_merge($ids, $additionalKeyValues);
				}

				if (empty($user->id))
				{
					$ids['session_id'] = $session->getId();
				}

				if ($return = $row->delete(array('cart_id'=>$cart_id)))
				{
					$item = new JObject;
					$item->product_id = $product_id;
					$item->product_attributes = $product_attributes[$cart_id];
					$item->vendor_id = '0'; // vendors only in enterprise version

					// fire plugin event
					
					JFactory::getApplication()->triggerEvent( 'onRemoveFromCart', array( $item ) );
				}
			}
		}
		else
		{
			foreach ($quantities as $cart_id=>$value)
			{
				$carts = JTable::getInstance( 'Carts', 'CitruscartTable' );
				$carts->load( array( 'cart_id'=>$cart_id) );
				$product_id = $carts->product_id;
				$value = (int) $value;

				//            	$keynames = explode('.', $key);
				//            	$product_id = $keynames[0];
				//            	$attributekey = $product_id.'.'.$keynames[1];
				//            	$index = $keynames[2];

				$vals = array();
				$vals['user_id'] = $user->id;
				$vals['session_id'] = $session->getId();
				$vals['product_id'] = $product_id;

				// fire plugin event: onGetAdditionalCartKeyValues
				//this event allows plugins to extend the multiple-column primary key of the carts table
				//		        	$additionalKeyValues = CitruscartHelperCarts::getAdditionalKeyValues( null, $post, $index );
				//		        	if (!empty($additionalKeyValues))
				//		        	{
				//		        		$vals = array_merge($vals, $additionalKeyValues);
				//		        	}

				// using a helper file,To determine the product's information related to inventory
				$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $product_id, $product_attributes[$cart_id] );
				if ( $availableQuantity->product_check_inventory && $value > $availableQuantity->quantity )
				{
					JFactory::getApplication()->enqueueMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $value ));
					continue;
				}

				if ($value > 1)
				{
					$product = JTable::getInstance( 'Products', 'CitruscartTable' );
					$product->load( array( 'product_id'=>$product_id) );
					if( $product->quantity_restriction )
					{
						$min = $product->quantity_min;
						$max = $product->quantity_max;

						if( $max )
						{
							if ($value > $max )
							{
								$msg = JText::_('COM_CITRUSCART_REACHED_MAXIMUM_QUANTITY_FOR_THIS_OBJECT').$max;
								$value = $max;
							}
						}
						if( $min )
						{
							if ($value < $min )
							{
								$msg = JText::_('COM_CITRUSCART_REACHED_MAXIMUM_QUANTITY_FOR_THIS_OBJECT').$min;
								$value = $min;
							}
						}
					}
					if ($product->product_recurs)
					{
						$value = 1;
					}
				}

				$row = $model->getTable();
				$vals['product_attributes'] = $product_attributes[$cart_id];
				$vals['product_qty'] = $value;
				if (empty($vals['product_qty']) || $vals['product_qty'] < 1)
				{
					// remove it
					if ($return = $row->delete($cart_id))
					{
						$item = new JObject;
						$item->product_id = $product_id;
						$item->product_attributes = $product_attributes[$cart_id];
						$item->vendor_id = '0'; // vendors only in enterprise version

						// fire plugin event
						
						JFactory::getApplication()->triggerEvent( 'onRemoveFromCart', array( $item ) );
					}
				}
				else
				{
					$row->load($cart_id);
					$row->product_qty = $vals['product_qty'];
					$row->save();
				}
			}
		}

		$carthelper = new CitruscartHelperCarts();
		$carthelper->fixQuantities();
		if (empty($user->id))
		{
			$carthelper->checkIntegrity($session->getId(), 'session_id');
		}
		else
		{
			$carthelper->checkIntegrity($user->id);
		}

		Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
		$router = new CitruscartHelperRoute();

		$redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=".$router->findItemid( array('view'=>'carts') ), false );
		$this->setRedirect( $redirect, $msg);
	}

	/*
	 *
	 */
	function confirmAdd()
	{
		$model  = $this->getModel( $this->get('suffix') );
		$this->_setModelState();
		$items = $model->getList();
		$show_tax = Citruscart::getInstance()->get('display_prices_with_tax');

		$view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
		$view->assign('cartobj', $this->checkItems($items, $show_tax));
		$view->assign( 'show_tax', $show_tax );
		$view->set('hidemenu', true);
		$view->set('_doTask', true);
		$view->setModel( $model, true );
		$view->setLayout('confirmadd');
		$view->display();
		$this->footer();
		return;
	}

	/**
	 *
	 * Method to check config, user group and product state (if recurs).
	 * Then get right values accordingly
	 * @param array $items - cart items
	 * @param boolean - config to show tax or not
	 * @return object
	 */
	function checkItems( &$items, $show_tax=false )
	{
		if (empty($items)) { return array(); }

		Citruscart::load('CitruscartHelperUser', 'helpers.user');
		Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
		Citruscart::load('CitruscartHelperTax', 'helpers.tax');

		if ($show_tax)
		$taxes = CitruscartHelperTax::calculateTax( $items, 1 );

		$subtotal = 0;
		foreach ($items as $item)
		{
			if($show_tax)
			{
				$item->product_price = $item->product_price + $taxes->product_taxes[$item->product_id];
				$item->taxtotal = $taxes->product_taxes[$item->product_id];
			}

			$item->subtotal = $item->product_price * $item->product_qty;
			$subtotal = $subtotal + $item->subtotal;
		}
		$cartObj = new stdClass();
		$cartObj->items = $items;
		$cartObj->subtotal = $subtotal;
		return $cartObj;
	}

	/**
	 * Verifies the fields in a submitted form.
	 * Then adds the item to the users cart
	 *
	 * @return unknown_type
	 */
	function addToCart()
	{
		Citruscart::load( 'CitruscartControllerProducts', 'controllers.products', array( 'site' => 'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );
		$controller = new CitruscartControllerProducts();
		$controller->addToCart();
		$controller->redirect();
	}
}