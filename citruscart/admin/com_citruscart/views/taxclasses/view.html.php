<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewTaxclasses extends CitruscartViewBase
{
	/**
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null)
	{

		$layout = $this->getLayout();
		$this->renderSubmenu();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			  break;
			case "form":
				JFactory::getApplication()->set('hidemainmenu', '1');
				$this->_form($tpl);
			  break;
			case "default":
			default:
				$this->set( 'leftMenu', 'leftmenu_localization' );
				$this->_default($tpl);
			  break;
		}
	}

	function _default($tpl=null)
	{
		Citruscart::load( 'CitruscartUrl', 'library.url' );
		parent::_default($tpl);
	}

	/**
	 * Method to overwrite
	 * @param $tpl
	 * @see CitruscartViewBase::_form()
	 */
	function _form($tpl = null){
		$app = JFactory::getApplication();
		parent::_form($tpl);

		$model = $this->getModel();


		$this->row = $model->getItem();

		if (empty($this->row->tax_class_id))
		{
			// this is a new product
			$item = JTable::getInstance($app->input->getString('view'), 'CitruscartTable');
			$this->assign( 'row', $item );
		}
	}
}
