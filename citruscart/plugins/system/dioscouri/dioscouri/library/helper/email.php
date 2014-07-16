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


class DSCHelperEmail extends DSCHelper
{
    /**
     * Protected! Use getInstance()
     */
    protected function DSCHelperEmail()
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
        $config     = DSCConfig::getInstance();
        $mailfrom   = $config->get( 'emails_defaultemail', $mainframe->getCfg('mailfrom') );
        $fromname   = $config->get( 'emails_defaultname', $mainframe->getCfg('fromname') );
        $sitename   = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $siteurl    = $config->get( 'siteurl', JURI::root() );

        $recipients = $this->getEmailRecipients( $data->order_id, $type );
        $content = $this->getEmailContent( $data, $type );

        // trigger event onAfterGetEmailContent
        $dispatcher= JDispatcher::getInstance();
        JFactory::getApplication()->triggerEvent('onAfterGetEmailContent', array( $data, &$content) );

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
            }
        }

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
            case "new_order":
                $system_recipients = $this->getSystemEmailRecipients();
                foreach ($system_recipients as $r)
                {
                    if (!in_array($r->email, $recipients))
                    {
                        $recipients[] = $r->email;
                    }
                }
            case 'order':
            default:
                $model = DSC::getClass('DSCModelOrders', 'models.orders');
                $model->setId( $id );
                $order = $model->getItem();

                $user = JFactory::getUser( $order->user_id );
                //$user = JUser::getInstance( $order->user_id );

                // is the email one of our guest emails?
                $pos = strpos($user->email, "guest");
                if ($pos === false)
                {
                    // string needle NOT found in haystack
                    if (!in_array($user->email, $recipients))
                    {
                        $recipients[] = $user->email;
                    }
                }
                    else
                {
                    // add the userinfo email to the list of recipients
                    if (!in_array($order->userinfo_email, $recipients))
                    {
                        $recipients[] = $order->userinfo_email;
                    }
                }

                // add the order user_email to the list of recipients
                if (!in_array($order->user_email, $recipients))
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
        $lang->load('com_sample', JPATH_ADMINISTRATOR);

        $return = new stdClass();
        $return->body = '';
        $return->subject = '';

        // get config settings
        $config = DSCConfig::getInstance();
        $sitename = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $siteurl = $config->get( 'siteurl', JURI::root() );

        // get the placeholders array here so the switch statement can add to it
        $placeholders = $this->getPlaceholderDefaults();

        switch ($type)
        {
            case "subscription_expiring":
                $return->subject    = JText::_( 'EMAIL_EXPIRING_SUBSCRIPTION_SUBJECT' );
                $return->body       = JText::_( 'EMAIL_EXPIRING_SUBSCRIPTION_BODY' );
                if ($this->use_html)
                {
                    $return->body = nl2br( $return->body );
                }
                $placeholders['user.name'] = $data->user_name;
                $placeholders['product.name'] = $data->product_name;
              break;

            case "subscription_expired":
                $return->subject    = JText::_( 'EMAIL_EXPIRED_SUBSCRIPTION_SUBJECT');
                $return->body       = JText::_( 'EMAIL_EXPIRED_SUBSCRIPTION_BODY' );
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
            	$user = JFactory::getUser($data->user_id);
                $link = JURI::root()."index.php?option=com_sample&view=orders&task=view&id=".$data->order_id;
                $link = JRoute::_( $link, false );

                if ( count($data->history) == 1 )
                {
                    // new order
                    $return->subject = sprintf( JText::_('EMAIL_NEW_ORDER_SUBJECT'), $data->order_id );

                    // set the email body
                    $text = JText::_('EMAIL_DEAR') ." ".$user->name.",\n\n";
                    $text .= JText::_("EMAIL_THANKS_NEW_SUBSCRIPTION")."\n\n";
                    $text .= JText::_("EMAIL_CHECK")." ".$link."\n\n";
                    $text .= JText::_("EMAIL_RECEIPT_FOLLOWS")."\n\n";
                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }

                    // get the order body
                    DSC::load( 'DSCHelperOrder', 'helpers.order' );
                    $text .= DSCHelperOrder::getOrderHtmlForEmail( $data->order_id );
                }
                    else
                {
                    // Status Change
                    $return->subject = JText::_( 'EMAIL_SUBSCRIPTION_STATUS_CHANGE' );
                    $last_history = count($data->history) - 1;

                    $text  = JText::_('EMAIL_DEAR') ." ".$user->name.",\n\n";
                    $text .= sprintf( JText::_("EMAIL_ORDER_UPDATED"), $data->order_id );
                    if (!empty($data->history[$last_history]->comments))
                    {
                        $text .= sprintf( JText::_("EMAIL_ADDITIONAL_COMMENTS"), $data->history[$last_history]->comments );
                    }
                    $text .= JText::_("EMAIL_CHECK")." ".$link;

                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }
                }

                $return->body = $text;

                $placeholders['user.name'] = $user->get('name');

                break;

            case "new_order":
            case "order":
            default:
                //$user = JUser::getInstance($data->user_id);
                $user = JFactory::getUser($data->user_id);
                $link = JURI::root()."index.php?option=com_sample&view=orders&task=view&id=".$data->order_id;
                $link = JRoute::_( $link, false );

                if ( $type == 'new_order' )
                {
                    // new order
                    $return->subject = sprintf( JText::_('EMAIL_NEW_ORDER_SUBJECT'), $data->order_id );

                    // set the email body
                    $text = JText::_('EMAIL_DEAR') ." ".$user->name.",\n\n";
                    $text .= JText::_("EMAIL_THANKS_NEW_ORDER")."\n\n";
                    $text .= JText::_("EMAIL_CHECK")." ".$link."\n\n";
                    $text .= JText::_("EMAIL_RECEIPT_FOLLOWS")."\n\n";
                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }

                    // get the order body
                    DSC::load( 'DSCHelperOrder', 'helpers.order' );
                    $text .= DSCHelperOrder::getOrderHtmlForEmail( $data->order_id );
                }
                    else
                {
                    // Status Change
                    $return->subject = JText::_( 'EMAIL_ORDER_STATUS_CHANGE' );
                    $last_history = count($data->orderhistory) - 1;

                    $text  = JText::_('EMAIL_DEAR') ." ".$user->name.",\n\n";
                    $text .= sprintf( JText::_("EMAIL_ORDER_UPDATED"), $data->order_id );
                    $text .= JText::_("EMAIL_NEW_STATUS")." ".$data->orderhistory[$last_history]->order_state_name."\n\n";
                    if (!empty($data->orderhistory[$last_history]->comments))
                    {
                        $text .= sprintf( JText::_("EMAIL_ADDITIONAL_COMMENTS"), $data->orderhistory[$last_history]->comments );
                    }
                    $text .= JText::_("EMAIL_CHECK")." ".$link;

                    if ($this->use_html)
                    {
                        $text = nl2br( $text );
                    }
                }

                $return->body = $text;

                $placeholders['user.name'] = $user->get('name');
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
        $db =JFactory::getDBO();
        $query = "
            SELECT tbl.email
            FROM #__users AS tbl
            WHERE tbl.sendEmail = '1';
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
     * Creates the placeholder array with the default site values
     *
     * @return unknown_type
     */
    function getPlaceholderDefaults()
    {
        $mainframe = JFactory::getApplication();
        $config = DSCConfig::getInstance();
        $site_name              = $config->get( 'sitename', $mainframe->getCfg('sitename') );
        $site_url               = $config->get( 'siteurl', JURI::root() );
        $link_my_subscriptions  = $config->get( 'link_my_subscriptions', JURI::root()."/index.php?option=com_sample&view=subscriptions" );
        $user_name              = JText::_( $config->get( 'default_email_user_name', 'Valued Customer' ) );

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
}