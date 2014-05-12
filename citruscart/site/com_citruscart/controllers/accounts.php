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

class CitruscartControllerAccounts extends CitruscartController
{
    /**
     * constructor
     */
    function __construct()
    {
        if (empty(JFactory::getUser()->id))
        {
            $url = JRoute::_( "index.php?option=com_citruscart&view=accounts" );
            Citruscart::load( "CitruscartHelperUser", 'helpers.user' );
            $redirect = JRoute::_( CitruscartHelperUser::getUserLoginUrl( $url ), false );
            JFactory::getApplication()->redirect( $redirect );
            return;
        }

        parent::__construct();
        $this->set('suffix', 'accounts');
    }

    /**
     * (non-PHPdoc)
     * @see Citruscart/admin/CitruscartController#_setModelState()
     */
    function _setModelState()
    {
        $state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();

        $state['filter_userid']     = JFactory::getUser()->id;

        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

    function display($cachable=false, $urlparams = false)
    {
        $uri = JURI::getInstance();

        $view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
        $view->set('hidemenu', false);
        $view->set('_doTask', true);
        $view->setLayout('default');

    		if (version_compare(JVERSION, '1.6.0', 'ge')) {
					$url = "index.php?option=com_users&view=user&task=user.edit";
				}
				else {
					$url = "index.php?option=com_user&view=user&task=edit";
				}

        Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
        $helper = CitruscartHelperBase::getInstance( 'Ambra' );
        if ($helper->isInstalled())
        {
            $url = "index.php?option=com_ambra&view=users&task=edit&return=" . base64_encode( $uri->__toString() );
        }
        $view->assign( 'url_profile', $url );

        parent::display($cachable, $urlparams);
    }

    /**
     * @return void
     */
    function edit()
    {
    	$input = JFactory::getApplication()->input;
        $model  = $this->getModel( $this->get('suffix') );
        $row = $model->getTable();
        $row->load( array( 'user_id' => JFactory::getUser()->id ) );

        $input->set('id', $row->user_info_id );
    	$input->set('view', 'accounts');
    	$input->set('layout', 'form');
        parent::display();
    }

    /**
     * Saves an item and redirects based on task
     * @return void
     */
    function save()
    {
        $model  = $this->getModel( $this->get('suffix') );
        $row = $model->getTable();
        $row->load( array( 'user_id' => JFactory::getUser()->id ) );
        $row->bind( $_POST );
        $row->user_id = JFactory::getUser()->id;

        if ( $row->save() )
        {
            $model->clearCache();
            $model->setId( $row->user_id );
            $this->messagetype  = 'message';
            $this->message      = JText::_('COM_CITRUSCART_SAVED');

            
            JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
        }

        $redirect = "index.php?option=com_citruscart";
        $task = JFactory::getApplication()->input->getString('task');
        switch ($task)
        {
            case "save":
            default:
                $redirect .= "&view=".$this->get('suffix');
              break;
        }

        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
}