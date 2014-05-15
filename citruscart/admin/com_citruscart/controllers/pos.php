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

class CitruscartControllerPOS extends CitruscartController
{
	/**
	 * Default redirect URL
	 */
	var $redirect = 'index.php?option=com_citruscart&view=orders';
	var $validation_url = 'index.php?option=com_citruscart&view=pos&task=validate&format=raw';

	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'pos');
		$this->registerTask( 'flag_billing', 'flag' );
		$this->registerTask( 'flag_shipping', 'flag' );
		$this->registerTask( 'flag_deleted', 'flag' );
		$this->registerTask( 'deleterequests', 'deleteRequests' );
	}

	function display($cachable=false, $urlparams = false)
	{
		$app = JFactory::getApplication();

		$post =$app->input->getArray($_POST);

		$step = $app->input->getString('nextstep', 'step1');
		if(empty($step))
		{
			$step = 'step1';
		}

		DSCModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_citruscart/models');
		$elementUserModel = DSCModel::getInstance('ElementUser', 'CitruscartModel');
		$session = JFactory::getSession();

		$view = $this->getView('pos', 'html');
		$view->assign('session', $session);
		$view->assign('step', $step);
		$view->assign('validation_url', $this->validation_url);
		$view->setModel($elementUserModel);
		$view->setTask(true);
		$method_name = 'do' . $step;
		if(method_exists($this, $method_name))
		{
			$this->$method_name($post);
		}
		parent::display($cachable, $urlparams);
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function doStep2($post = array())
	{

		$view = $this->getView('pos', 'html');
		$view->assign('step1_inactive', $this->step1Inactive());
		$view->assign('cart', $this->getCartView());
	}

	/**
	 * Method to set values in the session from the post
	 * @return unknown_type
	 */
	function saveStep1()
	{
		$app = JFactory::getApplication();

		$post = $app->input->get($_POST);
		// store the values in the session
		$session = JFactory::getSession();

		if($post['user_type'] != 'existing')
		{
			$email = '';
			// Create user
			Citruscart::load('CitruscartHelperUser', 'helpers.user');
			$userHelper = CitruscartHelperUser::getInstance('User', 'CitruscartHelper');
			if($post['user_type'] == 'new')
			{
				$text = JText::_('COM_CITRUSCART_USERNAME');
				$email = $post['new_email'];
				$username = !empty($post['new_username_create']) ?  $email : $post['new_username'];
				$details = array('email' => $post['new_email'],
				'name' => $post['new_name'],
				'username' => $username);
				// create password here?
				jimport('joomla.user.helper');
				$details['password'] = JUserHelper::genRandomPassword();
				$details['password2'] = $details['password'];

				$user = $userHelper->createNewUser($details);
			}
			elseif($post['user_type'] == 'anonymous')
			{
				$email = $post['anon_email'];
				// create a guest email address to be stored in the __users table
				//get the domain from the uri
				$uri = JURI::getInstance();
				$domain = $uri->gethost();

				$guestId = time();
				// send the guest user credentials to the user's real email address
				$details = array('email' => $post['anon_email'],
				'name' => "guest_" . $guestId,
				'username' => "guest_" . $guestId);

				// use a random password, and send password2 for the email
				jimport('joomla.user.helper');
				$details['password'] = JUserHelper::genRandomPassword();
				$details['password2'] = $details['password'];

				$user = $userHelper->createNewUser($details, true);

				// but don't save the user's real email in the __users db table
				if(Citruscart::getInstance()->get('obfuscate_guest_email', '0'))
				{
					$lastUserId = $userHelper->getLastUserId();
					$guestId = $lastUserId + 1;
					// format: guest_[id]@domain.com
					$guest_email = "guest_" . $guestId . "@" . $domain;
					$userEmailUpdate = $userHelper->updateUserEmail($user->id, $guest_email);
				}
			}

			// save the real user's info in the userinfo table
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
			$userinfo = JTable::getInstance('UserInfo', 'CitruscartTable');
			$userinfo->load( array('user_id' => $user->id));
			$userinfo->user_id = $user->id;
			$userinfo->email = $email;
			$userinfo->save();

			// overide the userid in the post
			$user_id = $user->id;
			$session->set('user_id', $user->id, 'citruscart_pos');
		}
		else
		{
			$session->set('user_id', $post['user_id'], 'citruscart_pos');
		}

		$session->set('user_type', $post['user_type'], 'citruscart_pos');
		$session->set('new_email', $post['new_email'], 'citruscart_pos');
		$session->set('new_name', $post['new_name'], 'citruscart_pos');
		$session->set('new_username_create', !empty($post['new_username_create']), 'citruscart_pos');
		$session->set('new_username', $post['new_username'], 'citruscart_pos');
		$session->set('anon_emails', !empty($post['anon_emails']), 'citruscart_pos');
		$session->set('anon_email', $post['anon_email'], 'citruscart_pos');
		$session->set('subtask', 'shipping', 'citruscart_pos');

		$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step2");
	}

	function doStep3($post = array())
	{
		// track if we are in shipping or payment
		$session = JFactory::getSession();
		$subtask = $session->get('subtask', 'shipping', 'citruscart_pos');

		$order = $this->populateOrder();
		$view = $this->getView('pos', 'html');
		$view->assign('step1_inactive', $this->step1Inactive());

		// determines if have item/s that need shipping
		Citruscart::load("CitruscartHelperBase", 'helpers._base');
		$product_helper = CitruscartHelperBase::getInstance('Product');

		$items = $order->getItems();

		$showShipping = false;
		foreach($items as $item)
		{
			$shipping = $product_helper->isShippingEnabled($item->product_id);
			if($shipping)
			{
				$showShipping = true;
				break;
			}
		}
		$view->assign('showShipping', $showShipping);

		if($showShipping)
		{
			$shippingAddress = $order->getShippingAddress();
			if(!empty($shippingAddress))
				$view->assign('shippingAddress', $shippingAddress);
			else
				$view->assign('shippingForm', $this->getAddressForm('shipping_input_', $showShipping, true));

		}
		else
		{
			// go directly to payment if we dont no shipping required
			$subtask = 'payment';
			$session->set('subtask', $subtask, 'citruscart_pos');
		}

		$billingAddress = $order->getBillingAddress();
		if(!empty($billingAddress))
			$view->assign('billingAddress', $billingAddress);
		else
			$view->assign('billingForm', $this->getAddressForm('billing_input_'));

		switch($subtask)
		{
			case 'shipping' :
				$view->assign('shippingRates', $this->getShippingHtml($order));
				break;
			case 'payment' :
				$order->shipping = new JObject();
				$order->shipping->shipping_price = $session->get('shipping_price', '', 'citruscart_pos');
				$order->shipping->shipping_extra = $session->get('shipping_extra', '', 'citruscart_pos');
				$order->shipping->shipping_name = $session->get('shipping_name', '', 'citruscart_pos');
				$order->shipping->shipping_tax = $session->get('shipping_tax', '', 'citruscart_pos');

				//calculate the order totals as we already have the shipping
				$order->calculateTotals();
				$view->assign('paymentOptions', $this->getPaymentOptionsHtml($order));

				// are there any enabled coupons?
				$coupons_present = false;
				$modelCoupon = DSCModel::getInstance('Coupons', 'CitruscartModel');
				$modelCoupon->setState('filter_enabled', '1');
				if($coupons = $modelCoupon->getList())
				{
					$coupons_present = true;
				}
				$view->assign('coupons_present', $coupons_present);

				$userid = $session->get('user_id', '', 'citruscart_pos');
				// assign userinfo for credits
				$userinfo = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
				$userinfo->load( array( 'user_id'=>$userid ) );
				$userinfo->credits_total = (float) $userinfo->credits_total;
				$view->assign('userinfo', $userinfo);

			default :
				break;
		}
		$view->setTask(true);
		$view->assign('orderSummary', $this->getOrderSummary($order));
		$view->assign('order', $order);
		$view->assign('user_type', $session->get( 'user_type', 'anonymous', 'citruscart_pos' ));
		$view->assign('subtask', $subtask);

	}

	function doStep4($post = array())
	{
		$values = $post;
		$session = JFactory::getSession();
		$userid = $session->get('user_id', '', 'citruscart_pos');

		// Save the order with a pending status
		if(!$order = $this->saveOrder($values))
		{
			// Output error message and halt
			JError::raiseNotice('Error Saving Order', $this->getError());
			return false;
		}

		// Update the addresses' user id!
		$shippingAddress = $order->getShippingAddress();
		$billingAddress = $order->getBillingAddress();

		$shippingAddress->user_id = $userid;
		$billingAddress->user_id = $userid;

		// Checking whether shipping is required
		$showShipping = false;
		if ($values['shippingrequired'])
			$showShipping = true;

		if ($showShipping && !$shippingAddress->save())
		{
			// Output error message and halt
			JError::raiseNotice( 'Error Updating the Shipping Address', $shippingAddress->getError() );
			return false;
		}

		if (!$billingAddress->save())
		{
			// Output error message and halt
			JError::raiseNotice( 'Error Updating the Billing Address', $billingAddress->getError() );
			return false;
		}

		$orderpayment_type = $values['payment_plugin'];
		$transaction_status = JText::_('COM_CITRUSCART_INCOMPLETE');
		// in the case of orders with a value of 0.00, use custom values
		if ( (float) $order->order_total == (float)'0.00' )
		{
			$orderpayment_type = 'free';
			$transaction_status = JText::_('COM_CITRUSCART_COMPLETE');
		}

		// Save an orderpayment with an Incomplete status
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$orderpayment = JTable::getInstance('OrderPayments', 'CitruscartTable');
		$orderpayment->order_id = $order->order_id;
		$orderpayment->orderpayment_type = $orderpayment_type; // this is the payment plugin selected
		$orderpayment->transaction_status = $transaction_status; // payment plugin updates this field onPostPayment
		$orderpayment->orderpayment_amount = $order->order_total; // this is the expected payment amount.  payment plugin should verify actual payment amount against expected payment amount
		if (!$orderpayment->save())
		{
			// Output error message and halt
			JError::raiseNotice( 'Error Saving Pending Payment Record', $orderpayment->getError() );
			return false;
		}

		// remove unnecessary _db proprety which causes 'Request-URI Too Large' error
		//unset($order->orderinfo->_db);

		// send the order_id and orderpayment_id to the payment plugin so it knows which DB record to update upon successful payment
		$values["order_id"]             = $order->order_id;
		$values["orderinfo"]            = $order->orderinfo;
		$values["orderpayment_id"]      = $orderpayment->orderpayment_id;
		$values["orderpayment_amount"]  = $orderpayment->orderpayment_amount;

		// IMPORTANT: Store the order_id in the user's session for the postPayment "View Invoice" link
		$mainframe = JFactory::getApplication();
		$mainframe->setUserState( 'Citruscart.order_id', $order->order_id );
		$mainframe->setUserState( 'Citruscart.orderpayment_id', $orderpayment->orderpayment_id );

		// in the case of orders with a value of 0.00, we redirect to the confirmPayment page
		if ( (float) $order->order_total == (float)'0.00' )
		{
			JFactory::getApplication()->redirect( 'index.php?option=com_citruscart&view=pos&task=confirmPayment' );
			return;
		}


		$results = JFactory::getApplication()->triggerEvent( "onPrePayment", array( $values['payment_plugin'], $values ) );

		//set payment to session
		$session = JFactory::getSession();
		$session->set('payment_plugin', $values['payment_plugin'], 'citruscart_pos');

		// Display whatever comes back from Payment Plugin for the onPrePayment
		$html = "";
		for ($i=0; $i<count($results); $i++)
		{
			$html .= $results[$i];
		}

		// Get Addresses
		$billing_address = $order->getBillingAddress();
		$shipping_address = $showShipping ? $order->getShippingAddress() : null;

		$shippingMethodName = $values['shipping_name'];

		$view = $this->getView( 'pos', 'html' );
		$view->assign('order', $order);
		$view->assign('plugin_html', $html);
		$view->assign('shipping_info', $shipping_address);
		$view->assign('billing_info', $billing_address);
		$view->assign('shipping_method_name',$shippingMethodName);
		$view->assign('showShipping', $showShipping );
		$view->assign('step1_inactive', $this->step1Inactive());

		// create POS request record
		$pos_tbl = JTable::getInstance( "PosRequests", "CitruscartTable" );
		$pos_tbl->load( array( 'user_id' => $userid, 'order_id' => $order->order_id, 'mode' => 1 ) );
		$pos_tbl->order_id = $order->order_id;
		$pos_tbl->user_id = $userid;
		$pos_tbl->mode = 1; // mode 1 => back-end
		$pos_tbl->data = base64_encode(@json_encode($values));
		$pos_tbl->save(); // generate pos_id
		$pos_tbl->save(); // save the final token (with pos_id -> that's why we need to save it twice)
		$values['pos_id'] = $pos_tbl->pos_id;
		$values['pos_token'] = $pos_tbl->token;

		$view->assign('values', $values);
		//calculate the order totals as we already have the shipping
		$order->calculateTotals();
		$view->assign('orderSummary', $this->getOrderSummary($order));
		$showBilling = true;
        if (empty($billingAddress->address_id))
        {
            $showBilling = false;
        }
        $view->assign( 'showBilling', $showBilling );
	}

	function doStep5($post)
	{
		$app = JFactory::getApplication();
       //do some security check?
        //$pos_id = JRequest::getInt( 'pos_id', 0);

		$pos_id = $app->input->getInt( 'pos_id', 0);

		$token = $app->input->get('pos_token', '' );

        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$tbl_pos = JTable::getInstance( 'PosRequests', 'CitruscartTable' );
		if( !$tbl_pos->load( array( 'pos_id' => $pos_id, 'user_id' => 0, 'order_id' => 0, 'mode' => 2, 'token' => $token ) ) )
		{
			// no POS data with this description were found
			JError::raiseNotice('COM_CITRUSCART_RESTRICTED_ACCESS', $this->getError());
			return false;
		}

		$data = json_decode(base64_decode($tbl_pos->data));
		if(is_object($data)) $data = get_object_vars($data);
		$values = $app->input->get('get');
		$values = array_merge($data, $values);

		$order_id = $values['order_id'];
		$session = JFactory::getSession();
		$orderpayment_type = $app->input->getString('orderpayment_type', $session->get('payment_plugin', '', 'citruscart_pos'));

		//$doneReloading = JRequest::getInt('reloaded');
		$doneReloading = $app->input->getInt('reloaded');

		// TODO find other way to do this check - there can be other offline payment types (new ones or created by users)
		if( ($orderpayment_type == 'payment_offline' || $orderpayment_type == 'payment_ccoffline') && !$doneReloading)
		{
			$uri	 = JURI::getInstance();
			$query = $uri->getQuery();
			//reload the page since in payment_offline we are still in the modal
			$doc = JFactory::getDocument();
			$link = JURI::root().'administrator/index.php?'.$query.'&reloaded=1';
			$js = "window.parent.location.href = '{$link}';";
			$js .= "window.parent.document.getElementById('sbox-window').close(); ";
			$doc->addScriptDeclaration($js);
		}


		$html = "";
		$order = JTable::getInstance('Orders', 'CitruscartTable');
		$order->load( array('order_id'=>$order_id) );

		if ( (!empty($order_id)) && (float) $order->order_total == (float)'0.00' )
		{
			$order->order_state_id = '17'; // PAYMENT RECEIVED
			$order->save();

			// send notice of new order
			Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
			$helper = CitruscartHelperBase::getInstance('Email');
			$order_model = Citruscart::getClass("CitruscartModelOrders", "models.orders");
			$order_model->setId( $order_id );
			$order_model_item = $order_model->getItem();
			$helper->sendEmailNotices($order_model_item, 'new_order');

			Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
			CitruscartHelperOrder::setOrderPaymentReceived( $order_id );
		}
		else
		{
			// get the payment results from the payment plugin
			$results = JFactory::getApplication()->triggerEvent( "onPostPayment", array( $orderpayment_type, $values ) );

			// Display whatever comes back from Payment Plugin for the onPrePayment
			for ($i=0; $i<count($results); $i++)
			{
				$html .= $results[$i];
			}

			// re-load the order in case the payment plugin updated it
			$order->load( array('order_id'=>$order_id) );
		}
		$articles = array();
		$view = $this->getView( 'pos', 'html' );
		$view->assign('plugin_html', $html);

		if(!empty($order_id))
		{
			$order_link = 'index.php?option=com_citruscart&controller=orders&view=orders&task=edit&id='.$order_id;
			$view->assign('order_link', $order_link );

			// get the articles to display after checkout
		  $article_id = Citruscart::getInstance()->get( 'article_checkout' );
			$articles = array();
	    if (!empty($article_id))
	    {
        Citruscart::load( 'CitruscartArticle', 'library.article' );
	    	$articles[] = CitruscartArticle::display( $article_id );
	    }

			switch ($order->order_state_id)
			{
			    case "2":
			    case "3":
			    case "5":
			    case "17":
			        $articles = array_merge( $articles, $this->getOrderArticles( $order_id ) );
			        break;
			}
		}
		$view->setTask(true);
		$view->assign( 'articles', $articles );
		$view->assign('orderSummary', $this->getOrderSummary($order));
		$view->assign('step1_inactive', $this->step1Inactive());
	}

	function saveStep2()
	{
		$session = JFactory::getSession();
		$session->set('subtask', 'shipping', 'citruscart_pos');
		// merely a redirect
		$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step3");
	}

	//TODO: making it task display instead to have access to the post
	function saveStep3()
	{
		$app = JFactory::getApplication();

		$values = $app->input->get($_POST);

		$session = JFactory::getSession();
		if(empty($values['payment_plugin']))
		{
			$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step3", JText::_('COM_CITRUSCART_PAYMENT_METHOD_REQUIRED'), 'notice');
		}

		$session->set('payment_plugin', $values['payment_plugin'], 'citruscart_pos');

		// Save the order with a pending status
		if(!$order = $this->saveOrder($values))
		{
			// Output error message and halt
			JError::raiseNotice('Error Saving Order', $this->getError());
			return false;
		}

		// merely a redirect
		$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step4");

	}

	function step1Inactive()
	{
		$session = JFactory::getSession();
		switch ($session->get( 'user_type', '', 'citruscart_pos' ))
		{
			case "existing" :
				$user = JFactory::getUser($session->get('user_id', '', 'citruscart_pos'));
				$step1_inactive = JText::_('COM_CITRUSCART_EXISTING_USER') . ": " . $user->name . " - " . $user->email . " [" . $user->id . "]";
				break;
			case "new" :
				$step1_inactive = JText::_('COM_CITRUSCART_NEW_USER') . ": " . $session->get('new_name', '', 'citruscart_pos') . " - " . $session->get('new_email', '', 'citruscart_pos');
				break;
			case "anonymous" :
				$step1_inactive = JText::_('COM_CITRUSCART_ANONYMOUS_USER');
				break;
			default :
				$step1_inactive = JText::_('COM_CITRUSCART_NAME_AND_EMAIL_OF_USER');
				break;
		}

		return $step1_inactive;
	}

	function saveShipping()
	{
		$app = JFactory::getApplication();
		$post  = $app->input->get($_POST);
		//$post = JRequest::get('post');
		$session = JFactory::getSession();
		$session->set('shipping_plugin', $post['shipping_plugin'], 'citruscart_pos');
		$session->set('shipping_price', $post['shipping_price'], 'citruscart_pos');
		$session->set('shipping_tax', $post['shipping_tax'], 'citruscart_pos');
		$session->set('shipping_name', $post['shipping_name'], 'citruscart_pos');
		$session->set('shipping_code', $post['shipping_code'], 'citruscart_pos');
		$session->set('shipping_extra', $post['shipping_extra'], 'citruscart_pos');
		$session->set('customer_note', $post['customer_note'], 'citruscart_pos');
		$session->set('subtask', 'payment', 'citruscart_pos');
		$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step3&subtask=payment");
	}

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/site/CitruscartController::validate()
	 */
	function validate()
	{
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = new CitruscartHelperBase();

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		// get elements from post
		$elements = json_decode(preg_replace('/[\n\r]+/', '\n', JRequest::getVar('elements', '', 'post', 'string')));

		// validate it using table's ->check() method
		if(empty($elements))
		{
			// if it fails check, return message
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage(JText::_('COM_CITRUSCART_COULD_NOT_PROCESS_FORM'));
			echo( json_encode($response));
			return ;
		}

		// convert elements to array that can be binded
		$values = $helper->elementsToArray($elements);
       	$values['sameasbilling'] = isset($values['_checked']['sameasbilling']) && !empty( $values['_checked']['sameasbilling']);

		// validate it based on the step
		switch ( $values['step'] )
		{
			case "step1" :
				$response = $this->validateStep1($values);
				break;
			case "step2" :
				$response = $this->validateStep2($values);
				break;
			case "step3" :
				$response = $this->validateStep3($values);
				break;
			case "step4" :
				break;
		}

		echo( json_encode($response));
		return ;
	}

	/**
	 *
	 * Enter description here ...
	 * @param $values
	 * @return unknown_type
	 */
	function validateStep1($values)
	{
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = new CitruscartHelperBase();

		$msg = array();
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		switch ( $values['_checked']['user_type'] )
		{
			case "existing" :
				if(empty($values['user_id']))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_PLEASE_SELECT_USER');

				}
				break;
			case "new" :
				if(empty($values['new_email']) || $values['new_email'] == JText::_('COM_CITRUSCART_EMAIL'))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_PLEASE_PROVIDE_EMAIL');
				}

				if(empty($values['new_name']) || $values['new_name'] == JText::_('COM_CITRUSCART_FULLNAME'))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_PLEASE_PROVIDE_NAME');
				}

				if(empty($values['_checked']['new_username_create']) && (empty($values['new_username']) || $values['new_username'] == JText::_('COM_CITRUSCART_USERNAME')))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_PLEASE_PROVIDE_USERNAME');
				}

				$userhelper = $helper->getInstance('User');

				// Is this email already used?
				if($userhelper->emailExists($values['new_email']))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_EMAIL_ALREADY_EXISTS');
				}

				  // Send the reminder
		        jimport('joomla.mail.helper');

		        // Validate the e-mail address
		        if (!JMailHelper::isEmailAddress($values['new_email']))
		        {
		          	 $response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_INVALID_EMAIL_ADDRESS');
		        }

				// Is this username already used?
				if(empty($values['_checked']['new_username_create']) && $userhelper->usernameExists($values['new_username']))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_USERNAME_ALREADY_EXISTS');
				}
				break;
			case "anonymous" :
				if(!empty($values['_checked']['anon_emails']) && (empty($values['anon_email']) || $values['anon_email'] == JText::_('COM_CITRUSCART_EMAIL')))
				{
					$response['error'] = '1';
					$msg[] = JText::_('COM_CITRUSCART_PLEASE_PROVIDE_EMAIL');
				}
				else
				{
					 // Send the reminder
			        jimport('joomla.mail.helper');
			        // Validate the e-mail address
			        if (!JMailHelper::isEmailAddress($values['anon_email']))
			        {
			          	$response['error'] = '1';
						$msg[] = JText::_('COM_CITRUSCART_INVALID_EMAIL_ADDRESS');
			        }
				}

				break;
		}

		$response['msg'] = $helper->generateMessage("<li>" . implode("</li><li>", $msg) . "</li>", false);
		return $response;
	}

	/**
	 * Method to validate the cart before proceeding to the next step
	 * @param $values - array
	 * @return array - json response
	 */
	function validateStep2($values)
	{
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = new CitruscartHelperBase();

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		//check if we have empty cart
		if(!isset($values['cid']))
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage(JText::_('COM_CITRUSCART_NO_ITEMS_IN_CART'), false);
		}

		return $response;
	}

	function validateStep3($values)
	{
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = new CitruscartHelperBase();

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';
		$msg = array();

		$config = Citruscart::getInstance();
		//check if we have billing address id
		if(empty($values['billing_input_address_id']))
		{
			$responseBilling = $this->validateAddress($values);
			if(count($responseBilling))
			{
				$msg = array_merge($msg, $responseBilling);
				$response['error'] = '1';
			}
		}

		$session = JFactory::getSession();
		$subtask = $session->get('subtask', 'shipping', 'citruscart_pos');

		switch($subtask)
		{
			case 'shipping' :
				//check if we have billing address id
				if(empty($values['shipping_input_address_id']))
				{
					if(empty($values['sameasbilling']))
					{
						$responseShipping = $this->validateAddress($values, 'shipping');
						if(count($responseShipping))
						{
							$msg = array_merge($msg, $responseShipping);
							$response['error'] = '1';
						}
					}
				}
				else
				{
					if(empty($values['shipping_name']))
					{
						$response['error'] = '1';
						$msg[] = JText::_('COM_CITRUSCART_PLEASE_SELECT_SHIPPING_METHOD');
					}
				}
				break;
			case 'payment' :
			default :
				if(!empty($values['billing_input_address_id']))
				{
					if(empty($values['payment_plugin']))
					{
						$response['error'] = '1';
						$msg[] = JText::_('COM_CITRUSCART_PLEASE_SELECT_PAYMENT_METHOD');
					}
				}

				break;
		}


		$response['msg'] = $helper->generateMessage("<li>" . implode("</li><li>", $msg) . "</li>", false);
		return $response;
	}


	function validateAddress($values, $type="billing")
	{
		Citruscart::load('CitruscartHelperShipping', 'helpers.shipping');
		//special case
		$sameasbilling = !empty($values['sameasbilling']) ? true : false;
		$msg = array();

		switch($type)
		{
			case 'shipping':
				$text = 'Shipping';
				if($sameasbilling)
				{
					$prefix = 'billing_input_';
					$validate_id = '1';
					$text = 'Billing';
				}
				else
				{
					$prefix = 'shipping_input_';
					$validate_id = '2';
				}
				break;
			case 'billing':
			default:
				$prefix = 'billing_input_';
				$validate_id = '1';
				$text = 'Billing';
				break;
		}

		// check if we already have an address id
		// if found we return an empty msg
		$addressInput = $prefix . 'address_id';
		if(!empty($values[$addressInput]))
		{
			return $msg;
		}

		$config = Citruscart::getInstance();
		$field_title = $config->get('validate_field_title');
		$field_name = $config->get('validate_field_name');
		$field_middle = $config->get('validate_field_middle');
		$field_last = $config->get('validate_field_last');
		$field_company = $config->get('validate_field_company');
		$field_address1 = $config->get('validate_field_address1');
		$field_address2 = $config->get('validate_field_address2');
		$field_country = $config->get('validate_field_country');
		$field_zone = $config->get('validate_field_zone');
		$field_city = $config->get('validate_field_city');
		$field_zip = $config->get('validate_field_zip');
		$field_phone = $config->get('validate_field_phone');

		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_title, $sameasbilling) && empty($values["{$prefix}title"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_TITLE_FIELD_REQUIRED",$text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_name, $sameasbilling) && empty($values["{$prefix}first_name"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_FIRST_NAME_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_middle, $sameasbilling) && empty($values["{$prefix}middle_name"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_MIDDLE_NAME_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_last, $sameasbilling) && empty($values["{$prefix}last_name"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_LAST_NAME_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_company, $sameasbilling) && empty($values["{$prefix}company"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_COMPANY_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_address1, $sameasbilling) && empty($values["{$prefix}address_1"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_ADDRESS_LINE_1_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_address2, $sameasbilling) && empty($values["{$prefix}address_2"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_ADDRESS_LINE_2_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_city, $sameasbilling) && empty($values["{$prefix}city"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_CITY_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_country, $sameasbilling) && empty($values["{$prefix}country_id"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_COUNTRY_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_zone, $sameasbilling) && empty($values["{$prefix}zone_id"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_ZONE_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_zip, $sameasbilling) && empty($values["{$prefix}postal_code"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_POSTAL_CODE_FIELD_REQUIRED", $text);
		}
		if( CitruscartHelperShipping::shouldBeValidated($validate_id, $field_phone, $sameasbilling) && empty($values["{$prefix}phone_1"]) )
		{
			$msg[] = JText::sprintf("COM_CITRUSCART_ENTRY_PHONE_FIELD_REQUIRED", $text);
		}

		return $msg;
	}

	/**
	 * Validate Coupon Code
	 *
	 * @return unknown_type
	 */
	function validateCouponCode()
	{
		$app = JFactory::getApplication();
		JLoader::import('com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components');
		//$elements = json_decode(preg_replace('/[\n\r]+/', '\n', JRequest::getVar('elements', '', 'post', 'string')));

		$elements = json_decode(preg_replace('/[\n\r]+/', '\n', $app->input->post->get('elements', '', 'post', 'string')));

		// convert elements to array that can be binded
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = CitruscartHelperBase::getInstance();
		$values = $helper->elementsToArray($elements);
       	$values['sameasbilling'] = isset($values['_checked']['sameasbilling']) && !empty( $values['_checked']['sameasbilling']);

		$coupon_code = JRequest::getVar('coupon_code', '');

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		// check if coupon code is valid
		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');

		Citruscart::load('CitruscartHelperCoupon', 'helpers.coupon');
		$helper_coupon = new CitruscartHelperCoupon();
		$coupon = $helper_coupon->isValid($coupon_code, 'code', $user_id);
		if(!$coupon)
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage($helper_coupon->getError());
			echo json_encode($response);
			return ;
		}

		if(!empty($values['coupons']) && in_array($coupon->coupon_id, $values['coupons']))
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage(JText::_('COM_CITRUSCART_COUPON_NOTICE'));
			echo json_encode($response);
			return ;
		}

		// TODO Check that the user can add this coupon to the order
		$can_add = true;
		if(!$can_add)
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage(JText::_('COM_CITRUSCART_CANNOT_ADD_COUPON_NOTICE'));
			echo json_encode($response);
			return ;
		}

		// if valid, return the html for the coupon
		$response['msg'] = " <input type='hidden' name='coupons[]' value='$coupon->coupon_id'>";

		echo json_encode($response);
		return ;
	}

	/**
     * Validates the credit amount and applies it to the order
     * @return unknown_type
     */
    function validateApplyCredit()
    {
    	$app = JFactory::getApplication();
        JLoader::import( 'com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components' );
        //$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
        $elements = json_decode( preg_replace('/[\n\r]+/', '\n', $app->input->post->get( 'elements', '', 'post', 'string' ) ) );

        // convert elements to array that can be binded
        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper = CitruscartHelperBase::getInstance();
        $values = $helper->elementsToArray( $elements );
       	$values['sameasbilling'] = isset($values['_checked']['sameasbilling']) && !empty( $values['_checked']['sameasbilling']);

        $session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$apply_credit_amount = (float) $app->input->getString( 'apply_credit_amount', '');
        //$apply_credit_amount = (float) JRequest::getVar( 'apply_credit_amount', '');

        $response = array();
        $response['msg'] = '';
        $response['error'] = '';

        // is the credit amount valid (i.e. greater than 0,
        if ($apply_credit_amount < (float) '0.00')
        {
            $response['error'] = '1';
            $response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_PLEASE_SPECIFY_VALID_CREDIT_AMOUNT') );
            echo json_encode($response);
            return;
        }

        // less than/== their available amount & order amount?
        $userinfo = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
        $userinfo->load( array( 'user_id'=>$user_id ) );
        $userinfo->credits_total = (float) $userinfo->credits_total;
        if ($apply_credit_amount > $userinfo->credits_total)
        {
            $apply_credit_amount = $userinfo->credits_total;
        }

        // get the order object so we can populate it
        $order = $this->populateOrder();

        // bind what you can from the post
        $order->bind( $values );

        // unset the order credit because it may have been set by the bind
        $order->order_credit = '0';

        // set the currency
        $order->currency_id = Citruscart::getInstance()->get( 'default_currencyid', '1' ); // USD is default if no currency selected

        // set the shipping method
        $order->shipping = new JObject();
        $order->shipping->shipping_price      = $values['shipping_price'];
        $order->shipping->shipping_extra      = $values['shipping_extra'];
        $order->shipping->shipping_name       = $values['shipping_name'];
        $order->shipping->shipping_tax        = $values['shipping_tax'];

        // set the addresses

        $this->setAddresses( $order, $values );

        // get the items and add them to the order
        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        $items = CitruscartHelperCarts::getProductsInfo();
        foreach ($items as $item)
        {
            $order->addItem( $item );
        }

        // get all coupons and add them to the order
        if (!empty($values['coupons']))
        {
            foreach ($values['coupons'] as $coupon_id)
            {
                $coupon = JTable::getInstance('Coupons', 'CitruscartTable');
                $coupon->load(array('coupon_id'=>$coupon_id));
                $order->addCoupon( $coupon );
            }
        }

        $this->addAutomaticCoupons($order);

        // get the order totals
        $order->calculateTotals();

        if ($apply_credit_amount > $order->order_total)
        {
            $apply_credit_amount = $order->order_total;
        }

        // if valid, return the html for the credit
        $response['msg'] = "<input type='hidden' name='order_credit' value='$apply_credit_amount'>";
        echo json_encode($response);
        return;
    }

	private function addAutomaticCoupons(&$order)
	{
		$date = JFactory::getDate();
		$date = $date->toSql();

		// Per Order Automatic Coupons
		$model = DSCModel::getInstance('Coupons', 'CitruscartModel');
		$model->setState('filter_automatic', '1');
		$model->setState('filter_date_from', $date);
		$model->setState('filter_date_to', $date);
		$model->setState('filter_datetype', 'validity');
		$model->setState('filter_type', '0');
		$model->setState('filter_enabled', '1');

		$coupons = $model->getList();

		// Per Product Automatic Coupons
		$model->setState('filter_type', '1');
		$coupons_2 = $model->getList(true);

		$coupons = array_merge( $coupons, $coupons_2 );

		if($coupons)
		{
			foreach($coupons as $coupon)
			{
				$order->addCoupon($coupon);
			}
		}

	}

	/**
	 * Method to show the listing of products
	 */
	function addProducts()
	{
		$this->set('suffix', 'products');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel($this->get('suffix'));
		$ns = $this->getNamespace();

		foreach($state as $key => $value)
		{
			$model->setState($key, $value);
		}
		$model->setState( 'filter_enabled', 1 );

		$view = $this->getView('pos', 'html');
		$view->setModel($model, true);
		$view->assign('state', $model->getState());
		$view->assign('items', $model->getList());
		$view->setTask(true);
		$view->setLayout('addproduct');
		$view->display();
	}

	/**
	 * Method to show the view a product to be added to cart
	 * @return unknown_type
	 */
	function viewProduct()
	{
		$app = JFactory::getApplication();
		$app->input->set('page','pos');
		//JRequest::setVar( 'page', 'pos' );
		$model = $this->getModel('Products');
		$model->setId($model->getId());
		$row = $model->getItem();
		$session = JFactory::getSession();
		$view = $this->getView('pos', 'html');
		$view->setModel($model, true);
		$view->assign('product', $row);
		$view->setTask(true);
		$view->setLayout('viewproduct');
		$view->display();
	}

	/**
	 *
	 * Enter description here ...
	 * @param $product_id
	 * @param $values
	 * @return unknown_type
	 */
	function getAddToCart($product_id, $values = array())
	{
		$app = JFactory::getApplication();
		$html = '';
		DSCModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_citruscart/models');
		$model = DSCModel::getInstance('Products', 'CitruscartModel');
		$model->setId($product_id);

		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$filter_group = CitruscartHelperUser::getUserGroup($user_id, $product_id);

		$view = $this->getView('pos', 'html');
		$view->setModel($model, true);
		$model->setState('filter_group', $filter_group);
		$row = $model->getItem(false);

		$view->assign('product', $row );
		$view->setLayout('viewproduct');



		ob_start();
		JFactory::getApplication()->triggerEvent('onDisplayProductAttributeOptions', array($product_id));
		$view->assign('onDisplayProductAttributeOptions', ob_get_contents());
		ob_end_clean();

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Method to item to cart
	 */
	function addToCart()
	{
		$app = JFactory::getApplication();
		$post = $app->input->get($_POST);
		//$post = JRequest::get('post');
		//$files = JRequest::get('files');
		$files = $app->input->get($_FILES);

		$product_id = $post['product_id'];

		// get attributes
		$attributes = array();
		foreach($post as $key => $value)
		{
			if(substr($key, 0, 10) == 'attribute_')
				$attributes[] = $value;
		}
		sort($attributes);
		$attributes_csv = implode(',', $attributes);

		$product_qty = $post['quantity'];
		// Integrity checks on quantity being added
		if($product_qty < 0)
			$product_qty = '1';

		// check product if available
		$availableQuantity = Citruscart::getClass('CitruscartHelperProduct', 'helpers.product')->getAvailableQuantity($product_id, $attributes_csv);
		if($availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity)
		{
			$messagetype = 'notice';
			$message = JText::_(JText::sprintf('COM_CITRUSCART_NOT_AVAILABLE_QUANTITY_NOTICE', $availableQuantity->product_name, $product_qty));
			$this->setRedirect('index.php?option=com_citruscart&view=pos&task=addproducts&tmpl=component', $message, $messagetype);
			return ;
		}

		// chec if product for sale
		$product = JTable::getInstance('Products', 'CitruscartTable');
		$product->load( array('product_id' => $product_id));

		// if product notforsale, fail
		if($product->product_notforsale)
		{
			$messagetype = 'notice';
			$message = JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE_NOTICE');
			$this->setRedirect('index.php?option=com_citruscart&view=pos&task=addproducts&tmpl=component', $message, $messagetype);
			return ;
		}

		$session = JFactory::getSession();
		$cart_id = $session->get('user_id', '', 'citruscart_pos');
		// userid from session
		$id_type = "user_id";

		// create cart object out of item properties
		$item = new JObject;
		$item->user_id = $cart_id;
		//TODO: need to determine what user
		$item->product_id = ( int )$product_id;
		$item->product_qty = ( int )$product_qty;
		$item->product_attributes = $attributes_csv;
		$item->vendor_id = '0';
		// vendors only in enterprise version

		$canAddToCart = Citruscart::getClass('CitruscartHelperCarts', 'helpers.carts')->canAddItem($item, $cart_id, $id_type);

		// onAfterCreateItemForAddToCart: plugin can add values to the item before it is being validated /added
		// once the extra field(s) have been set, they will get automatically saved

		$results = JFactory::getApplication()->triggerEvent("onAfterCreateItemForAddToCart", array($item, $post, $files));
		foreach($results as $result)
		{
			foreach($result as $key => $value)
			{
				$item->set($key, $value);
			}
		}

		// no matter what, fire this validation plugin event for plugins that extend the checkout workflow
		$results = array();

		$results = JFactory::getApplication()->triggerEvent("onBeforeAddToCart", array($item, $post));

		for($i = 0; $i < count($results); $i++)
		{
			$result = $results[$i];
			if(!empty($result->error))
			{
				$messagetype = 'notice';
				$message = JText::_(JText::sprintf('COM_CITRUSCART_NOT_AVAILABLE_QUANTITY_NOTICE', $availableQuantity->product_name, $product_qty));
				$this->setRedirect('index.php?option=com_citruscart&view=pos&task=addproducts&tmpl=component', $result->message, 'notice');
				return ;
			}
		}

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$table = JTable::getInstance('Carts', 'CitruscartTable');

		// first, determine if this product+attribute+vendor(+additonal_keys) exists in the cart
		// if so, update quantity
		// otherwise, add as new item
		// return the cart object with cart_id (to be used by plugins, etc)

		$keynames = array();
		$keynames['user_id'] = $item->user_id;
		if(empty($item->user_id))
		{
			$keynames['session_id'] = $session->getId();
		}
		$keynames['product_id'] = $item->product_id;
		$keynames['product_attributes'] = $item->product_attributes;

		// fire plugin event: onGetAdditionalCartKeyValues
		// this event allows plugins to extend the multiple-column primary key of the carts table
		$additionalKeyValues = CitruscartHelperCarts::getAdditionalKeyValues($item, $post, null);
		if(!empty($additionalKeyValues))
		{
			$keynames = array_merge($keynames, $additionalKeyValues);
		}

		if($table->load( $keynames ) )
		{
			$table->product_qty = $table->product_qty + $item->product_qty;
		}
		else
		{
			foreach($item as $key => $value)
			{
				if(property_exists($table, $key))
				{
					$table->set($key, $value);
				}
			}
		}

		// Now for Eavs!!
		$eavs = CitruscartHelperEav::getAttributes('products', $item->product_id, false, array(1, 2));

		if(count($eavs))
		{
			foreach($eavs as $eav)
			{
				// Search for user edtable fields & user submitted value
				if( in_array($eav->editable_by, array(1, 2) ) !== false && array_key_exists($eav->eavattribute_alias, $item))
				{
					$key = $eav->eavattribute_alias;
					$table->set($key, $item->$key);
				}
			}
		}

		$date = JFactory::getDate();
		$table->last_updated = $date->toSql();
		$table->session_id = $session->getId();

		if(!$table->save())
		{
			JError::raiseNotice('updateCart', $table->getError());
		}
		else
		{
			$this->fixQuantities($item->user_id);
		}
		$this->setRedirect('index.php?option=com_citruscart&view=pos&task=addproducts&added=1&tmpl=component', JText::_('COM_CITRUSCART_SUCCESSFULLY_ADDED_ITEM_TO_CART'), 'success');
	}

	function getCartView()
	{
		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');

		$model = $this->getModel('Carts');
		$model->setState('filter_user', $user_id);
		$items = $model->getList();
		if(!empty($items))
		{
			//trigger the onDisplayCartItem for each cartitem


			$i = 0;
			$onDisplayCartItem = array();
			foreach($items as $key => $item)
			{
				ob_start();
				JFactory::getApplication()->triggerEvent('onDisplayCartItem', array($i,
				$item));
				$cartItemContents = ob_get_contents();
				ob_end_clean();
				if(!empty($cartItemContents))
				{
					$onDisplayCartItem[$i] = $cartItemContents;
				}
				$i++;
			}
			$view = $this->getView('pos', 'html');
			$view->assign('onDisplayCartItem', $onDisplayCartItem);
			$view->assign('items', $items);
			$view->setModel($model, true);
			$view->setLayout('cart');

			ob_start();
			$view->display();
			$html = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$html = JText::_('COM_CITRUSCART_NO_ITEMS_IN_CART');
		}

		return $html;
	}

	/**
	 * Method to remove items or updated the quantities to a cart
	 */
	function removeItems()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('carts');
		$post = $app->input->getArray($_POST);
		//$post = JRequest::get('post');
		//$cids = JRequest::getVar('cid', array(0), '', 'ARRAY');
		$cids = $app->input->get('cid', array(0), '', 'ARRAY');
		$product_attributes = $app->input->get('product_attributes', array(0), '', 'ARRAY');
		$quantities = $app->input->get('quantities', array(0), '', 'ARRAY');

		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');

		foreach($cids as $cart_id => $product_id)
			{
				$row = $model->getTable();
				$ids = array('user_id' => $user_id,
				'cart_id' => $cart_id);

				if($return = $row->delete( array('cart_id' => $cart_id)))
				{
					$item = new JObject;
					$item->product_id = $product_id;
					$item->product_attributes = $product_attributes[$cart_id];
					$item->vendor_id = '0';
					// vendors only in enterprise version

					// fire plugin event

					JFactory::getApplication()->triggerEvent('onRemoveFromCart', array($item));
				}
			}



		$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step2");
	}

	function updateQty()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('carts');
		$post = $app->input->getArray($_POST);
		//$post = JRequest::get('post');
		$cids = $app->input->get('cid', array(0), 'ARRAY');
		$product_attributes = $app->input->get('product_attributes', array(0), 'ARRAY');
		$quantities =$app->input->get('quantities', array(0), 'ARRAY');


		$session = JFactory::getSession();
		//$user_id = $session->get('user_id', 765, 'citruscart_pos');
		$user_id = $session->get('user_id', '', 'citruscart_pos');

			foreach($quantities as $cart_id => $value)
			{
				$carts = JTable::getInstance('Carts', 'CitruscartTable');
				$carts->load( array('cart_id' => $cart_id));
				$product_id = $carts->product_id;
				$value = (int)$value;
				$vals = array();
				$vals['user_id'] = $user_id;
				$vals['session_id'] = $session->getId();
				$vals['product_id'] = $product_id;

				$availableQuantity = Citruscart::getClass('CitruscartHelperProduct', 'helpers.product')->getAvailableQuantity($product_id, $product_attributes[$cart_id]);
				if($availableQuantity->product_check_inventory && $value > $availableQuantity->quantity)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CITRUSCART_NOT_AVAILABLE_QUANTITY_NOTICE', $availableQuantity->product_name, $value));
					continue ;
				}

				if($value > 1)
				{
					$product = JTable::getInstance('Products', 'CitruscartTable');
					$product->load( array('product_id' => $product_id));
					if($product->quantity_restriction)
					{
						$min = $product->quantity_min;
						$max = $product->quantity_max;

						if($max)
						{
							if($value > $max)
							{
								$msg = JText::_('COM_CITRUSCART_MAX_QUANTITY_REACHED_MESSAGE') .': '. $max;
								$value = $max;
							}
						}
						if($min)
						{
							if($value < $min)
							{
								$msg = JText::_('COM_CITRUSCART_MIN_QTY_REACHED_MESSAGE') .': '. $min;
								$value = $min;
							}
						}
					}
					if($product->product_recurs)
					{
						$value = 1;
					}
				}

				$row = $model->getTable();
				$vals['product_attributes'] = $product_attributes[$cart_id];
				$vals['product_qty'] = $value;
				if(empty($vals['product_qty']) || $vals['product_qty'] < 1)
				{
					// remove it
					if($return = $row->delete($cart_id))
					{
						$item = new JObject;
						$item->product_id = $product_id;
						$item->product_attributes = $product_attributes[$cart_id];
						$item->vendor_id = '0';
						// vendors only in enterprise version

						// fire plugin event

						JFactory::getApplication()->triggerEvent('onRemoveFromCart', array($item));
					}
				}
				else
				{
					$row->load($cart_id);
					$row->product_qty = $vals['product_qty'];
					$row->save();
				}

			}


	$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step2");
}




	/**
	 * Adjusts cart quantities based on availability
	 *
	 * @see Citruscart/admin/CitruscartHelperCarts::fixQuantities()
	 */
	function fixQuantities($user_id)
	{
		DSCModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/models');
		DSCModel::addIncludePath(JPATH_SITE . DS . 'components/com_citruscart/models');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$product = JTable::getInstance('ProductQuantities', 'CitruscartTable');
		$tableProduct = JTable::getInstance('Products', 'CitruscartTable');

		$suffix = strtolower(CitruscartHelperCarts::getSuffix());
		$model = DSCModel::getInstance('Carts', 'CitruscartModel');

		switch ($suffix)
		{
			case 'sessioncarts' :

			case 'carts' :

			default :
				$model->setState('filter_user', $user_id);

				$cart = $model->getList();
				if(!empty($cart))
				{
					foreach($cart as $cartitem)
					{
						$keynames = array();
						$keynames['user_id'] = $cartitem->user_id;
						if(empty($cartitem->user_id))
						{
							$keynames['session_id'] = $cartitem->session_id;
						}
						$keynames['product_id'] = $cartitem->product_id;
						$keynames['product_attributes'] = $cartitem->product_attributes;

						$tableProduct->load($cartitem->product_id);
						if($tableProduct->quantity_restriction)
						{
							$quantity = $cartitem->product_qty;
							$min = $tableProduct->quantity_min;
							$max = $tableProduct->quantity_max;

							if($max)
							{
								if($cartitem->product_qty > $max)
								{
									$quantity = $max;
								}
							}
							if($min)
							{
								if($cartitem->product_qty < $min)
								{
									$quantity = $min;
								}
							}
							// load table to adjust quantity in cart
							$table = JTable::getInstance('Carts', 'CitruscartTable');
							//$table->load($keynames);
							$table->load( array('cart_id' => $cartitem->cart_id));
							$table->product_id = $cartitem->product_id;
							$table->product_attributes = $cartitem->product_attributes;
							$table->user_id = $cartitem->user_id;
							$table->session_id = $cartitem->session_id;
							// adjust the cart quantity
							$table->product_qty = $quantity;
							$table->save();
						}

						if(empty($tableProduct->product_check_inventory))
						{
							// if this item doesn't check inventory, skip it
							continue ;
						}

						$product->load( array('product_id' => $cartitem->product_id,
						'vendor_id' => '0',
						'product_attributes' => $cartitem->product_attributes));
						if($cartitem->product_qty > $product->quantity)
						{
							// enqueu a system message
							JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CITRUSCART_NOT_AVAILABLE_QUANTITY_NOTICE', $cartitem->product_name, $cartitem->product_qty));

							// load table to adjust quantity in cart
							$table = JTable::getInstance('Carts', 'CitruscartTable');
							$table->load($keynames);
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
	 * Method to populate the order with the items from cart
	 * @return object - order object
	 */
	function populateOrder()
	{
		$session = JFactory::getSession();
		$order = JTable::getInstance('Orders', 'CitruscartTable');
		$order->currency_id = Citruscart::getInstance()->get('default_currencyid', '1');
		// USD is default if no currency selected
		// set the shipping method
		$order->shipping_method_id = Citruscart::getInstance()->get('defaultShippingMethod', '2');

		// set the order's addresses based on the form inputs
		// set to user defaults
		Citruscart::load("CitruscartHelperBase", 'helpers._base');
		$user_helper = CitruscartHelperBase::getInstance('User');
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$billingAddress = $user_helper->getPrimaryAddress($user_id, 'billing');
		$shippingAddress = $user_helper->getPrimaryAddress($user_id, 'shipping');

		$order->setAddress($billingAddress, 'billing');
		$order->setAddress($shippingAddress, 'shipping');

		// get the items and add them to the order
		$items = $this->getProductsInfo();

		foreach($items as $item)
		{
			$order->addItem($item);
		}

		// get the order totals
		$order->calculateTotals();
		return $order;
	}

	function getOrderSummary(&$order)
	{
		$model = $this->getModel('carts');

		JLoader::register( "CitruscartViewPOS", JPATH_ADMINISTRATOR."/components/com_citruscart/views/pos/view.html.php" );
		$view = new CitruscartViewPOS();
		$view->set( '_controller', 'pos' );
		$view->set( '_view', 'pos' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', false);
		$view->setModel( $model, true );
		$view->assign('state', $model->getState());

		$config = Citruscart::getInstance();
		$show_tax = $config->get('display_prices_with_tax');
		$view->assign('show_tax', $show_tax);
		$view->assign('using_default_geozone', false);

		$view->assign('order', $order);
		$orderitems = $order->getItems();
		Citruscart::load("CitruscartHelperTax", 'helpers.tax');

		if($show_tax)
		{
			$geozones = $order->getBillingGeoZones();
			if(empty($geozones))
			{
				// use the default
				$view->assign('using_default_geozone', true);
				$geozones = array( $config->get( 'default_tax_geozone' ) );
			}
			else
			{
				foreach( $geozones as $key => $value )
					$geozones[$key] = $value->geozone_id;
			}
			$taxes = CitruscartHelperTax::calculateGeozonesTax( $orderitems, 4, $geozones );
		}

		$product_helper = CitruscartHelperBase::getInstance('Product');
		$order_helper = CitruscartHelperBase::getInstance('Order');

		$showShipping = false;
		$tax_sum = 0;
		$notfoundShipping = true;
		foreach($orderitems as &$item)
		{
			//check shipping if required
			if($notfoundShipping && ($isShippingEnabled = $product_helper->isShippingEnabled($item->product_id)))
			{
				$showShipping = true;
				$notfoundShipping = false;
			}

			if($show_tax)
			{
				$item->price = $item->orderitem_price + floatval($item->orderitem_attributes_price) + $taxes->product_taxes[ $item->product_id ];
				$item->orderitem_final_price = $item->price * $item->orderitem_quantity;

				$order->order_subtotal += ($taxes->product_taxes[ $item->product_id ] * $item->orderitem_quantity);
			}
			else
				$item->price = $item->orderitem_price + floatval($item->orderitem_attributes_price);
		}

		$view->assign('orderitems', $orderitems);

		if($showShipping)
		{
			$view->assign('shipping_total', $order->getShippingTotal());
			$view->assign('showShipping', $showShipping);
		}

		//START onDisplayOrderItem: trigger plugins for extra orderitem information
		if(!empty($orderitems))
		{
			$onDisplayOrderItem = $order_helper->onDisplayOrderItems($orderitems);
			$view->assign('onDisplayOrderItem', $onDisplayOrderItem);
		}
		//END onDisplayOrderItem

		$view->setLayout('ordersummary');

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Gets an address form for display
	 *
	 * @param string $prefix
	 * @return string html
	 */
	function getAddressForm($prefix, $showShipping=false, $forShipping=false)
	{
		$html = '';
		$model = $this->getModel('Addresses', 'CitruscartModel');
		JLoader::register( "CitruscartViewPOS", JPATH_ADMINISTRATOR."/components/com_citruscart/views/pos/view.html.php" );
		$view = new CitruscartViewPOS();
		$view->set( '_controller', 'pos' );
		$view->set( '_view', 'pos' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', false);
		$view->set('form_prefix', $prefix);
		$view->setModel($model, true);
		$view->setLayout('form_address');

		$view->assign('showShipping', $showShipping);
		$view->assign('forShipping', $forShipping);

		DSCModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/models');
		$countries_model = DSCModel::getInstance('Countries', 'CitruscartModel');
		$default_country = $countries_model->getDefault();
		$default_country_id = $default_country->country_id;

		Citruscart::load('CitruscartSelect', 'library.select');
		$zones = CitruscartSelect::zone('', $prefix . 'zone_id', $default_country_id);

		$view->assign('default_country_id', $default_country_id);
		$view->assign('zones', $zones);

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 *
	 * @param $address_id
	 * @return unknown_type
	 */
	function retrieveAddressIntoArray($address_id)
	{
		$model = DSCModel::getInstance('Addresses', 'CitruscartModel');
		$model->setId($address_id);
		$item = $model->getItem();
		if(is_object($item))
		{
			return    get_object_vars($item);
		}
		return array();
	}

	/**
	 *
	 * @param unknown_type $oldArray
	 * @param unknown_type $old_prefix
	 * @param unknown_type $new_prefix
	 * @param unknown_type $append
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

	function saveAddress()
	{
		$app = JFactory::getApplication();
		$post = $app->input->getArray($_POST);

		$session = JFactory::getSession();
		//$user_id = $session->get('user_id', 764, 'citruscart_pos');
		$user_id = $session->get('user_id', '', 'citruscart_pos');


		$same_as_billing = (!empty($post['sameasbilling'])) ? true : false;
		$billing_input_prefix = 'billing_input_';
		$shipping_input_prefix = 'shipping_input_';

		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$billingAddressArray=array();

		$billing_zone_id = 0;
		$billingAddressArray = $this->filterArrayUsingPrefix($post, $billing_input_prefix, '', false );		// set the zone name
		$bzone = JTable::getInstance('Zones', 'CitruscartTable');
		$bzone->load( $billingAddressArray['zone_id'] );
		$billingAddressArray['zone_name'] = $bzone->zone_name;
		// set the country name
		$billingcountry = JTable::getInstance('Countries', 'CitruscartTable');
		$billingcountry->load( $billingAddressArray['country_id'] );
		$billingAddressArray['country_name'] = $billingcountry->country_name;

		if(array_key_exists('zone_id', $billingAddressArray))
			$billing_zone_id = $billingAddressArray['zone_id'];

		//SHIPPING ADDRESS: get shipping address from dropdown or form (depending on selection)
		$shipping_zone_id = 0;
		$shippingAddressArray = $same_as_billing ? $billingAddressArray : $this->getAddress($shipping_address_id, $shipping_input_prefix, $values);

		if($same_as_billing)
		{
			$shippingAddressArray = $billingAddressArray;
		}
		else
		{
			$shippingaddressArray = $this->filterArrayUsingPrefix($post, $shipping_input_prefix, '', false );
			// set the zone name
			$szone = JTable::getInstance('Zones', 'CitruscartTable');
			$szone->load( $shippingaddressArray['zone_id'] );
			$addressArray['zone_name'] = $szone->zone_name;
			// set the country name
			$shippingcountry = JTable::getInstance('Countries', 'CitruscartTable');
			$shippingcountry->load( $shippingaddressArray['country_id'] );
			$shippingaddressArray['country_name'] = $shippingcountry->country_name;
		}

		if(array_key_exists('zone_id', $shippingAddressArray))
			$shipping_zone_id = $shippingAddressArray['zone_id'];


		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$billingAddress = JTable::getInstance('Addresses', 'CitruscartTable');
		$shippingAddress = JTable::getInstance('Addresses', 'CitruscartTable');

		 //exit;

		// set the order billing address
		$billingAddress->bind($billingAddressArray);
		$billingAddress->user_id = $user_id;
		$billingAddress->save();

		// set the order billing address
		$shippingAddress->bind($shippingAddressArray);
		$shippingAddress->user_id = $user_id;
		$shippingAddress->save();

		$this->setRedirect("index.php?option=com_citruscart&view=pos&nextstep=step3");
	}

	/**
	 *
	 * @param unknown_type $address_id
	 * @param unknown_type $input_prefix
	 * @param unknown_type $form_input_array
	 * @return unknown_type
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
			// set the zone name
			$zone = JTable::getInstance('Zones', 'CitruscartTable');
			$zone->load( @$addressArray['zone_id'] );
			$addressArray['zone_name'] = $zone->zone_name;
			// set the country name
			$country = JTable::getInstance('Countries', 'CitruscartTable');
			$country->load( @$addressArray['country_id'] );
			$addressArray['country_name'] = $country->country_name;
		}
		return $addressArray;
	}


	/**
	 *
	 * @param $values
	 * @para boolean - save the addresses
	 * @return unknown_type
	 */
	function setAddresses(&$order, $values, $saved = false)
	{
		// Get the currency from the configuration
		$currency_id = Citruscart::getInstance()->get('default_currencyid', '1');
		// USD is default if no currency selected
		$billing_address_id = (!empty($values['billing_input_address_id'])) ? $values['billing_input_address_id'] : 0;
		$shipping_address_id = (!empty($values['shipping_input_address_id'])) ? $values['shipping_input_address_id'] : 0;
		//$shipping_method_id     = $values['shipping_method_id'];
		$same_as_billing = (!empty($values['sameasbilling'])) ? true : false;
		$billing_input_prefix = 'billing_input_';
		$shipping_input_prefix = 'shipping_input_';

		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');

		$billing_zone_id = 0;
		//$billingAddressArray = $this->retrieveAddressIntoArray($billing_address_id);
		$billingAddressArray = $this->getAddress( $billing_address_id, $billing_input_prefix, $values );
		if(array_key_exists('zone_id', $billingAddressArray))
			$billing_zone_id = $billingAddressArray['zone_id'];

		//SHIPPING ADDRESS: get shipping address from dropdown or form (depending on selection)
		$shipping_zone_id = 0;
		$shippingAddressArray = $same_as_billing ? $billingAddressArray : $this->getAddress($shipping_address_id, $shipping_input_prefix, $values);

		if(array_key_exists('zone_id', $shippingAddressArray))
			$shipping_zone_id = $shippingAddressArray['zone_id'];


		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$billingAddress = JTable::getInstance('Addresses', 'CitruscartTable');
		$shippingAddress = JTable::getInstance('Addresses', 'CitruscartTable');

		// set the order billing address
		$billingAddress->bind($billingAddressArray);
		$billingAddress->user_id = $user_id;
		if($saved)
			$billingAddress->save();

		$order->setAddress($billingAddress);

		// set the order shipping address
		$shippingAddress->bind($shippingAddressArray);
		$shippingAddress->user_id = $user_id;
		if($saved)
			$shippingAddress->save();

		$order->setAddress($shippingAddress, 'shipping');

		return ;
	}

	function getShippingHtml($order=null)
	{
		$html = '';
		$model = $this->getModel('Checkout', 'CitruscartModel');
		JLoader::register( "CitruscartViewPOS", JPATH_ADMINISTRATOR."/components/com_citruscart/views/pos/view.html.php" );
		$view = new CitruscartViewPOS();
		$view->set( '_controller', 'pos' );
		$view->set( '_view', 'pos' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', false);
		$view->setModel( $model, true );
		$view->setLayout('shipping');

		$rates = $this->getShippingRates($order);
		$default_rate = array();
		if(count($rates) == 1)
		{
			$default_rate = $rates[0];
		}
		$view->assign('rates', $rates);
		$view->assign('default_rate', $default_rate);
		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	function getShippingRates($order=null)
	{
		// get all the enabled shipping plugins
		Citruscart::load('CitruscartHelperPlugin', 'helpers.plugin');
		//$plugins = CitruscartHelperPlugin::getPluginsWithEvent( 'onGetShippingPlugins' );
		$model = DSCModel::getInstance('Shipping', 'CitruscartModel');
		$model->setState('filter_enabled', '1');
		$plugins = $model->getList();



		$rates = array();

		if($plugins)
		{
			foreach($plugins as $plugin)
			{

				$shippingOptions = JFactory::getApplication()->triggerEvent("onGetShippingOptions", array($plugin->element,
				$order));

				if(in_array(true, $shippingOptions, true))
				{
					$results = JFactory::getApplication()->triggerEvent("onGetShippingRates", array($plugin->element,
					$order));
					foreach($results as $result)
					{
						if(is_array($result))
						{
							foreach($result as $r)
							{
								$extra = 0;
								// here is where a global handling rate would be added
								if($global_handling = Citruscart::getInstance()->get('global_handling'))
								{
									$extra = $global_handling;
								}
								$r['extra'] += $extra;
								$r['total'] += $extra;
								$rates[] = $r;
							}
						}
					}
				}
			}
		}

		return $rates;
	}

	/**
	 * Sets the selected shipping method
	 *
	 * @return unknown_type
	 */
	function setShippingMethod()
	{
		$elements = json_decode(preg_replace('/[\n\r]+/', '\n', JRequest::getVar('elements', '', 'post', 'string')));

		// convert elements to array that can be binded
		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = CitruscartHelperBase::getInstance();
		$values = $helper->elementsToArray($elements);
       	$values['sameasbilling'] = isset($values['_checked']['sameasbilling']) && !empty( $values['_checked']['sameasbilling']);

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		// get the order object so we can populate it
		$order = JTable::getInstance('Orders', 'CitruscartTable');

		// bind what you can from the post
		$order->bind($values);

		// set the currency
		$order->currency_id = Citruscart::getInstance()->get('default_currencyid', '1');
		// USD is default if no currency selected

		// set the shipping method
		$order->shipping = new JObject();
		$order->shipping->shipping_price = @$values['shipping_price'];
		$order->shipping->shipping_extra = @$values['shipping_extra'];
		$order->shipping->shipping_name = @$values['shipping_name'];
		$order->shipping->shipping_tax = @$values['shipping_tax'];

		// set the addresses
		$this->setAddresses($order, $values);

		$items = $this->getProductsInfo();
		foreach($items as $item)
		{
			$order->addItem($item);
		}

		// get all coupons and add them to the order
		if(!empty($values['coupons']))
		{
			foreach($values['coupons'] as $coupon_id)
			{
				$coupon = JTable::getInstance('Coupons', 'CitruscartTable');
				$coupon->load( array('coupon_id' => $coupon_id));
				$order->addCoupon($coupon);
			}
		}

		// get the order totals
		$order->calculateTotals();

		// now get the summary
		$html = $this->getOrderSummary($order);

		$response = array();
		$response['msg'] = $html;
		$response['error'] = '';

		// encode and echo (need to echo to send back to browser)
		echo json_encode($response);

		return ;
	}

	function updateShippingRates()
	{
		$input = JFactory::getApplication()->input;
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		Citruscart::load('CitruscartHelperBase', 'helpers._base');
		$helper = CitruscartHelperBase::getInstance();

		// get elements from post
		$elements = json_decode(preg_replace('/[\n\r]+/', '\n', $input->get('elements', '', 'post', 'string')));

		// Test if elements are empty
		// Return proper message to user
		if(empty($elements))
		{
			// do form validation
			// if it fails check, return message
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage(JText::_('COM_CITRUSCART_PARAMETER_VALIDATION_ERROR'), true, false);
			echo( json_encode($response));
			return ;
		}
		// convert elements to array that can be binded
		$submitted_values = $helper->elementsToArray( $elements );
       	$submitted_values['sameasbilling'] = isset($submitted_values['_checked']['sameasbilling']) && !empty( $submitted_values['_checked']['sameasbilling']);
		$msg = $this->validateAddress( $submitted_values, 'shipping');

		if(!empty($msg))
		{
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage("<li>" . implode("</li><li>", $msg) . "</li>", false, false);
			// encode and echo (need to echo to send back to browser)
			echo json_encode($response);
			return;
		}
		$order = $this->populateOrder();
		$this->setAddresses( $order , $submitted_values, false );

		// set response array
		$response = array();
		$response['msg'] = $this->getShippingHtml($order);

		// encode and echo (need to echo to send back to browser)
		echo json_encode($response);

		return ;
	}

	function getPaymentOptionsHtml(&$order)
	{
		$html = '';
		$model = $this->getModel('Checkout', 'CitruscartModel');
		$view = $this->getView('pos', 'html');
		$view->setModel($model, true);
		$view->setLayout('payment_options');

		$payment_plugins = $this->getPaymentOptions($order);
		$view->assign('payment_plugins', $payment_plugins);

		if(count($payment_plugins) == 1)
		{
			$payment_plugins[0]->checked = true;

			$results = JFactory::getApplication()->triggerEvent("onGetPaymentForm", array($payment_plugins[0]->element,
			''));

			$text = '';
			for($i = 0; $i < count($results); $i++)
			{
				$text .= $results[$i];
			}

			$view->assign('payment_form_div', $text);
		}

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	function getPaymentOptions(&$order)
	{
		$options = array();

		if(is_null($order))
			return $options;

		//get payment plugins
		// get all the enabled payment plugins
		Citruscart::load('CitruscartHelperPlugin', 'helpers.plugin');
		$plugins = CitruscartHelperPlugin::getPluginsWithEvent('onGetPaymentPlugins');

		if($plugins)
		{

			foreach($plugins as $plugin)
			{
				$results = JFactory::getApplication()->triggerEvent("onGetPaymentOptions", array($plugin->element,
				$order));
				if(in_array(true, $results, true))
				{
					$options[] = $plugin;
				}
			}
		}

		return $options;
	}

	/**
	 * Fires selected Citruscart payment plugin and captures output
	 * Returns via json_encode
	 *
	 * @return unknown_type
	 */
	function getPaymentForm($element='')
	{
		// Use AJAX to show plugins that are available
		JLoader::import('com_citruscart.library.json', JPATH_ADMINISTRATOR.'/components');
		$values = JRequest::get('post');
		$html = '';
		$text = "";
		$user = JFactory::getUser();
		if(empty($element))
		{
			$element = JRequest::getVar('payment_element');
		}
		$results = array();

		$results = JFactory::getApplication()->triggerEvent("onGetPaymentForm", array($element,
		$values));

		for($i = 0; $i < count($results); $i++)
		{
			$result = $results[$i];
			$text .= $result;
		}

		$html = $text;

		// set response array
		$response = array();
		$response['msg'] = $html;

		// encode and echo (need to echo to send back to browser)
		echo json_encode($response);

		return ;
	}

	// TODO: transfer it to the helper?
	function getProductsInfo()
	{
		Citruscart::load("CitruscartHelperProduct", 'helpers.product');
		$product_helper = CitruscartHelperBase::getInstance('Product');

		DSCModel::addIncludePath(JPATH_SITE . DS . 'components/com_citruscart/models');
		DSCModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/models');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$model = DSCModel::getInstance('Carts', 'CitruscartModel');

		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$model->setState('filter_user', $user_id);

		Citruscart::load("CitruscartHelperBase", 'helpers._base');
		Citruscart::load("CitruscartHelperCarts", 'helpers.carts');
		$user_helper = CitruscartHelperBase::getInstance('User');
		$filter_group = $user_helper->getUserGroup($user_id);
		$model->setState('filter_group', $filter_group);

		$cartitems = $model->getList();

		$productitems = array();
		foreach($cartitems as $cartitem)
		{
			//echo Citruscart::dump($cartitem);
			unset($productModel);
			$productModel = DSCModel::getInstance('Products', 'CitruscartModel');
			$filter_group = $user_helper->getUserGroup($user_id, $cartitem->product_id);
			$productModel->setState('filter_group', $filter_group);
			$productModel->setId($cartitem->product_id);
			if($productItem = $productModel->getItem(false))
			{
				$productItem->price = $productItem->product_price = !$cartitem->product_price_override->override ? $cartitem->product_price : $productItem->price;

				//we are not overriding the price if its a recurring && price
				if(!$productItem->product_recurs && $cartitem->product_price_override->override)
				{
					// at this point, ->product_price holds the default price for the product,
					// but the user may qualify for a discount based on volume or date, so let's get that price override
					// TODO Shouldn't we remove this?  Is it necessary?  $cartitem has already done this in the carts model!
					$productItem->product_price_override = $product_helper->getPrice($productItem->product_id, $cartitem->product_qty, $filter_group, JFactory::getDate()->toSql());
					if(!empty($productItem->product_price_override))
					{
						$productItem->product_price = $productItem->product_price_override->product_price;
					}
				}

				if($productItem->product_check_inventory)
				{
					// using a helper file,To determine the product's information related to inventory
					$availableQuantity = $product_helper->getAvailableQuantity($productItem->product_id, $cartitem->product_attributes);
					if($availableQuantity->product_check_inventory && $cartitem->product_qty > $availableQuantity->quantity && $availableQuantity->quantity >= 1)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CITRUSCART_CART_QUANTITY_ADJUSTED', $productItem->product_name, $cartitem->product_qty, $availableQuantity->quantity));
						$cartitem->product_qty = $availableQuantity->quantity;
					}

					// removing the product from the cart if it's not available
					if($availableQuantity->quantity == 0)
					{
						if(empty($cartitem->user_id))
						{
							CitruscartHelperCarts::removeCartItem($session_id, $cartitem->user_id, $cartitem->product_id);
						}
						else
						{
							CitruscartHelperCarts::removeCartItem($cartitem->session_id, $cartitem->user_id, $cartitem->product_id);
						}
						JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CITRUSCART_NOT_AVAILABLE') . " " . $productItem->product_name);
						continue ;
					}
				}

				// TODO Push this into the orders object->addItem() method?
				$orderItem = JTable::getInstance('OrderItems', 'CitruscartTable');
				$orderItem->product_id = $productItem->product_id;
				$orderItem->orderitem_sku = $cartitem->product_sku;
				$orderItem->orderitem_name = $productItem->product_name;
				$orderItem->orderitem_quantity = $cartitem->product_qty;
				$orderItem->orderitem_price = $productItem->product_price;
				$orderItem->orderitem_attributes = $cartitem->product_attributes;
				$orderItem->orderitem_attribute_names = $cartitem->attributes_names;
				$orderItem->orderitem_attributes_price = $cartitem->orderitem_attributes_price;
				$orderItem->orderitem_final_price = ($orderItem->orderitem_price + $orderItem->orderitem_attributes_price) * $orderItem->orderitem_quantity;


				$results = JFactory::getApplication()->triggerEvent("onGetAdditionalOrderitemKeyValues", array($cartitem));
				foreach($results as $result)
				{
					foreach($result as $key => $value)
					{
						$orderItem->set($key, $value);
					}
				}

				// TODO When do attributes for selected item get set during admin-side order creation?
				array_push($productitems, $orderItem);
			}
		}
		return $productitems;
	}

	/**
	 * Returns a selectlist of zones
	 * Called via Ajax
	 *
	 * @return unknown_type
	 */
	function getZones()
	{
		Citruscart::load( 'CitruscartSelect', 'library.select' );
		$html = '';
		$text = '';

		$country_id = JRequest::getVar('country_id');
		$prefix = JRequest::getVar('prefix');

		if (empty($country_id))
		{
		    $html = JText::_('COM_CITRUSCART_SELECT_COUNTRY');
		}
    		else
		{
		    $html = CitruscartSelect::zone( '', $prefix.'zone_id', $country_id );
		}

		$response = array();
		$response['msg'] = $html . " *";
		$response['error'] = '';

		// encode and echo (need to echo to send back to browser)
		echo ( json_encode($response) );

		return;
	}

	function saveOrder($values)
	{
		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');

		$error = false;
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$order = JTable::getInstance('Orders', 'CitruscartTable');
		$order->bind($values);
		$order->user_id = $user_id;
		$order->ip_address = $_SERVER['REMOTE_ADDR'];
		$this->setAddresses($order, $values);

		$session = JFactory::getSession();
		// set the shipping method
		if($values['shippingrequired'])
		{
			$order->shipping = new JObject();
			$order->shipping->shipping_price = $session->get('shipping_price', '', 'citruscart_pos');
			$order->shipping->shipping_extra = $session->get('shipping_extra', '', 'citruscart_pos');
			$order->shipping->shipping_name = $session->get('shipping_name', '', 'citruscart_pos');
			$order->shipping->shipping_tax = $session->get('shipping_tax', '', 'citruscart_pos');
		}

		// Store the text verion of the currency for order integrity
		Citruscart::load('CitruscartHelperOrder', 'helpers.order');
		$order->order_currency = CitruscartHelperOrder::currencyToParameters($order->currency_id);

		$reviewitems = $this->getProductsInfo();
		foreach($reviewitems as $reviewitem)
		{
			$order->addItem($reviewitem);
		}

		// get all coupons and add them to the order
		$coupons_enabled = Citruscart::getInstance()->get('coupons_enabled');
		$mult_enabled = Citruscart::getInstance()->get('multiple_usercoupons_enabled');
		if(!empty($values['coupons']) && $coupons_enabled)
		{
			foreach($values['coupons'] as $coupon_id)
			{
				$coupon = JTable::getInstance('Coupons', 'CitruscartTable');
				$coupon->load( array('coupon_id' => $coupon_id));
				$order->addCoupon($coupon);
				if(empty($mult_enabled))
				{
					// this prevents Firebug users from adding multiple coupons to orders
					break;
				}
			}
		}

		$order->order_state_id = 15;
		$order->calculateTotals();
		$order->getShippingTotal();
		$order->getInvoiceNumber();

		$model = DSCModel::getInstance('Orders', 'CitruscartModel');
		if($order->save())
		{
			$model->setId($order->order_id);

			// save the order items
			if(!$this->saveOrderItems($order))
			{
				// TODO What to do if saving order items fails?
				$error = true;
			}

			// save the order vendors
			if(!$this->saveOrderVendors($order))
			{
				// TODO What to do if saving order vendors fails?
				$error = true;
			}

			// save the order info
			if(!$this->saveOrderInfo($order))
			{
				// TODO What to do if saving order info fails?
				$error = true;
			}

			// save the order history
			if(!$this->saveOrderHistory($order))
			{
				// TODO What to do if saving order history fails?
				$error = true;
			}

			// save the order taxes
			if(!$this->saveOrderTaxes($order))
			{
				// TODO What to do if saving order taxes fails?
				$error = true;
			}

			// save the order shipping info
			if(!$this->saveOrderShippings($order))
			{
				// TODO What to do if saving order shippings fails?
				$error = true;
			}

			// save the order coupons
			if(!$this->saveOrderCoupons($order))
			{
				// TODO What to do if saving order coupons fails?
				$error = true;
			}

			$model->clearCache();
		}

		return $order;
	}

	/**
	 * Saves each individual item in the order to the DB
	 *
	 * @return unknown_type
	 */
	function saveOrderItems(&$order)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$items = $order->getItems();

		if(empty($items) || !is_array($items))
		{
			$this->setError("saveOrderItems:: " . JText::_('COM_CITRUSCART_ITEMS_ARRAY_INVALID'));
			return false;
		}

		$error = false;
		$errorMsg = "";
		foreach($items as $item)
		{
			$item->order_id = $order->order_id;

			if(!$item->save())
			{
				// track error
				$error = true;
				$errorMsg .= $item->getError();
			}
			else
			{
				//fire onAfterSaveOrderItem

				JFactory::getApplication()->triggerEvent('onAfterSaveOrderItem', array($item));

				// does the orderitem create a subscription?
				if(!empty($item->orderitem_subscription))
				{
					$date = JFactory::getDate();
					// these are only for one-time payments that create subscriptions
					// recurring payment subscriptions are handled differently - by the payment plugins
					$subscription = JTable::getInstance('Subscriptions', 'CitruscartTable');
					$subscription->user_id = $order->user_id;
					$subscription->order_id = $order->order_id;
					$subscription->product_id = $item->product_id;
					$subscription->orderitem_id = $item->orderitem_id;
					$subscription->transaction_id = '';
					// in recurring payments, this is the subscr_id
					$subscription->created_datetime = $date->toSql();
					$subscription->subscription_enabled = '0';
					// disabled at first, enabled after payment clears
					switch($item->subscription_period_unit)
					{
						case "Y" :
							$period_unit = "YEAR";
							break;
						case "M" :
							$period_unit = "MONTH";
							break;
						case "W" :
							$period_unit = "WEEK";
							break;
						case "D" :

						default :
							$period_unit = "DAY";
							break;
					}

					if(!empty($item->subscription_lifetime))
					{
						// set expiration 100 years in future
						$period_unit = "YEAR";
						$item->subscription_period_interval = '100';
						$subscription->lifetime_enabled = '1';
					}
					$database = JFactory::getDBO();
					$query = " SELECT DATE_ADD('{$subscription->created_datetime}', INTERVAL {$item->subscription_period_interval} $period_unit ) ";
					$database->setQuery($query);
					$subscription->expires_datetime = $database->loadResult();

					if(!$subscription->save())
					{
						$error = true;
						$errorMsg .= $subscription->getError();
					}

					// add a sub history entry, email the user?
					$subscriptionhistory = JTable::getInstance('SubscriptionHistory', 'CitruscartTable');
					$subscriptionhistory->subscription_id = $subscription->subscription_id;
					$subscriptionhistory->subscriptionhistory_type = 'creation';
					$subscriptionhistory->created_datetime = $date->toSql();
					$subscriptionhistory->notify_customer = '0';
					// notify customer of new trial subscription?
					$subscriptionhistory->comments = JText::_('COM_CITRUSCART_NEW_SUBSCRIPTION_CREATED');
					$subscriptionhistory->save();
				}

				// Save the attributes also
				if(!empty($item->orderitem_attributes))
				{
					$attributes = explode(',', $item->orderitem_attributes);
					foreach(@$attributes as $attribute)
					{
						unset($productattribute);
						unset($orderitemattribute);
						$productattribute = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
						$productattribute->load($attribute);
						$orderitemattribute = JTable::getInstance('OrderItemAttributes', 'CitruscartTable');
						$orderitemattribute->orderitem_id = $item->orderitem_id;
						$orderitemattribute->productattributeoption_id = $productattribute->productattributeoption_id;
						$orderitemattribute->orderitemattribute_name = $productattribute->productattributeoption_name;
						$orderitemattribute->orderitemattribute_price = $productattribute->productattributeoption_price;
						$orderitemattribute->orderitemattribute_code = $productattribute->productattributeoption_code;
						$orderitemattribute->orderitemattribute_prefix = $productattribute->productattributeoption_prefix;
						if(!$orderitemattribute->save())
						{
							// track error
							$error = true;
							$errorMsg .= $orderitemattribute->getError();
						}
					}
				}
			}
		}

		if($error)
		{
			$this->setError($errorMsg);
			return false;
		}
		return true;
	}

	/**
	 * Saves the order info to the DB
	 * @return unknown_type
	 */
	function saveOrderInfo(&$order)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$row = JTable::getInstance('OrderInfo', 'CitruscartTable');
		$row->order_id = $order->order_id;

		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$row->user_email = JFactory::getUser($user_id)->get('email');

		// Get Addresses
		$shipping_address = $order->getShippingAddress();
		$billing_address = $order->getBillingAddress();

		// billing infos
		$zone = JTable::getInstance('Zones', 'CitruscartTable');
		$zone->load( @$billing_address->zone_id );
		$country = JTable::getInstance('Countries', 'CitruscartTable');
		$country->load( @$billing_address->country_id );

		$row->billing_company = $billing_address->company;
		$row->billing_first_name = $billing_address->first_name;
		$row->billing_last_name = $billing_address->last_name;
		$row->billing_middle_name = $billing_address->middle_name;
		$row->billing_phone_1 = $billing_address->phone_1;
		$row->billing_phone_2 = $billing_address->phone_2;
		$row->billing_fax = $billing_address->fax;
		$row->billing_address_1 = $billing_address->address_1;
		$row->billing_address_2 = $billing_address->address_2;
		$row->billing_city = $billing_address->city;
		$row->billing_postal_code = $billing_address->postal_code;
		$row->billing_zone_id = $billing_address->zone_id;
		$row->billing_country_id = $billing_address->country_id;
    $row->billing_country_name = @$country->country_name;
    $row->billing_zone_name = @$zone->zone_name;

		// shipping infos
		$zone = JTable::getInstance('Zones', 'CitruscartTable');
		$zone->load( @$shipping_address->zone_id );
		$country = JTable::getInstance('Countries', 'CitruscartTable');
		$country->load( @$shipping_address->country_id );

		$row->shipping_company = $shipping_address->company;
		$row->shipping_first_name = $shipping_address->first_name;
		$row->shipping_last_name = $shipping_address->last_name;
		$row->shipping_middle_name = $shipping_address->middle_name;
		$row->shipping_phone_1 = $shipping_address->phone_1;
		$row->shipping_phone_2 = $shipping_address->phone_2;
		$row->shipping_fax = $shipping_address->fax;
		$row->shipping_address_1 = $shipping_address->address_1;
		$row->shipping_address_2 = $shipping_address->address_2;
		$row->shipping_city = $shipping_address->city;
		$row->shipping_postal_code = $shipping_address->postal_code;
		$row->shipping_zone_id = $shipping_address->zone_id;
		$row->shipping_country_id = $shipping_address->country_id;
    $row->shipping_country_name = @$country->country_name;
    $row->shipping_zone_name = @$zone->zone_name;

		if(!$row->save())
		{
			$this->setError($row->getError());
			return false;
		}

		$order->orderinfo = $row;
		return true;
	}

	/**
	 * Adds an order history record to the DB for this order
	 * @return unknown_type
	 */
	function saveOrderHistory(&$order)
	{

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$row = JTable::getInstance('OrderHistory', 'CitruscartTable');
		$row->order_id = $order->order_id;
		$row->order_state_id = $order->order_state_id;

		$row->notify_customer = '0';
		// don't notify the customer on prepayment
		$row->comments = JRequest::getVar('order_history_comments', '', 'post');

		if(!$row->save())
		{
			$this->setError($row->getError());
			return false;
		}
		return true;
	}

	/**
	 * Saves each vendor related to this order to the DB
	 * @return unknown_type
	 */
	function saveOrderVendors(&$order)
	{
		$items = $order->getVendors();

		if(empty($items) || !is_array($items))
		{
			// No vendors other than store owner, so just skip this
			//$this->setError( "saveOrderVendors:: ".JText::_('COM_CITRUSCART_VENDORS_ARRAY_INVALID') );
			//return false;
			return true;
		}

		$error = false;
		$errorMsg = "";
		foreach($items as $item)
		{
			if(empty($item->vendor_id))
			{
				continue ;
			}
			$item->order_id = $order->order_id;
			if(!$item->save())
			{
				// track error
				$error = true;
				$errorMsg .= $item->getError();
			}
		}

		if($error)
		{
			$this->setError($errorMsg);
			return false;
		}
		return true;
	}

	/**
	 * Adds an order tax class/rate record to the DB for this order
	 * for each relevant tax class & rate
	 *
	 * @return unknown_type
	 */
	function saveOrderTaxes(&$order)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');

		$taxclasses = $order->getTaxClasses();
		foreach($taxclasses as $taxclass)
		{
			unset($row);
			$row = JTable::getInstance('OrderTaxClasses', 'CitruscartTable');
			$row->order_id = $order->order_id;
			$row->tax_class_id = $taxclass->tax_class_id;
			$row->ordertaxclass_amount = $order->getTaxClassAmount($taxclass->tax_class_id);
			$row->ordertaxclass_description = $taxclass->tax_class_description;
			$row->save();
		}

		$taxrates = $order->getTaxRates();
		foreach($taxrates as $taxrate)
		{
			unset($row);
			$row = JTable::getInstance('OrderTaxRates', 'CitruscartTable');
			$row->order_id = $order->order_id;
			$row->tax_rate_id = $taxrate->tax_rate_id;
			$row->ordertaxrate_rate = $taxrate->tax_rate;
			$row->ordertaxrate_amount = $order->getTaxRateAmount($taxrate->tax_rate_id);
			$row->ordertaxrate_description = $taxrate->tax_rate_description;
			$row->ordertaxrate_level = $taxrate->level;
			$row->ordertaxclass_id = $taxrate->tax_class_id;
			$row->save();
		}

		// TODO Better error tracking necessary here
		return true;
	}

	/**
	 * Saves the order shipping info to the DB
	 * @return unknown_type
	 */
	function saveOrderShippings(&$order)
	{
		$session = JFactory::getSession();
		$user_id = $session->get('user_id', '', 'citruscart_pos');
		$shipping_plugin = $session->get('shipping_plugin', '', 'citruscart_pos');
		$shipping_name = $session->get('shipping_name', '', 'citruscart_pos');
		$shipping_code = $session->get('shipping_code', '', 'citruscart_pos');
		$shipping_price = $session->get('shipping_price', '', 'citruscart_pos');
		$shipping_tax = $session->get('shipping_tax', '', 'citruscart_pos');
		$shipping_extra = $session->get('shipping_extra', '', 'citruscart_pos');

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
		$row = JTable::getInstance('OrderShippings', 'CitruscartTable');
		$row->order_id = $order->order_id;
		$row->ordershipping_type = $shipping_plugin;
		$row->ordershipping_price = $shipping_price;
		$row->ordershipping_name = $shipping_name;
		$row->ordershipping_code = $shipping_code;
		$row->ordershipping_tax = $shipping_tax;
		$row->ordershipping_extra = $shipping_extra;

		if(!$row->save())
		{
			$this->setError($row->getError());
			return false;
		}

		// Let the plugin store the information about the shipping

		JFactory::getApplication()->triggerEvent("onPostSaveShipping", array($shipping_plugin, $row));

		return true;
	}

	/**
	 * Saves the order coupons to the DB
	 * @return unknown_type
	 */
	function saveOrderCoupons(&$order)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');

		$error = false;
		$errorMsg = "";
		$ordercoupons = $order->getOrderCoupons();
		foreach($ordercoupons as $ordercoupon)
		{
			$ordercoupon->order_id = $order->order_id;
			if(!$ordercoupon->save())
			{
				// track error
				$error = true;
				$errorMsg .= $ordercoupon->getError();
			}
		}

		if($error)
		{
			$this->setError($errorMsg);
			return false;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see CitruscartController::cancel()
	 */
	function cancel()
	{

		$this->redirect = 'index.php?option=com_citruscart&view=orders';
	    parent::cancel();

		// Clear all of the session POS values
	    $session = JFactory::getSession();
		$session->clear('user_id', 'citruscart_pos');
		$session->clear('user_type', 'citruscart_pos');
		$session->clear('new_email', 'citruscart_pos');
		$session->clear('new_name', 'citruscart_pos');
		$session->clear('new_username_create', 'citruscart_pos');
		$session->clear('new_username', 'citruscart_pos');
		$session->clear('anon_emails', 'citruscart_pos');
		$session->clear('anon_email', 'citruscart_pos');
		$session->clear('subtask', 'citruscart_pos');
		$session->clear('payment_plugin', 'citruscart_pos');
		$session->clear('shipping_price', 'citruscart_pos');
		$session->clear('shipping_tax', 'citruscart_pos');
		$session->clear('shipping_name', 'citruscart_pos');
		$session->clear('shipping_code', 'citruscart_pos');
		$session->clear('shipping_extra', 'citruscart_pos');
		$session->clear('customer_note', 'citruscart_pos');
	}

	function addresses()
	{
		$this->set('suffix', 'addresses');
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel($this->get('suffix'));
		$ns = $this->getNamespace();

		$session = JFactory::getSession();
        $state['filter_userid']     = $session->get('user_id', '', 'citruscart_pos');
        $state['filter_deleted']    = '0';

        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

		$view = $this->getView('pos', 'html');
		$view->setModel($model, true);
		$view->assign('state', $model->getState());
		$view->assign('items', $model->getList());
		$view->setTask(true);
		$view->setLayout('addresses');
		$view->display();
	}
	function address()
	{
		$this->set('suffix', 'addresses');
		$model  = $this->getModel( $this->get('suffix') );
        $row = $model->getTable();
        $row->load( $model->getId() );
		$user_id = JFactory::getSession()->get('user_id', '', 'citruscart_pos');
        // if id is present then user is editing, check if user can edit this item
        if (!empty($row->address_id) && $row->user_id != $user_id)
        {
        	$redirect = "index.php?option=com_citruscart&view=pos&task=addresses";
        	$redirect = JRoute::_( $redirect, false );
			$this->message = JText::_('COM_CITRUSCART_CANNOT_EDIT_ADDRESS_NOTICE');
			$this->messagetype = 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
        }

		$view = $this->getView('pos', 'html');
		$view->setModel($model, true);
		$view->setLayout('address');
		$view->setTask(true);
		$view->assign('address', $row);
		$view->display();
	}

	function addaddress()
	{
		$this->set('suffix', 'addresses');
		$post = JRequest::get('post');

		$model = $this->getModel( $this->get('suffix') );
        $row = $model->getTable();
        $row->load( $model->getId() );
        $row->bind( $post);
        $row->_isNew = empty($row->address_id);

        $redirect = "index.php?option=com_citruscart&view=pos&task=addresses";
    	if (JRequest::getVar('tmpl') == 'component')
    	{
        	$redirect .= "&tmpl=component";
        }
        $redirect = JRoute::_( $redirect, false );

      	$user_id = JFactory::getSession()->get('user_id', '', 'citruscart_pos');

        if ($row->_isNew)
        {

            $row->user_id = $user_id;
        }
        elseif ($row->user_id != $user_id)
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_NOT_AUTHORIZED_TO_EDIT_ITEM');
        	$this->setRedirect( $redirect, $this->message, $this->messagetype );
        	return;
        }

        if ( $row->save() )
        {
            $model->setId( $row->address_id );
            $this->messagetype  = 'message';
            $this->message      = JText::_('COM_CITRUSCART_SAVED');


            JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
        }

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	 /**
     * Flags an address
     * @return unknown_type
     */
    function flag()
    {
        $error = false;
        $this->messagetype  = '';
        $this->message      = '';
        $redirect = 'index.php?option=com_citruscart&view=pos&task=addresses';
    	if (JRequest::getVar('tmpl') == 'component')
    	{
        	$redirect .= "&tmpl=component";
        }

		$this->set('suffix', 'addresses');
		$user_id = JFactory::getSession()->get('user_id', '', 'citruscart_pos');
        $model = $this->getModel($this->get('suffix'));
        $row = $model->getTable();

        $task = JRequest::getVar( 'task' );
        $actions = explode( '_', $task );
        if (!is_array($actions))
        {
            $this->message = JText::_('COM_CITRUSCART_INVALID_TASK');
            $this->messagetype = 'notice';
            $redirect = JRoute::_( $redirect, false );
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }
        $act = $actions['1'];
        $errors = array();

        $cids = JRequest::getVar('cid', array (0), 'post', 'array');
        foreach (@$cids as $cid)
        {
            switch($act)
            {
                case "billing":
                    $flag = "is_default_billing"; $value = "1";
                  break;
                case "shipping":
                    $flag = "is_default_shipping"; $value = "1";
                  break;
                case "deleted":
                    $flag = "is_deleted"; $value = "1";
                  break;
                default:
                    $this->message = JText::_('COM_CITRUSCART_INVALID_ACT');
                    $this->messagetype = 'notice';
                    $redirect = JRoute::_( $redirect, false );
                    $this->setRedirect( $redirect, $this->message, $this->messagetype );
                    return;
                  break;
            }
            $row->load($cid);
            if ($row->address_id && $row->user_id == $user_id)
            {
            	$row->$flag = $value;
            	if (!$row->save())
            	{
	                $errors[] = $cid;
	                $this->messagetype = 'notice';
	                $error = true;
            	}
            }
            else
            {
                $errors[] = $cid;
                $this->messagetype = 'notice';
                $error = true;
            }
        }

        if ($error)
        {
            $this->message = JText::_('COM_CITRUSCART_UNABLE_TO_CHANGE').": ".implode(", ", $errors);
        }
            else
        {
            $this->message = "";
        }

        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
        return;
    }

	/**
	 * Returns an array of objects,
	 * each containing the parsed html of all articles that should be displayed
	 * after an order is completed,
	 * based on the defined global article and any product-, shippingmethod-, and paymentmethod-specific articles
	 *
	 * @param $order_id
	 * @return array
	 */
	function getOrderArticles( $order_id )
	{
		Citruscart::load( 'CitruscartArticle', 'library.article' );

		$articles = array();

		DSCModel::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = DSCModel::getInstance( 'OrderItems', 'CitruscartModel' );
		$model->setState( 'filter_orderid', $order_id);
		$orderitems = $model->getList();
		foreach ($orderitems as $item)
		{
			if (!empty($item->product_article))
			{
				$articles[] = CitruscartArticle::display( $item->product_article );
			}
		}


		JFactory::getApplication()->triggerEvent( 'onGetOrderArticles', array( $order_id, &$articles ) );

		return $articles;
	}

	function deleteRequests()
	{
		$hours = Citruscart::getInstance()->get('pos_request_clean_hours', 24);
		Citruscart::getClass('CitruscartQuery', 'library.query');
		$q = new CitruscartQuery();
		$q->delete();
		$q->from( '#__citruscart_posrequests' );
		$q->where( 'created_date < (NOW() - INTERVAL '.$hours.' HOUR)' );
		$db = JFactory::getDbo();
		$db->setQuery( $q );
		if( $db->query() ) {
			$rec = $db->getAffectedRows();
			$this->setMessage( JText::sprintf('COM_CITRUSCART_POS_REQUESTS_DELETED_SUCCESS', $rec) );
		} else {
			$this->setMessage( JText::_('COM_CITRUSCART_POS_REQUESTS_DELETED_FAILED', 'error') );
		}
		$this->setRedirect( 'index.php?option=com_citruscart&view=config&task=orders' );
	}

    /**
     * Used whenever an attribute selection is changed,
     * to update the price and/or attribute selectlists
     *
     * @return unknown_type
     */
    function updateAddToCart( )
    {
    	$app = JFactory::getApplication();
        $response = array( );
        $response['msg'] = '';
        $response['error'] = '';

        // get elements from post
        //$elements = json_decode( preg_replace( '/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
        $elements = json_decode( preg_replace( '/[\n\r]+/', '\n',$app->input->getArray('elements', '', 'post', 'string' ) ) );

        // convert elements to array that can be binded
        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();
        $values = $helper->elementsToArray( $elements );

        // merge current elements with post
        //$request_arr = JRequest::get();
        $request_arr = $app->input->get();
        unset( $request_arr['elements'] );
       $app->input->set( 'elements', null );
        $values = array_merge( $values, $request_arr );
        //JRequest::set( $values, 'POST' );

        $app->input->set( $values, 'POST' );

        if ( empty( $values['product_id'] ) )
        {
            $values['product_id'] = $app->input->getInt( 'product_id', 0 );
        }

        // now get the summary
        $this->display_cartbutton = true;
        Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
		$html = CitruscartHelperProduct::getCartButton( $values['product_id'], 'viewproduct_addtocart', $values );

        $response['msg'] = $html;
        // encode and echo (need to echo to send back to browser)
        echo json_encode( $response );
        return;
    }
}