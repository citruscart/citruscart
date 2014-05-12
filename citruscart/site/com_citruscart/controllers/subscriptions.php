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

class CitruscartControllerSubscriptions extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		if (empty(JFactory::getUser()->id))
		{
			$url = JRoute::_( "index.php?option=com_citruscart&view=orders" );
      Citruscart::load( "CitruscartHelperUser", 'helpers.user' );
      $redirect = JRoute::_( CitruscartHelperUser::getUserLoginUrl( $url ), false );
			JFactory::getApplication()->redirect( $redirect );
			return;
		}

		parent::__construct();
		$this->set('suffix', 'subscriptions');
    $this->registerTask( 'subscription_enabled.enable', 'boolean' );
    $this->registerTask( 'subscription_enabled.disable', 'boolean' );
    $this->registerTask( 'lifetime_enabled.enable', 'boolean' );
    $this->registerTask( 'lifetime_enabled.disable', 'boolean' );
    $this->registerTask( 'update_subscription', 'update' );
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

        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.created_datetime', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
        $state['filter_orderid']       = $app->getUserStateFromRequest($ns.'filter_orderid', 'filter_orderid', '', '');
        $state['filter_type']       = $app->getUserStateFromRequest($ns.'filter_type', 'filter_type', '', '');
        $state['filter_transaction']    = $app->getUserStateFromRequest($ns.'filter_transaction', 'filter_transaction', '', '');
        $state['filter_user']         = $app->getUserStateFromRequest($ns.'filter_user', 'filter_user', '', '');
        $state['filter_userid']         = $user_id=JFactory::getUser()->id;
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
        $state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
        $state['filter_datetype']   = 'created';
        $state['filter_total_from']    = $app->getUserStateFromRequest($ns.'filter_total_from', 'filter_total_from', '', '');
        $state['filter_total_to']      = $app->getUserStateFromRequest($ns.'filter_total_to', 'filter_total_to', '', '');
		$state['filter_enabled']       = $app->getUserStateFromRequest($ns.'filter_enabled', 'filter_enabled', '', '');
		$state['filter_lifetime']       = $app->getUserStateFromRequest($ns.'filter_lifetime', 'filter_lifetime', '', '');

    	foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
  		return $state;
    }

    /**
     *
     * Adds a subscription history entry to a subscription
     * @return unknown_type
     */
    function unsubscribe()
    {
    	$input =JFactory::getApplication()->input;
        $row = JTable::getInstance('Subscriptions', 'CitruscartTable');
        $id=$input->getInt('id');
        $row->load($id);
        $row->subscription_enabled="0";
        if ($row->save())
        {
            
            JFactory::getApplication()->triggerEvent( 'onAfterUpdateStatus'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
        }

        $redirect = "index.php?option=com_citruscart";
        $redirect .= '&view='.$this->get('suffix').'&task=view&id='.$row->subscription_id;
        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

    /**
     * (non-PHPdoc)
     * @see Citruscart/site/CitruscartController#view()
     */
    function view()
    {
    	// if the user cannot view order, fail
        $model  = $this->getModel( $this->get('suffix') );
        $subscriptions = $model->getTable( 'subscriptions' );
        $subscriptions->load( $model->getId() );
        //$subscriptions->getItems();

        $row = $model->getItem();

        $user_id = JFactory::getUser()->id;
        if (empty($user_id) || $user_id != $row->user_id)
        {
        	$this->messagetype  = 'notice';
        	$this->message      = JText::_('COM_CITRUSCART_INVALID_SUBSCRIPTIONS');
            $redirect = "index.php?option=com_citruscart&view=".$this->get('suffix');
            $redirect = JRoute::_( $redirect, false );
            $this->setRedirect( $redirect, $this->message, $this->messagetype );
            return;
        }

        Citruscart::load( 'CitruscartUrl', 'library.url' );

        $view = $this->getView( 'subscriptions', 'html' );
        $view->set( '_controller', 'subscriptions' );
        $view->set( '_view', 'orders' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $model, true );
        $view->assign( 'order', $subscriptions );

        $view->setLayout( 'view' );
        $view->display();
        $this->footer();
    }

}

?>