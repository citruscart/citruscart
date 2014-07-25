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

class CitruscartControllerZonerelations extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'zonerelations');
	}

	/**
	 * Saves an item and redirects based on task
	 * @return void
	 */
	function save()
	{
		$input= JFactory::getApplication()->input;
		$model 	= $this->getModel( $this->get('suffix') );

	    $row = $model->getTable();
	    $row->load( $model->getId() );
		$row->bind( $_POST );
		$geozoneid = $row->geozone_id;

		if ( $row->save() )
		{
			$model->setId( $row->id );
			$model->clearCache();
			$this->messagetype 	= 'message';
			$this->message  	= JText::_('COM_CITRUSCART_SAVED');


			JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
			else
		{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

    	$redirect = "index.php?option=com_citruscart&tmpl=component&geozoneid=$geozoneid";
    	$task = $input->getString('task');
    	switch ($task)
    	{
    		case "savenew":
    			$redirect .= '&view='.$this->get('suffix').'&layout=form';
    		  break;
    		case "apply":
    			$redirect .= '&view='.$this->get('suffix').'&layout=form&id='.$model->getId();
    		  break;
    		case "save":
    		default:
    			$redirect .= "&task=configzones&view=".$this->get('suffix');
    		  break;
    	}

    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Cancels operation and redirects to default page
	 * If item is checked out, releases it
	 * @return void
	 */
	function cancel()
	{
		$model 	= $this->getModel( $this->get('suffix') );
	    $row = $model->getTable();
	    $row->load( $model->getId() );
		$geozoneid = $row->geozone_id;

		$this->redirect = "index.php?option=com_citruscart&task=configzones&tmpl=component&geozoneid=$geozoneid&view=".$this->get('suffix');
        parent::cancel();
	}

	/**
	 * Deletes record(s) and redirects to default layout
	 */
	function delete()
	{
		$app = JFactory::getApplication();
		$model 	= $this->getModel( $this->get('suffix') );
	    $row = $model->getTable();
	    $row->load( $model->getId() );
		$geozoneid = $row->geozone_id;

		$this->redirect = $app->input->getString( 'return' )
		  ? base64_decode( $app->input->getString( 'return' ) )
		  : "index.php?option=com_citruscart&task=configzones&tmpl=component&geozoneid=$geozoneid&view==".$this->get('suffix');
		$this->redirect = JRoute::_( $this->redirect, false );
        parent::delete();
	}

	/*
	 * Creates a popup where fields can be selected and associated with this category.
	 * Basically a reverse of the category popup on the fields screen
	 */
	function configzones()
    {
    	$this->set('suffix', 'zonerelations');
    	$state = parent::_setModelState();
    	$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();

      	$state['filter_typeid'] 	= $app->getUserStateFromRequest($ns.'typeid', 'filter_typeid', '', '');

      	$geozoneid = $app->input->get( 'geozoneid' );
		$state['filter_geozoneid'] = $geozoneid;

    	foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$id =$app->input->getInt( 'id',0);
		$row = $model->getTable( 'zonerelations' );
		$row->load( $id );

		$view	= $this->getView( 'zonerelations', 'html' );
		$view->set( '_controller', 'zonerelations' );
		$view->set( '_view', 'zonerelations' );
		$view->set( '_action', "index.php?option=com_citruscart&controller=zonerelations&task=configzones&tmpl=component&geozoneid=$geozoneid" );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->setTask(true);
		$view->display();
    }

}

