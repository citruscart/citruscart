<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class CitruscartControllerConfig extends CitruscartController
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->set('suffix', 'config');
    }

    /**
     * save a record
     * @return void
     */
    function save()
    {
    	$app = JFactory::getApplication();
        $error = false;
        $errorMsg = "";
        $model  = $this->getModel( $this->get('suffix') );
        $config = Citruscart::getInstance();
        $properties = $config->getProperties();

        foreach ($properties as $key => $value )
        {
            unset($row);

            $row = $model->getTable( 'config' );

            $newvalue =$app->input->getHtml($key);

            $value_exists = array_key_exists( $key, $_POST );

            if ( $value_exists && !empty($key) )
            {
                // proceed if newvalue present in request. prevents overwriting for non-existent values.
                $row->load( array('config_name'=>$key) );
                $row->config_name = $key;
                $row->value = $newvalue;

                if ( !$row->save() )
                {
                    $error = true;
                    $errorMsg .= JText::_('COM_CITRUSCART_COULD_NOT_STORE')." $key :: ".$row->getError()." - ";
                }
            }
        }

        $model->clearCache();

        if ( !$error )
        {
            $this->messagetype  = 'message';
            $this->message      = JText::_('COM_CITRUSCART_SAVED');

            JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$errorMsg;
        }

        $redirect = "index.php?option=com_citruscart&view=".$this->get('suffix');

        $group = $app->input->get('group');

        switch ($group)
        {
            default:
                if ($group) {
                    $redirect .= "&task=" . $group;
                }
                break;
        }

        //$format = JRequest::getVar('format');
        $format = $app->input->get('format');
        if ($format == 'raw')
        {
            $response = array();
            $response['error'] = $error;
            $response['msg'] = $this->message;
            echo json_encode($response);
            return;
        }

        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

    public function all($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
        $app->input->set('layout', 'all');
        parent::display($cachable, $urlparams);
    }

    public function displaysettings($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
    	$app->input->set('layout', 'displaysettings');
        parent::display($cachable, $urlparams);
    }

    public function orders($cachable=false, $urlparams = false)
    {	$app = JFactory::getApplication();
        $app->input->set('layout', 'orders');
        parent::display($cachable, $urlparams);
    }

    public function products($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
        $app->input->set('layout', 'products');
        parent::display($cachable, $urlparams);
    }

    public function emails($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
    	$app->input->set('layout', 'emails');
        parent::display($cachable, $urlparams);
    }

    public function admin($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
       $app->input->set('layout', 'admin');
        parent::display($cachable, $urlparams);
    }

    public function advanced($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();

    	$app->input->set('layout', 'advanced');

    	// $app->input->set('layout', 'advanced');

        parent::display($cachable, $urlparams);
    }

}

