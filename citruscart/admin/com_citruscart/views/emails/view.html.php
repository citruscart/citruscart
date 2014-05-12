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

class CitruscartViewEmails extends CitruscartViewBase
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

	/**
	 * The default toolbar for a list
	 * @return unknown_type
	 */
	function _defaultToolbar()
	{

	}

	function _formToolbar( $isNew = false )
	{
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');


		JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
	}


}
