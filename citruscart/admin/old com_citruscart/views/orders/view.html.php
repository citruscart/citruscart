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



Citruscart::load('CitruscartViewBase', 'views._base');
Citruscart::load('CitruscartSelect', 'library.select');
Citruscart::load('CitruscartGrid', 'library.grid');
Citruscart::load('CitruscartUrl', 'library.url');

class CitruscartViewOrders extends CitruscartViewBase
{
	/**
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null)
	{
		$app = JFactory::getApplication();
		$view = $app->input->getString('view');
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "confirmdelete":
				JToolBarHelper::deleteList( 'COM_CITRUSCART_VALID_DELETE_ITEMS' );
				JToolBarHelper::cancel('close', 'COM_CITRUSCART_CLOSE');
				$validate = JSession::getFormToken();
				$form = array();
				$controller = strtolower($this->get('_controller', $app->input->getString('controller', $view)));
				$view = strtolower($this->get('_view', $view));
				$action = $this->get('_action', "index.php?option=com_citruscart&controller={$controller}&view={$view}");
				$form['action'] = $action;
				$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
				$this->assign('form', $form);
				break;
			case "print":

			case "view":
				$this->_form($tpl);
				break;
			case "form_addresses":
				$app->input->set('hidemainmenu', '1');
				$this->_formAddresses($tpl);
				break;
			case "form":
				$app->input->set('hidemainmenu', '1');
				$this->_form($tpl);
				break;
			case "batchedit":
				$app->input->set('hidemainmenu', '1');
				$this->_batchedit($tpl);
				break;
			case "default":

			default:
				$this->set('leftMenu', 'leftmenu_orders');
				$this->_default($tpl);
				break;
		}
	}

	/**
	 * The default toolbar for a list
	 * @return unknown_type
	 */
	function _defaultToolbar()
	{
		JToolBarHelper::custom('batchedit', "forward", "forward", 'COM_CITRUSCART_BATCH_EDIT', false);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('COM_CITRUSCART_VALID_DELETE_ITEMS');
		$class_name = 'new';
		$url = "index.php?option=com_citruscart&view=pos";
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('link', $class_name, 'COM_CITRUSCART_NEW', $url);



	}

	/**
	 * Process the data for the convert view
	 * @return void
	 **/
	function _batchedit($tpl=null)
	{
		$app = JFactory::getApplication();
		$view  =$app->input->getString('view');
		// Import necessary helpers + library files
		JLoader::import('com_citruscart.library.select', JPATH_ADMINISTRATOR.'/components');
		JLoader::import('com_citruscart.library.grid', JPATH_ADMINISTRATOR.'/components');
		JLoader::import('com_citruscart.library.url', JPATH_ADMINISTRATOR.'/components');
		$model = $this->getModel();

		// set the model state
		$this->assign('state', $model->getState());

		// page-navigation
		$this->assign('pagination', $model->getPagination());

		// list of items
		$items = $model->getList();
		$this->assign('items', $items);

		// set toolbar
		$this->_batcheditToolbar();

		// form
		$validate = JSession::getFormToken();
		$form = array();
		$controller = strtolower($this->get('_controller',$app->input->getString('controller', $view)));
		$action = $this->get('_action', "index.php?option=com_citruscart&controller={$controller}&view={$view}");
		$form['action'] = $action;
		$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
		$this->assign('form', $form);
	}

	function _batcheditToolbar()
	{
		$this->set('title', "Order Batch Edit");
		JToolBarHelper::save('updatebatch');
		JToolBarHelper::cancel();
	}

	function _formAddresses($tpl=null)
	{
		$app = JFactory::getApplication();

		$view = $app->input->getString('view');

		$model = $this->getModel();

		// set the model state
		$state = $model->getState();
		JFilterOutput::objectHTMLSafe($state);
		$this->assign('state', $state);

		// get the data
		// not using getItem here to enable ->checkout (which requires JTable object)
		$row = $model->getTable();
		$row->load((int)$model->getId());
		// TODO Check if the item is checked out and if so, setlayout to view

		$this->displayTitle('Edit Addresses');
		JToolBarHelper::save('saveAddresses');
		JToolBarHelper::cancel('closeEditAddresses', 'COM_CITRUSCART_CLOSE');

		// form
		$validate = JSession::getFormToken();
		$form = array();
		$controller = strtolower($this->get('_controller', $app->input->getString('controller',$view)));
		$view = strtolower($this->get('_view', $view));
		$action = $this->get('_action', "index.php?option=com_citruscart&controller={$controller}&view={$view}&layout=form&id=" . $model->getId());
		$form['action'] = $action;
		$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
		$form['id'] = $model->getId();
		$this->assign('form', $form);
		$this->assign('row', $model->getItem());
	}

}
