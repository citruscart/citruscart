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

class CitruscartControllerPayment extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'enabled.enable', 'boolean' );
		$this->registerTask( 'enabled.disable', 'boolean' );
		$this->set('suffix', 'payment');
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
        $state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

    /**
     * saves the editing in payment plugin
     */
    function save()
    {

	   	$app = JFactory::getApplication();
        $model  = $this->getModel( $this->get('suffix') );

		if(version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
	        $row  = JTable::getInstance('extension');
	    } else {
	        // Joomla! 1.5 code here
	       $row  = JTable::getInstance('plugin');
	    }
		$post = $app->input->getArray($_POST);

	    $row->bind( $post);


       	$task = $app->input->getString( 'task' );

	      if ($task == "save"){
            $pk = $row->getKeyName();
            $row->$pk = 0;
        }

        if ( $row->store() )
        {
            $model->setId( $row->extension_id );
            $model->clearCache();
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

        switch ($task)
        {
            case "saveprev":
                $redirect .= '&view='.$this->get('suffix');
                // get prev in list
                $model->emptyState();
                $this->_setModelState();
                $surrounding = $model->getSurrounding( $model->getId() );
                if (!empty($surrounding['prev']))
                {
                    $redirect .= '&task=edit&id='.$surrounding['prev'];
                }
                break;
            case "savenext":
                $redirect .= '&view='.$this->get('suffix');
                // get next in list
                $model->emptyState();
                $this->_setModelState();
                $surrounding = $model->getSurrounding( $model->getId() );
                if (!empty($surrounding['next']))
                {
                    $redirect .= '&task=edit&id='.$surrounding['next'];
                }
                break;

            case "savenew":
                $redirect .= '&view='.$this->get('suffix').'&task=add';
                break;
            case "apply":
                $redirect .= '&view='.$this->get('suffix').'&task=edit&id='.$model->getId();
                break;
            case "save":
            default:
                $redirect .= "&view=".$this->get('suffix');
                break;
        }
        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
}


