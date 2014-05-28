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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @package		Joomla
 * @subpackage	User
 * @since		1.5
 */
class UserViewReset extends JView
{
	/**
	 * Registry namespace prefix
	 *
	 * @var	string
	 */
	var $_namespace	= 'com_user.reset.';

	/**
	 * Display function
	 *
	 * @since 1.5
	 */
	function display($tpl = null)
	{
		jimport('joomla.html.html');

		global $mainframe;

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		// Add the tooltip behavior
		JHTML::_('behavior.tooltip');

		// Get the layout
		$layout	= $this->getLayout();

		if ($layout == 'complete')
		{
			$id		= $mainframe->getUserState($this->_namespace.'id');
			$token	= $mainframe->getUserState($this->_namespace.'token');

			if (is_null($id) || is_null($token))
			{
				$mainframe->redirect('index.php?option=com_user&view=reset');
			}
		}

		// Get the page/component configuration
		$params = &$mainframe->getParams();

		$menus	= JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new DSCParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_('COM_CITRUSCART_FORGOT_YOUR_PASSWORD'));
			}
		} else {
			$params->set('page_title',	JText::_('COM_CITRUSCART_FORGOT_YOUR_PASSWORD'));
		}
		$document	= JFactory::getDocument();
		$document->setTitle( $params->get( 'page_title' ) );

		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
}
