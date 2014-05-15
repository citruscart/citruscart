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

jimport('joomla.html.toolbar.button');

class DSCButton extends JToolbarButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'DSC';

	function fetchButton( $type='DSC', $name = '', $text = '', $task = '', $list = true, $hideMenu = false, $taskName = 'shippingTask' )
	{
		$i18n_text	= JText::_($text);
		$class	= $this->fetchIconClass($name);
		$doTask	= $this->_getCommand($text, $task, $list, $hideMenu, $taskName);
		if($class=='icon-new') {
			$btn_class='btn btn-small btn-success';
		} else {
			$btn_class='btn btn-small';
		}
		$html = '';
		$html	.= "<button href=\"#\" onclick=\"$doTask\" class=\"toolbar $btn_class\">\n";
		$html	.="<i class='$class icon-white'></i>";
		$html	.= "$i18n_text\n";
		$html	.= "</button>\n";

		return $html;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access	private
	 * @param	string	$name	The task name as seen by the user
	 * @param	string	$task	The task used by the application
	 * @param	???		$list
	 * @param	boolean	$hide
	 * @param	string	$taskName	the task field name
	 * @return	string	JavaScript command string
	 * @since	1.5
	 */
	/* function _getCommand($name, $task, $list, $hide, $taskName)
	{
		$todo		= JString::strtolower(JText::_( $name ));
		$message	= JText::sprintf( 'Please make a selection from the list to', $todo );
		$message	= addslashes($message);

		if ($list) {
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{ submitDSCbutton('$task', '$taskName')}";
		} else {
			$cmd = "javascript:$hidecode submitDSCbutton('$task', '$taskName')";
		}


		return $cmd;
	} */

	/**
	 * Get the button CSS Id
	 *
	 * @access	public
	 * @return	string	Button CSS Id
	 */

	function fetchId( $type='Confirm', $name = '', $text = '', $task = '', $list = true, $hideMenu = false )
	{
		return $this->_name.'-'.$name;
	}

	function _getCommand($name, $task, $list, $hide, $taskName)
	{
		$todo		= JString::strtolower(JText::_( $name ));
		$message	= JText::sprintf( 'COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO', $todo );
		$message	= addslashes($message);

		if ($list) {
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{ submitCitruscartButton('$task', '$taskName')}";
		} else {
			$cmd = "javascript:submitCitruscartButton('$task', '$taskName')";
		}


		return $cmd;
	}


}