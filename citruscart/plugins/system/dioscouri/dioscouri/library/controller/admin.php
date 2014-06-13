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

jimport('joomla.application.component.controller');
require_once(JPATH_SITE.'/libraries/dioscouri/library/controller.php');

class DSCControllerAdmin extends DSCController
{

	function __construct($config=array())
	{
		// Set a base path for use by the controller
		if (array_key_exists('base_path', $config)) {
			$this->_basePath	= $config['base_path'];
		} else {
			$this->_basePath	= JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);

		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'new', 'edit' );
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'savenew', 'save' );
		$this->registerTask( 'remove', 'delete' );
		$this->registerTask( 'publish', 'enable' );
		$this->registerTask( 'unpublish', 'enable' );
		$this->registerTask( 'disable', 'enable' );
		$this->registerTask( 'saveorder', 'ordering' );
		$this->registerTask( 'prev', 'jump' );
		$this->registerTask( 'next', 'jump' );
		$this->registerTask( 'saveprev', 'save' );
		$this->registerTask( 'savenext', 'save' );
		$this->registerTask( 'page_tooltip_enable', 'pagetooltip_switch' );
		$this->registerTask( 'page_tooltip_disable', 'pagetooltip_switch' );
		$this->registerTask( 'save_as', 'save' );
		$this->registerTask( 'published.enable', 'boolean' );
		$this->registerTask( 'published.disable', 'boolean' );

	}

	/**
	 * Hides a tooltip message
	 * Admin-only task
	 *
	 * @return unknown_type
	 */
	function pagetooltip_switch()
	{
		// get the application object
		$input = JFactory::getApplication()->input;
		$msg = new stdClass();
		$msg->type 		= '';
		$msg->message 	= '';
		$option = $this->get('com');
		$view = $input->getString('view');
		//$view = JRequest::getVar('view');
		$msg->link 		= 'index.php?option='.$option.'&view='.$view;
		$app = str_replace("com_", "", $option);

		//$key = JRequest::getVar('key');
		$key = $input->getString('key');
		$constant = 'page_tooltip_'.$key;
		$config_title = $constant."_disabled";

		$database = JFactory::getDbo();
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/'.$option.'/tables/' );
		unset($table);
		$table = JTable::getInstance( 'config', $app.'Table' );
		$table->load( array('config_name'=>$config_title) );
		$table->config_name = $config_title;
		$table->value = '1';

		if (!$table->save())
		{
			$msg->message = JText::_('Error') . ": " . $table->getError();
		}

		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * Displays an item
	 *
	 *  Child controllers should check that user can actually view the item they're attempting to display
	 */
	public function view($cachable=false, $urlparams = false)
	{
		$this->displayView($cachable, $urlparams);
	}

	/**
	 * Displays a standard editing form
	 *
	 *  Child controllers should check that user can actually edit the item they're attempting to edit
	 */
	public function edit($cachable=false, $urlparams = false)
	{
		$this->displayEdit($cachable, $urlparams);
	}

	/**
	 * Saves an item and redirects based on task
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function save()
	{

		return $this->doSave();
	}

	/**
	 * Deletes an item and redirects based on task
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function delete()
	{
		return $this->doDelete();
	}

	/**
	 * Orders an item
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function order()
	{
		return $this->doOrder();
	}

	/**
	 * Orders a list of items
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function ordering()
	{
		return $this->doOrdering();
	}

	/**
	 * Changes a boolean field
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function boolean()
	{
		return $this->doBoolean();
	}

	/**
	 * Changes a boolean field, is a wrapper for boolean
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function enable()
	{
		return $this->doEnable();
	}

	/**
	 * Displays a neighboring item in a list
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 * or
	 * (j1.5) JRequest::checkToken() or die( 'Invalid Token' );
	 *
	 * @return boolean
	 */
	public function jump()
	{
		return $this->doJump();
	}

}

