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

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewCurrencies extends CitruscartViewBase
{
	/**
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null)
	{
		$app = JFactory::getApplication();
		$layout = $this->getLayout();
		$this->renderSubmenu();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			  break;
			case "form":
				$app->input->set('hidemainmenu', '1');
				$this->_form($tpl);
			  break;
			case "default":
			default:
				$this->set( 'leftMenu', 'leftmenu_localization' );
				$this->_default($tpl);
			  break;
		}
	}

	function _defaultToolbar()
	{
        JToolBarHelper::publishList( 'currency_enabled.enable' );
        JToolBarHelper::unpublishList( 'currency_enabled.disable' );
		JToolBarHelper::divider();

		require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar.php');
		$toolbar = new CitruscartToolBar();
		Citruscart::load('CitruscartToolbar','helpers.toolbar.php');
		$toolbar->renderLinkbar();

		parent::_defaultToolbar();
	}

	function _form($tpl=null){
		parent::_form($tpl);

		$model = $this->getModel();


		$this->row = $model->getItem();

		if (empty($this->row->currency_id))
		{
			// this is a new product
			$item = JTable::getInstance('Currencies', 'CitruscartTable');
			//$item->manufacturer_params = new DSCParameter( $item->manufacturer_params );
			$this->assign( 'row', $item );
		}


	}
}
