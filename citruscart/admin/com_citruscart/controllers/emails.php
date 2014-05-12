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

class CitruscartControllerEmails extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'emails');

	}

	/**
	 * Overriding the method because we use a slightly diff getItem in the emails model
	 * (non-PHPdoc)
	 * @see Citruscart/admin/CitruscartController::edit()
	 */
	function edit($cachable=false, $urlparams = false)
	{
		$app = JFactory::getApplication();

		$id = $app->input->get('id','en-GB');

        //$id = JRequest::getVar('id', 'en-GB');

        $view   = $this->getView( $this->get('suffix'), 'html' );
        $model  = $this->getModel( $this->get('suffix') );

        $model->setId( $id );
        $row = $model->getItem( $id, true );
		$app->input->set( 'hidemainmenu', 1);
        //JRequest::setVar( 'hidemainmenu', '1' );
        $view->setLayout( 'form' );
        $view->setModel( $model, true );
        $view->assign( 'row', $row );

        $model->emptyState();
        $this->_setModelState();
        $surrounding = $model->getSurrounding( $model->getId() );
        $view->assign( 'surrounding', $surrounding );
		$view->setTask(true);
        $view->display();
        $this->footer();
        return;
	}

	/**
	 * Save method is diff here because we're writing to a file
	 * (non-PHPdoc)
	 * @see Citruscart/admin/CitruscartController::save()
	 */
	function save()
	{
		$app = JFactory::getApplication();

		$id = $app->input->get('id','en-GB');

		$temp_values =$app->input->get('post', '4');

		$model = $this->getModel('Emails', 'CitruscartModel');

		// Filter values
		$prefix = $model->email_prefix;
		$values = array();
		foreach($temp_values as $k =>$v){
			if(stripos($k, $prefix) === 0)
				$values[$k] = $v;
		}


		$lang = $model->getItem( $id );
		$path = $lang->path;

		$msg = JText::_('COM_CITRUSCART_SAVED');

		jimport('joomla.filesystem.file');

		if (JFile::exists($path))
		{
			$original = new JRegistry();
			$original->loadFile($path);

			$registry = new JRegistry();
			$registry->loadArray($values);

			$original->merge($registry);

			$txt = $original->__toString('INI');

			$success = JFile::write($path, $txt);

			if(!$success)
				$msg = JText::_('COM_CITRUSCART_ERROR_SAVING_NEW_LANGUAGE_FILE');

		}

		$model->clearCache();

		//$task = JRequest::getVar('task');
		$task = $app->input->getString('task');
        $redirect = "index.php?option=com_citruscart";

        switch ($task)
        {
            case "apply":
                $redirect .= '&view='.$this->get('suffix').'&task=edit&id='.$id;
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


