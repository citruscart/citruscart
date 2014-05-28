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

class DSCControllerConfig extends DSCController
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
        $config = DSCConfig::getInstance();
        $properties = $config->getProperties();

        foreach ($properties as $key => $value )
        {
            unset($row);
            $row = $model->getTable( 'config' );
            $newvalue = $app->input->get( $key );
            //$newvalue = JRequest::getVar( $key );
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
                    $errorMsg .= JText::_( "Could not store")." $key :: ".$row->getError()." - ";
                }
            }
        }

        if ( !$error )
        {
            $this->messagetype  = 'message';
            $this->message      = JText::_( 'Saved' );

            
            JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_( 'Save Failed' )." - ".$errorMsg;
        }

        $redirect = "index.php?option=com_sample";
        $task = $app->input->getString('task');
        switch ($task)
        {
            default:
                $redirect .= "&view=".$this->get('suffix');
              break;
        }

        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }

}

