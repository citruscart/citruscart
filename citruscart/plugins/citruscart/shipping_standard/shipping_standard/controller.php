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

//JLoader::import( 'com_citruscart.library.plugins.shippingcontroller', JPATH_ADMINISTRATOR.'/components' );
Citruscart::load( 'CitruscartControllerShippingPlugin', 'library.plugins.shippingcontroller' );

class CitruscartControllerShippingStandard extends CitruscartControllerShippingPlugin
{

	var $_element   = 'shipping_standard';

	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6+ code
			JModelLegacy::addIncludePath(JPATH_SITE.'/plugins/Citruscart/shipping_standard/shipping_standard/models');
			JTable::addIncludePath(JPATH_SITE.'/plugins/Citruscart/shipping_standard/shipping_standard/tables');
		}
		else {
			JModelLegacy::addIncludePath(JPATH_SITE.'/plugins/Citruscart/shipping_standard/models');
			JTable::addIncludePath(JPATH_SITE.'/plugins/Citruscart/shipping_standard/tables');
		}
		$this->registerTask( 'newMethod', 'newMethod' );
	}

	/**
	 * Gets the plugin's namespace for state variables
	 * @return string
	 */
	function getNamespace()
	{
		$app = JFactory::getApplication();
		$ns = $app->getName().'::'.'com.citruscart.plugin.shipping.standard';
		return $ns;
	}

	function newMethod(){
		return $this->view();
	}

	function save(){

		$input = JFactory::getApplication()->input;

		$values = $input->getArray($_POST);

		$this->includeCustomTables();
		$table = JTable::getInstance('ShippingMethods', 'CitruscartTable');

		$table->bind($values);

		$success =  $table->store($values);
		if($success){
			$this->messagetype 	= 'message';
			$this->message  	= JText::_('COM_CITRUSCART_SAVED');
		}
		else{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_('COM_CITRUSCART_SAVE_FAILED')." - ".$row->getError();
		}

		$redirect = $this->baseLink();

		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	function setRates()
	{
		$input = JFactory::getApplication()->input;
		Citruscart::load( 'CitruscartGrid', 'library.grid' );
		Citruscart::load( 'CitruscartSelect', 'library.select' );
		$this->includeCustomModel('ShippingRates');
		$sid = $input->get('sid');

		$this->includeCustomTables();
		$row = JTable::getInstance('ShippingMethods', 'CitruscartTable');
		$row->load($sid);

		$model = JModelLegacy::getInstance('ShippingRates', 'CitruscartModel');
		$model->setState('filter_shippingmethod', $sid);
		$app = JFactory::getApplication();
		$ns = $this->getNamespace();
		$state = array();
		$state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
		$state['direction'] = $app->getUserStateFromRequest($ns.'direction', 'direction', 'ASC', '');
		$state['order'] = $app->getUserStateFromRequest($ns.'order', 'order',0, 'int');
		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		$items = $model->getList();

		//form
		$form = array();
		$form['action'] = $this->baseLink();

		// view
		$view = $this->getView( 'ShippingMethods', 'html' );
		$view->hidemenu = true;
		$view->hidestats = true;
		$view->setTask(true);
		$view->setModel( $model, true );
		$view->assign('row', $row);
		$view->assign('state', $model->getState());
		$view->assign('items', $items);
		$view->assign('form2', $form);
		$view->assign('baseLink', $this->baseLink());
		$view->setLayout('setrates');
		$view->display();
	}

	function cancel(){
		$redirect = $this->baseLink();
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, '', '' );
	}

	function view($cachable = false, $urlparams = false)
	{
		$input = JFactory::getApplication()->input;
		JLoader::import( 'com_citruscart.library.button', JPATH_ADMINISTRATOR.'/components' );
		CitruscartToolBarHelper::_custom( 'save', 'save', 'save', 'COM_CITRUSCART_SAVE', false, 'shippingTask' );
		CitruscartToolBarHelper::_custom( 'cancel', 'cancel', 'cancel', 'COM_CITRUSCART_CLOSE', false, 'shippingTask' );

		$id = $input->getInt('id', 0);
		$sid = CitruscartShippingPlugin::getShippingId();
		$this->includeCustomModel('ShippingMethods');

		$model = JModelLegacy::getInstance('ShippingMethods', 'CitruscartModel');
		$model->setId((int)$sid);

		$item = $model->getItem();

		// Form
		$form = array();
		$form['action'] = $this->baseLink();
		$form['shippingTask'] = 'save';
		//We are calling a view from the ShippingMethods we isn't actually the same  controller this has, however since all it does is extend the base view it is
		// all good, and we don't need to remake getView()
		$view = $this->getView( 'ShippingMethods', 'html' );
		$view->hidemenu = true;
		$view->hidestats = true;
		$view->setTask(true);
		$view->setModel( $model, true );
		$view->assign('item', $item);
		$view->assign('form2', $form);
		$view->setLayout('view');
		$view->display();

	}

	/**
	 * Deletes a shipping method
	 * @see Citruscart/admin/library/plugins/CitruscartControllerShippingPlugin::delete()
	 */
	function delete()
	{
		$input =JFactory::getApplication()->input;
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';

		$model = $this->getModel('shippingmethods');
		$row = JTable::getInstance('ShippingMethods', 'CitruscartTable');

		$cids =$input->get('cid', array (0), 'request', 'array');
		foreach ($cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('COM_CITRUSCART_ITEMS_DELETED');
		}

		$this->redirect = $this->baseLink();
		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

	/**
	 * Creates a shipping rate and redirects
	 *
	 * @return unknown_type
	 */
	function createrate()
	{
		$this->includeCustomModel('shippingrates');
		$this->includeCustomTables();

		$this->set('suffix', 'shippingrates');
		$model  = $this->getModel( $this->get('suffix') );

		$row = $model->getTable();
		$row->bind(JFactory::getApplication()->input->getArray($_POST));
		if ( $row->save() )
		{

			JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SAssVE_FAILED')." - ".$row->getError();
		}

		$redirect = $this->baseLink()."&shippingTask=setrates&sid={$row->shipping_method_id}&tmpl=component";
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
		$input = JFactory::getApplication()->input;
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$this->includeCustomModel('ShippingRates');
		$this->includeCustomTables();
		$model = $this->getModel('shippingrates');
		$row = $model->getTable();

		$cids = $input->get('cid', array(0), 'request', 'array');
		$geozones = $input->get('geozone', array(0), 'request', 'array');
		$groups =$input->get('groups', array(0), 'request', 'array');
		$prices = $input->get('price', array(0), 'request', 'array');
		$weight_starts = $input->get('weight_start', array(0), 'request', 'array');
		$weight_ends = $input->get('weight_end', array(0), 'request', 'array');
		$handlings =$input->get('handling', array(0), 'request', 'array');

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->geozone_id = $geozones[$cid];
			$row->shipping_rate_price = $prices[$cid];
			$row->shipping_rate_weight_start = $weight_starts[$cid];
			$row->shipping_rate_weight_end = $weight_ends[$cid];
			$row->shipping_rate_handling = $handlings[$cid];
			$row->group_id = $groups[$cid];

			if (!$row->save())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = $this->baseLink()."&shippingTask=setrates&sid={$row->shipping_method_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Deletes a shipping rate and redirects
	 *
	 * @return unknown_type
	 */
	function deleterate()
	{
		$input = JFactory::getApplication()->input;
		$this->set('suffix', 'shippingrates');
		$model  = $this->getModel( $this->get('suffix') );

		$cids = $input->get('cid', array(0), 'request', 'array');

		foreach ($cids as $cid)
		{
			$row = $model->getTable();
			$row->load( $cid );

			if (!$row->delete())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = $this->baseLink()."&shippingTask=setrates&sid={$row->shipping_method_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}