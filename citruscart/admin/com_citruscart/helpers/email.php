<?php
/*------------------------------------------------------------------------
 # com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
    
class CitruscartHelperEmail extends CitruscartHelperBase
{
    /**
     * Protected! Use getInstance()
     */ 
    protected function CitruscartHelperEmail() 
    {
        parent::__construct();
        $this->use_html = true;
    }
    
    /**
     * Returns 
     * @param mixed Data to send
     * @param type  Type of mail.
     * @return boolean
     */
    public function sendEmailNotices( $data, $type = 'order' ) 
    {
        $mainframe = JFactory::getApplication();
        $success = false;
        $done = array();

        // grab config settings for sender name and email
        $config     = CitruscartConfig::getInstance();
        $mailfrom   = $config->get( 'shop_email', '' );
        if( !strlen( $mailfrom ) )
        	$mailfrom = $mainframe->getCfg('mailfrom');
        	
        $fromname   = $config->get( 'shop_email_from_name', '' );
        if( !strlen( $fromname ) )
        	$fromname = $mainframe->getCfg('fromname');
        
        $sitename   = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $siteurl    = $config->get( 'siteurl', JURI::root() );
        switch( $type )
        {
        	case 'subscription_expiring':
        	case 'subscription_expired' :
        	case 'subscription_new':
        	case 'new_subscription':
        	case 'subscription':
        		$recipients = $this->getEmailRecipients( $data->subscription_id, $type );
        		break;
        	default :
        		$recipients = $this->getEmailRecipients( $data->order_id, $type );
        		break;
        }
        $content = $this->getEmailContent( $data, $type );
        
        // trigger event onAfterGetEmailContent 
        $dispatcher= JDispatcher::getInstance(); 
        JFactory::getApplication()->triggerEvent('onAfterGetEmailContent', array( $data, &$content) ); 
                
        //$this->results = array();
        for ($i=0; $i<count($recipients); $i++) 
        {
            $recipient = $recipients[$i];
            if (!isset($done[$recipient])) 
            {
                if ( $send = $this->_sendMail( $mailfrom, $fromname, $recipient, $content->subject, $content->body ) ) 
                {
                    $success = true;
                    $done[$recipient] = $recipient;
                }
                //$this->results[$recipient] = $send;
            }
        }
        
        //$this->recipients = $recipients;
        //$this->content = $content;
        
        return $success;
    }

    /**
     * Returns an array of user objects
     * of all users who should receive this email 
     *  
     * @param $data Object
     * @return array
     */
    private function getEmailRecipients( $id, $type = 'order' ) 
    {
      $recipients = array();
       
      switch ($type)
      {
      	    case 'subscription_expiring':
    		case 'subscription_expired' :
    		case 'subscription_new':
    		case 'new_subscription':
    		case 'subscription':
	    		$model = Citruscart::getClass('CitruscartModelSubscriptions', 'models.subscriptions');
	    		$model->setId( $id );
	    		$subscription = $model->getItem();
    			
	    		$model_order = Citruscart::getClass('CitruscartModelOrders', 'models.orders');
	    		$model_order->setId( $subscription->order_id );
	    		$order = $model_order->getItem();
	    			
	    		if( $subscription->user_id > 0 ) // not a guest account
	    		{
		    		$user = JUser::getInstance( $subscription->user_id );
	    			// string needle NOT found in haystack
	    			if (!in_array($user->email, $recipients) && JMailHelper::isEmailAddress($user->email))
	    			{
	    				$recipients[] = $user->email;    
	    			}
	    		}
	    		else 
	    		{
	    			// add the userinfo email to the list of recipients
	    			if (!in_array($order->userinfo_email, $recipients) && JMailHelper::isEmailAddress($order->userinfo_email))
	    			{
	    				$recipients[] = $order->userinfo_email;    
	    			}
	    		}
	   			// add the order user_email to the list of recipients
	   			if (!in_array($order->user_email, $recipients) && JMailHelper::isEmailAddress($order->user_email))
	   			{
	   				$recipients[] = $order->user_email;    
	   			}
	   			break;
	   		case "new_order":
	   			$system_recipients = $this->getSystemEmailRecipients();
	   			foreach ($system_recipients as $r)
	   			{
	   				if (!in_array($r->email, $recipients))
	   				{
	   					$recipients[] = $r->email;    
	   				}
	   			}
	   			
      	  $additional_recipients = $this->getAdditionalEmailRecipients();
	   			foreach ($additional_recipients as $r)
	   			{
	   				if (!in_array($r, $recipients))
	   				{
	   					$recipients[] = $r;    
	   				}
	   			}
	   			
	   			$model = Citruscart::getClass('CitruscartModelOrders', 'models.orders');
	   			$model->setId( $id );
	   			$order = $model->getItem();
	   			jimport('joomla.mail.helper');
	   			
	   			// add the userinfo user_email to the list of recipients
	   			if (!in_array($order->userinfo_email, $recipients) && JMailHelper::isEmailAddress($order->userinfo_email))
	   			{
	   				$recipients[] = $order->userinfo_email;    
	   			}
               
	   			// add the order user_email to the list of recipients
	   			if (!in_array($order->user_email, $recipients) && JMailHelper::isEmailAddress($order->user_email))
	   			{
	   				$recipients[] = $order->user_email;    
	   			}
	   		case 'order':
	   		default:                
	   			$model = Citruscart::getClass('CitruscartModelOrders', 'models.orders');
	   			$model->setId( $id );
	   			$order = $model->getItem();
	   			
	    		if( $order->user_id > 0 ) // not a guest account
	   			{
	   				$user = JUser::getInstance( $order->user_id );
	   				// string needle NOT found in haystack
	   				if (!in_array($user->email, $recipients))
	   				{
	   					$recipients[] = $user->email;    
	   				}
	   			}
	   			else 
	   			{
	   				// add the userinfo email to the list of recipients
	   				if (!in_array($order->userinfo_email, $recipients) && JMailHelper::isEmailAddress($order->userinfo_email))
	   				{
	   					$recipients[] = $order->userinfo_email;    
	   				}
	   			}
               
	   			// add the order user_email to the list of recipients
	   			if (!in_array($order->user_email, $recipients) && JMailHelper::isEmailAddress($order->user_email))
	   			{
	   				$recipients[] = $order->user_email;    
	   			}
	   			break;
      }
      // allow plugins to modify the order email recipient list
      
      JFactory::getApplication()->triggerEvent( 'onGetEmailRecipients', array( $id, $type, &$recipients ) );
        
      return $recipients;
    }

    /**
     * Returns 
     * 
     * @param object
     * @param mixed Boolean
     * @param mixed Boolean
     * @return array
     */
    private function getEmailContent( $data, $type = 'order' ) 
    {
        $mainframe = JFactory::getApplication();
        $type = strtolower($type);  

        $lang = JFactory::getLanguage();
        $lang->load('com_citruscart', JPATH_ADMINISTRATOR);
        
        $return = new stdClass();
        $return->body = '';
        $return->subject = '';

        // get config settings
        $config = CitruscartConfig::getInstance();
        $sitename = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $siteurl = $config->get( 'siteurl', JURI::root() );
        
        // get the placeholders array here so the switch statement can add to it
        $placeholders = $this->getPlaceholderDefaults();
        
        switch ($type) 
        {
            case "subscription_expiring":
                $return->subject    = JText::_('COM_CITRUSCART_EMAIL_EXPIRING_SUBSCRIPTION_SUBJECT');
                $return->body       = JText::_('COM_CITRUSCART_EMAIL_EXPIRING_SUBSCRIPTION_BODY');
                if ($this->use_html)
                {
                    $return->body = nl2br( $return->body );
                }
                $placeholders['user.name'] = $data->user_name;
                $placeholders['product.name'] = $data->product_name;
              break;

            case "subscription_expired":
                $return->subject    = JText::_('COM_CITRUSCART_EMAIL_EXPIRED_SUBSCRIPTION_SUBJECT');
                $return->body       = JText::_('COM_CITRUSCART_EMAIL_EXPIRED_SUBSCRIPTION_BODY');
                if ($this->use_html)
                {
                    $return->body = nl2br( $return->body );
                }
                                
                $placeholders['user.name'] = $data->user_name;
                $placeholders['product.name'] = $data->product_name;
              break;
            
            case "subscription_new":
            case "new_subscription":
            case "subscription":
            		$user_name = JText::_('COM_CITRUSCART_GUEST');
            		if( $data->user_id > 0 )
            		{
	                $user = JUser::getInstance($data->user_id);
	                $user_name = $user->name;
            		}
            		if( $data->user_id < Citruscart::getGuestIdStart() )
            			$link = JURI::root()."index.php?option=com_citruscart&view=orders&task=view&id=".$data->order_id.'&h='.$data->order_hash;
            		else
            			$link = JURI::root()."index.php?option=com_citruscart&view=orders&task=view&id=".$data->order_id;
                $link = JRoute::_( $link, false );
                $link = "<a href='{$link}'>" . $link . "</a>";
                                
                if ( count($data->history) == 1 )
                {
                    // new order
                    $return->subject = sprintf( JText::_('COM_CITRUSCART_EMAIL_NEW_ORDER_SUBJECT'), $data->order_id );

                    // set the email body
                    $text = sprintf(JText::_('COM_CITRUSCART_EMAIL_DEAR'),$user_name).",\n\n";
                    $text .= JText::_('COM_CITRUSCART_EMAIL_THANKS_NEW_SUBSCRIPTION')."\n\n";
                    $text .= sprintf(JText::_('COM_CITRUSCART_EMAIL_CHECK'),$link)."\n\n";
                    $text .= JText::_('COM_CITRUSCART_EMAIL_RECEIPT_FOLLOWS')."\n\n";
                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }
                    
                    // get the order body
                    Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
                    $text .= CitruscartHelperOrder::getOrderHtmlForEmail( $data->order_id );
                }
                    else
                {
                    // Status Change
                    $return->subject = JText::_('COM_CITRUSCART_EMAIL_SUBSCRIPTION_STATUS_CHANGE');
                    $last_history = count($data->history) - 1;
                    
                    $text = sprintf(JText::_('COM_CITRUSCART_EMAIL_DEAR'),$user_name).",\n\n";
                    $text .= sprintf( JText::_('COM_CITRUSCART_EMAIL_ORDER_UPDATED'), $data->order_id );
                    if (!empty($data->history[$last_history]->comments))
                    {
                        $text .= sprintf( JText::_('COM_CITRUSCART_EMAIL_ADDITIONAL_COMMENTS'), $data->history[$last_history]->comments );
                    }
                    $text .= sprintf(JText::_('COM_CITRUSCART_EMAIL_CHECK'),$link)."\n\n";

                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }
                }
                
                $return->body = $text;
                
                $placeholders['user.name'] = $user_name;
                
                break;
                
            case "new_order":
            case "order":
            default:
								$user_name = JText::_('COM_CITRUSCART_GUEST');
            		if( $data->user_id > 0 )
            		{
	                $user = JUser::getInstance($data->user_id);
	                $user_name = $user->name;
            		}
            		if( $data->user_id < Citruscart::getGuestIdStart() )
            			$link = JURI::root()."index.php?option=com_citruscart&view=orders&task=view&id=".$data->order_id.'&h='.$data->order_hash;
            		else
            			$link = JURI::root()."index.php?option=com_citruscart&view=orders&task=view&id=".$data->order_id;
            			
                $link = JRoute::_( $link, false );
                $link = "<a href='{$link}'>" . $link . "</a>";
                
                if ( $type == 'new_order' )
                {
                    // new order
                    $return->subject = sprintf( JText::_('COM_CITRUSCART_EMAIL_NEW_ORDER_SUBJECT'), $data->order_id );

                    // set the email body
                    $text = sprintf(JText::_('COM_CITRUSCART_EMAIL_DEAR'),$user_name).",\n\n";
                    $text .= JText::_('COM_CITRUSCART_EMAIL_THANKS_NEW_ORDER')."\n\n";
                    $text .= sprintf(JText::_('COM_CITRUSCART_EMAIL_CHECK'),$link)."\n\n";
                    $text .= JText::_('COM_CITRUSCART_EMAIL_RECEIPT_FOLLOWS')."\n\n";
                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }
                    
                    // get the order body
                    Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
                    $text .= CitruscartHelperOrder::getOrderHtmlForEmail( $data->order_id );
                }
                    else
                {
                    // Status Change
                    $return->subject = JText::_('COM_CITRUSCART_EMAIL_ORDER_STATUS_CHANGE');
                    $last_history = count($data->orderhistory) - 1;

                    $text = sprintf(JText::_('COM_CITRUSCART_EMAIL_DEAR'),$user_name).",\n\n";
                    $text .= sprintf( JText::_('COM_CITRUSCART_EMAIL_ORDER_UPDATED'), $data->order_id );
                    $text .= JText::_('COM_CITRUSCART_EMAIL_NEW_STATUS')." ".$data->orderhistory[$last_history]->order_state_name."\n\n";
                    if (!empty($data->orderhistory[$last_history]->comments))
                    {
                        $text .= sprintf( JText::_('COM_CITRUSCART_EMAIL_ADDITIONAL_COMMENTS'), $data->orderhistory[$last_history]->comments );
                    }
                    $text .= sprintf(JText::_('COM_CITRUSCART_EMAIL_CHECK'),$link)."\n\n";

                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }
                }
                
                $return->body = $text;
                
                $placeholders['user.name'] = $user_name;
              break;
        }        
        // replace placeholders in language strings - great idea, Oleg
        $return->subject = $this->replacePlaceholders($return->subject, $placeholders);
        $return->body = $this->replacePlaceholders($return->body, $placeholders);
        
        return $return;

    }

    /**
     * Prepares and sends the email
     * 
     * @param unknown_type $from
     * @param unknown_type $fromname
     * @param unknown_type $recipient
     * @param unknown_type $subject
     * @param unknown_type $body
     * @param unknown_type $actions
     * @param unknown_type $mode
     * @param unknown_type $cc
     * @param unknown_type $bcc
     * @param unknown_type $attachment
     * @param unknown_type $replyto
     * @param unknown_type $replytoname
     * @return unknown_type
     */
    private function _sendMail( $from, $fromname, $recipient, $subject, $body, $actions=NULL, $mode=NULL, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) 
    {
        $success = false;
        $mailer = JFactory::getMailer();
        $mailer->addRecipient( $recipient );
        $mailer->setSubject( $subject );
        
        // check user mail format type, default html
        $mailer->IsHTML($this->use_html);
        $body = htmlspecialchars_decode( $body );
        $mailer->setBody( $body );
            
        $sender = array( $from, $fromname );
        $mailer->setSender($sender);
        $sent = $mailer->send();
        if ($sent == '1') 
        {
            $success = true;
        }
        
        return $success;
    }
    
    /**
     * Gets all targets for system emails
     * 
     * return array of objects
     */
    function getSystemEmailRecipients()
    {
        $db = JFactory::getDbo();
        $query = "
            SELECT tbl.email
            FROM #__users AS tbl
						WHERE tbl.sendEmail = 1 AND tbl.block = 0
				        "; 
        $db->setQuery( $query );
        $items = $db->loadObjectList();
        if (empty($items))
        {
            return array();
        }
        return $items;
    }
    
    /**
     * 
     * 
     * return array of emails
     */
    function getAdditionalEmailRecipients()
    {
        $items = array();
        
        $order_emails = CitruscartConfig::getInstance()->get('order_emails');
        if (empty($order_emails))
        {
            return $items;
        }
        
        if ($csv = explode(',', $order_emails))
        {
            foreach ($csv as $email) 
            {
                $email = trim($email);
                if (!in_array($email, $items))
                {
                	if( strlen( $email ) )
                    $items[] = $email;
                }
            }
        }

        if ($nlsv = explode("\n", $order_emails))
        {
            foreach ($nlsv as $email) 
            {
                $email = trim($email);
                if (!in_array($email, $items))
                {
                	if( strlen( $email ) )
                		$items[] = $email;
                }
            }
        }
        
        return $items;
    }
    
    /**
     * Creates the placeholder array with the default site values
     * 
     * @return unknown_type
     */
    function getPlaceholderDefaults()
    {
        $mainframe = JFactory::getApplication();
        $config =  CitruscartConfig::getInstance();
        $site_name              = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $site_url               = $config->get( 'siteurl', JURI::root() );
        $link_my_subscriptions  = $config->get( 'link_my_subscriptions', JURI::root()."/index.php?option=com_citruscart&view=subscriptions" );
        $user_name              = JText::_( $config->get( 'default_email_user_name', 'COM_CITRUSCART_VALUED_CUSTOMER' ) );
        
        // default placeholders
        $placeholders = array(
            'site.name'                 => $site_name,
            'site.url'                  => $site_url,
            'user.name'                 => $user_name,
            'link.my_subscriptions'     => $link_my_subscriptions
        );
        
        return $placeholders;
    }
    
    /**
     * Replaces placeholders with their values
     * 
     * @param string $text
     * @param array $placeholders
     * @return string
     * @access public
     */
    function replacePlaceholders($text, $placeholders)
    {
        $plPattern = '{%key%}';
        
        $plKeys = array();
        $plValues = array();
        
        foreach ($placeholders as $placeholder => $value) {
            $plKeys[] = str_replace('key', $placeholder, $plPattern);
            $plValues[] = $value;
        }
        
        $text = str_replace($plKeys, $plValues, $text);     
        return $text;
    }
    
    /**
     * Method to send the question ask by the customer to the site vendor
     * 
     * @param object $sendObject
     * @return boolean
     */
    function sendEmailToAskQuestionOnProduct($sendObject)
    {
    	$config = CitruscartConfig::getInstance();
		$lang = JFactory::getLanguage();
        $lang->load('com_citruscart', JPATH_ADMINISTRATOR);
    	//set the email subject
    	$subject = "[".$config->get('shop_name', 'SHOP')." - ".JText::_('COM_CITRUSCART_PRODUCT')." #{$sendObject->item->product_id} ] ";
    	$subject .= JText::_('COM_CITRUSCART_PRODUCT_INQUIRIES');
    	$sendObject->subject = $subject;
    	
    	$vendor_name = $config->get('shop_owner_name', 'Admin');
    	// set the email body
        $text = sprintf(JText::_('COM_CITRUSCART_EMAIL_DEAR'),$vendor_name).",\n\n";
     	$text .= $sendObject->namefrom.' '.JText::_('COM_CITRUSCART_HAS_SOME_INQUIRIES_ABOUT_THE_PRODUCT')." ".$sendObject->item->product_name." #{$sendObject->item->product_id} ".JText::_('COM_CITRUSCART_AND_HERES_WHAT_HE_HAS_TO_SAY')." -\n\n";
		$text .= "------------------------------------------------------------------------------------------\n";
     	$text .= $sendObject->body;
     	$text .= "\n------------------------------------------------------------------------------------------";
		$text .= "\n\n";
		$text .= JText::_('COM_CITRUSCART_PLEASE_USE_THE_LINK_BELOW_TO_VIEW_THE_PRODUCT')."\n\n";		
		$text .= JText::_('COM_CITRUSCART_CLICK_THIS_LINK_TO');
		$link = JURI::root().$sendObject->item->link;
		$text .= " <a href='{$link}'>";
		$text .= JText::_('COM_CITRUSCART_EMAIL_VIEW_PRODUCT').".";
		$text .= "</a>";
     	
        if ($this->use_html)
        {
        	$text = nl2br( $text );
        }

    	$success = false;    	
    	if ( $send = $this->_sendMail( $sendObject->mailfrom, $sendObject->namefrom, $sendObject->mailto, $sendObject->subject, $text ) ) 
        {
        	$success = true;                    
        }
        
        return $success;
    }

 /**
	 * Method to send a notice about low quantity of a product in the stock
	 *
	 * @param  string productquantity_id
	 * @return boolean
	 */
	function sendEmailLowQuanty( $productquantity_id )
	{
		$mainframe = JFactory::getApplication();
		$recipients = array();
		$done = array();
		$lang = JFactory::getLanguage();
		$lang->load( 'com_citruscart', JPATH_ADMINISTRATOR );
		$system_recipients = $this->getSystemEmailRecipients();

		foreach ( $system_recipients as $r )
		{
			if( !in_array( $r->email, $recipients ) )
			{
				$recipients[] = $r->email;
			}
		}
		$config = CitruscartConfig::getInstance();

		$fromname = $config->get('shop_name', 'SHOP');
		$mailfrom = $config->get('shop_email', '');
		if (!strlen($mailfrom))
		{
			$mailfrom = $mainframe->getCfg('mailfrom');
		}
		$vendor_name = $config->get('shop_owner_name', 'Admin');
		$ProductQuantities_model = JTable::getInstance('ProductQuantities', 'CitruscartTable');
		$ProductQuantities_model->load( array( 'productquantity_id' => $productquantity_id ) );
		$quantity = $ProductQuantities_model -> quantity;
		$product_id = $ProductQuantities_model -> product_id;
		$product_attributes_csv = $ProductQuantities_model -> product_attributes;

		if (!empty($product_attributes_csv))
		{
			$productattributeoption_id_array = explode(',', $product_attributes_csv);
		}
		else
		{
			$productattributeoption_id_array = NULL;
		}

		$productsTable = JTable::getInstance('Products', 'CitruscartTable');
		$productsTable -> load( $product_id, true, false );
		$product_name = $productsTable-> product_name;
		$subject = "[" . $config -> get('shop_name', 'SHOP') . " - " . JText::sprintf('COM_CITRUSCART_LOW_STOCK_MAIL_SUBJECT_NAME_AND_ID', $product_name, $product_id) . "]";

		// set the email body
		$text = JText::sprintf('COM_CITRUSCART_EMAIL_DEAR', $vendor_name) . ",\n\n";
		$text .= JText::sprintf("COM_CITRUSCART_LOW_STOCK_MAIL_PRODUCT_NAME_AND_ID", $product_name, $product_id) . "\n";
		if (!empty($productattributeoption_id_array))
		{
			foreach ($productattributeoption_id_array as $productattributeoption_id)
			{
				$productattributeoptionsTable = JTable::getInstance('Productattributeoptions', 'CitruscartTable');
				$productattributeoptionsTable -> load($productattributeoption_id);
				$productattribute_id = $productattributeoptionsTable -> productattribute_id;
				$productattributeoption_name = $productattributeoptionsTable -> productattributeoption_name;
				$productattributesTable = JTable::getInstance('Productattributes', 'CitruscartTable');
				$productattributesTable -> load($productattribute_id);
				$productattribute_name = $productattributesTable -> productattribute_name;
				$text .= JText::sprintf( "COM_CITRUSCART_LOW_STOCK_MAIL_OPTION_DETAILS", $productattribute_name, $productattributeoption_name ) . "\n";
			}
		}

		$text .= "\n------------------------------------------------------------------------------------------\n";
		$text .= JText::sprintf("COM_CITRUSCART_LOW_STOCK_MAIL_ITEMS_AVAILABLE", $quantity) . "\n";
		$text .= "------------------------------------------------------------------------------------------";
		$text .= "\n\n";

		if ($this -> use_html)
		{
			$text = nl2br($text);
		}

		$success = false;
		for ($i = 0; $i < count($recipients); $i++)
		{
			$recipient = $recipients[$i];
			if (!isset($done[$recipient]))
			{
				if ($send = $this -> _sendMail($mailfrom, $fromname, $recipient, $subject, $text))
				{
					$success = true;
					$done[$recipient] = $recipient;
				}
			}
		}
		return $success;
	}

}
