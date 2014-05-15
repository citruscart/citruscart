<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once(JPATH_SITE.'/libraries/dioscouri/library/controller/admin.php');

class CitruscartController extends DSCControllerAdmin
{
	/**
     * default view
     */
    public $default_view = 'dashboard';

	 /**
	 * @var array() instances of Models to be used by the controller
	 */
	public $_models = array();

	/**
	 * string url to perform a redirect with. Useful for child classes.
	 */
	protected $redirect;

	function __construct( $config=array() )
	{
	    parent::__construct( $config );

	    $this->defines = Citruscart::getInstance();
	}

	/**
	 * Hides a tooltip message
	 * @return unknown_type
	 */
	function pagetooltip_switch()
	{
		/* Get the application */
		$app = JFactory::getApplication();
		$msg = new stdClass();
		$msg->type 		= '';
		$msg->message 	= '';

		/* Get the view string */
		$view = $app->input->getString('view');
		//$view = JRequest::getVar('view');

		$msg->link 		= 'index.php?option=com_citruscart&view='.$view;

		/* Get the key string */
		$key = $app->input->getString('key');
		//$key = JRequest::getVar('key');

		$constant = 'page_tooltip_'.$key;
		$config_title = $constant."_disabled";

		$database = JFactory::getDBO();
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables/' );
		unset($table);
		$table = JTable::getInstance( 'config', 'CitruscartTable' );
		$table->load( array('config_name'=>$config_title) );
		$table->config_name = $config_title;
		$table->value = '1';
		
		if (!$table->save())
		{
			$msg->message = JText::_('COM_CITRUSCART_ERROR') . ": " . $table->getError();
		}

		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * For displaying a searchable list of products in a lightbox
	 * Usage:
	 */
	function elementProduct()
	{
		$model 	= $this->getModel( 'elementproduct' );
		$view	= $this->getView( 'elementproduct' );
		$view->setModel( $model, true );
		$view->display();
	}

	/**
	 * For displaying a searchable list of images in a lightbox
	 * Usage:
	 */
	function elementImage()
	{
		$model 	= $this->getModel( 'elementimage' );
		$view	= $this->getView( 'elementimage' );
		$view->setModel( $model, true );
		$view->display();
	}

}
