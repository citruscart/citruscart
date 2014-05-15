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

require_once(JPATH_SITE.'/libraries/dioscouri/library/button16.php');

require_once(JPATH_SITE.'/libraries/dioscouri/library/toolbarhelper16.php');


class JToolbarButtonCitruscartButton extends DSCButton{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */

	public $_name = 'Citruscart';


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
	function _getCommand($name, $task, $list, $hide, $taskName)
	{
		$todo		= JString::strtolower(JText::_( $name ));
		$message	= JText::sprintf( 'COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO', $todo );
		$message	= addslashes($message);

		if ($list) {
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{ citruscartSubmitForm('$task', '$taskName')}";
		} else {
			$cmd = "javascript:citruscartSubmitForm('$task', '$taskName')";
		}


		return $cmd;
	}
}

class CitruscartToolBarHelper extends DSCToolBarHelper {

	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	//protected $_name = 'Citruscart';


	/* IN Joomla 1.5 STATIC::$_name Causes a fatal error, so extending directly here to avoid that.
	 * ALSO in JOOMLA 1.5 $_name is public so that causes fatal error too, so  for now I
	 *  */

	public static  function _custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false, $taskName = 'shippingTask')
	{


		$bar = JToolBar::getInstance('toolbar');

		//strip extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button
		$bar->appendButton( 'Citruscart', $icon, $alt, $task, $listSelect, $x, $taskName );
	}

	/**
	 * Writes the common 'new' icon for the button bar
	 * @param string An override for the task
	 * @param string An override for the alt text
	 * @since 1.0
	 */
	public static  function _addNew($task = 'add', $alt = 'New', $taskName = 'shippingTask')
	{
		$bar = JToolBar::getInstance('toolbar');
		// Add a new button
		$bar->appendButton( 'Citruscart', 'new', $alt, $task, false, false, $taskName );
	}


}