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

class CitruscartControllerShippingMethods extends CitruscartController
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->set('suffix', 'shippingmethods');
        $this->registerTask( 'shipping_method_enabled.enable', 'boolean' );
        $this->registerTask( 'shipping_method_enabled.disable', 'boolean' );
        $this->registerTask( 'selected_enable', 'selected_switch' );
        $this->registerTask( 'selected_disable', 'selected_switch' );
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

        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']       = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_enabled']    = $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
        $state['filter_taxclass']   = $app->getUserStateFromRequest($ns.'taxclass', 'filter_taxclass', '', '');
        $state['filter_shippingtype']   = $app->getUserStateFromRequest($ns.'shippingtype', 'filter_shippingtype', '', '');

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

    /*
     * Creates a popup where prices can be edited & created
     */
    function setrates()
    {
        $this->set('suffix', 'shippingrates');
        $state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();
        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

        $row = JTable::getInstance('ShippingMethods', 'CitruscartTable');
        $row->load($model->getId());

        $model->setState('filter_shippingmethod', $model->getId());

        $view   = $this->getView( 'shippingrates', 'html' );
        $view->set( '_controller', 'shippingmethods' );
        $view->set( '_view', 'shippingmethods' );
        $view->set( '_action', "index.php?option=com_citruscart&controller=shippingmethods&task=setrates&id={$model->getId()}&tmpl=component" );
        $view->setModel( $model, true );
        $view->assign( 'state', $model->getState() );
        $view->assign( 'row', $row );
        $view->setLayout( 'default' );
		$view->setTask(true);
        $view->display();
    }

    /**
     * Creates a rate and redirects
     *
     * @return unknown_type
     */
    function createrate()
    {
        $this->set('suffix', 'shippingrates');
        $model  = $this->getModel( $this->get('suffix') );

        $row = $model->getTable();
        $row->bind($_POST);
        if ( $row->save() )
        {
            $model->clearCache();

            
            JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
        }

        $redirect = "index.php?option=com_citruscart&controller=shippingmethods&task=setrates&id={$row->shipping_method_id}&tmpl=component";
        $redirect = JRoute::_( $redirect, false );

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

    /**
     * Saves the properties for all prices in list
     *
     * @return unknown_type
     */
    function saverates()
    {
    	$app = JFactory::getApplication();
        $error = false;
        $this->messagetype  = '';
        $this->message      = '';

        $model = $this->getModel('shippingrates');
        $row = $model->getTable();

        /* $cids = JRequest::getVar('cid', array(0), 'request', 'array');
        $geozones = JRequest::getVar('geozone', array(0), 'request', 'array');
        $prices = JRequest::getVar('price', array(0), 'request', 'array');
        $weight_starts = JRequest::getVar('weight_start', array(0), 'request', 'array');
        $weight_ends = JRequest::getVar('weight_end', array(0), 'request', 'array');
        $handlings = JRequest::getVar('handling', array(0), 'request', 'array'); */

        $cids =$app->input->getArray('cid', array(0), '', 'array');
        $geozones = $app->input->getArray('geozone', array(0), '', 'array');
        $prices = $app->input->getArray('price', array(0), 'request', 'array');
        $weight_starts = $app->input->getArray('weight_start', array(0), 'request', 'array');
        $weight_ends = $app->input->getArray('weight_end', array(0), 'request', 'array');
        $handlings = $app->input->getArray('handling', array(0), 'request', 'array');

        foreach ($cids as $cid)
        {
            $row->load( $cid );
            $row->geozone_id = $geozones[$cid];
            $row->shipping_rate_price = $prices[$cid];
            $row->shipping_rate_weight_start = $weight_starts[$cid];
            $row->shipping_rate_weight_end = $weight_ends[$cid];
            $row->shipping_rate_handling = $handlings[$cid];

            if (!$row->save())
            {
                $this->message .= $row->getError();
                $this->messagetype = 'notice';
                $error = true;
            }
        }

        $model->clearCache();

        if ($error)
        {
            $this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
        }
            else
        {
            $this->message = "";
        }

        $redirect = "index.php?option=com_citruscart&controller=shippingmethods&task=setrates&id={$row->shipping_method_id}&tmpl=component";
        $redirect = JRoute::_( $redirect, false );

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
}

