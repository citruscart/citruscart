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

class CitruscartControllerTaxclasses extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'taxclasses');
	}

    /*
     * Creates a popup where rates can be edited & created
     */
    function setrates()
    {
        $this->set('suffix', 'taxrates');
        $state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();
        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

        $row = JTable::getInstance('TaxClasses', 'CitruscartTable');
        $row->load($model->getId());
        $model->setState('filter_taxclassid', $model->getId());

        $view   = $this->getView( 'taxrates', 'html' );
        $view->set( '_controller', 'taxclasses' );
        $view->set( '_view', 'taxclasses' );
        $view->set( '_action', "index.php?option=com_citruscart&controller=taxclasses&task=setrates&id={$model->getId()}&tmpl=component" );
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
        $this->set('suffix', 'taxrates');
        $model  = $this->getModel( $this->get('suffix') );

        $row = $model->getTable();
        $row->bind( $_POST );

        if ( $row->save() )
        {
            $model->clearCache();

            
            JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
            $this->messagetype  = 'notice';
            $this->message = JText::_('COM_CITRUSCART_SAVED');
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
        }

        $redirect = "index.php?option=com_citruscart&controller=taxclasses&task=setrates&id={$row->tax_class_id}&tmpl=component";
        $redirect = JRoute::_( $redirect, false );

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

    /**
     * Saves the properties for all rates in list
     *
     * @return unknown_type
     */
    function saverates()
    {
    	/* Get the applicaiton */
        $app = JFactory::getApplication();
    	$error = false;
        $this->messagetype  = '';
        $this->message      = '';

        $model = $this->getModel('taxrates');

        $row = $model->getTable();

        $cids = $app->input->get('cid', array(0), 'request', 'array');
        $rates = $app->input->get('rate', array(0), 'request', 'array');
        $levels = $app->input->get('levels', array(0), 'request', 'array');
        $descriptions = $app->input->get('description', array(0), 'request', 'array');

        foreach ($cids as $cid)
        {
            $row->load( $cid );
            $row->tax_rate = $rates[$cid];
            $row->tax_rate_description = $descriptions[$cid];
            $row->level = $levels[$cid];

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

        $redirect = "index.php?option=com_citruscart&view=taxclasses&task=setrates&id={$row->tax_class_id}&tmpl=component";
        $redirect = JRoute::_( $redirect, false );

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

}

