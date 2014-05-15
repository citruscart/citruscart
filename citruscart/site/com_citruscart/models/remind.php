<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');


class CitruscartModelRemind extends JModel
{
	/**
	 * Registry namespace prefix
	 *
	 * @var	string
	 */
	var $_namespace	= 'com_citruscart.remind.';

	/**
	 * Takes a user supplied e-mail address, looks
	 * it up in the database to find the username
	 * and then e-mails the username to the e-mail
	 * address given.
	 *
	 * @param	string	E-mail address
	 * @return	bool	True on success/false on failure
	 */
	function remindUsername($email)
	{
		jimport('joomla.mail.helper');

		global $mainframe;

		// Validate the e-mail address
		if (!JMailHelper::isEmailAddress($email))
		{
			$this->setError(JText::_('COM_CITRUSCART_INVALID_EMAIL_ADDRESS'));
			return false;
		}

		$db = JFactory::getDBO();
		$db->setQuery('SELECT username FROM #__users WHERE email = '.$db->Quote($email), 0, 1);

		// Get the username
		if (!($username = $db->loadResult()))
		{
			$this->setError(JText::_('COM_CITRUSCART_COULD_NOT_FIND_EMAIL'));
			return false;
		}

		// Push the email address into the session
		$mainframe->setUserState($this->_namespace.'email', $email);

		// Send the reminder email
		if (!$this->_sendReminderMail($email, $username))
		{
			return false;
		}

		return true;
	}

	/**
	 * Sends a username reminder to the e-mail address
	 * specified containing the specified username.
	 * @param	string	A user's e-mail address
	 * @param	string	A user's username
	 * @return	bool	True on success/false on failure
	 */
	function _sendReminderMail($email, $username)
	{
		$config		= JFactory::getConfig();
		$uri		= JFactory::getURI();
		$url		= $uri->__toString( array('scheme', 'host', 'port')).JRoute::_('index.php?option=com_user&view=login', false);

		$from		= $config->getValue('mailfrom');
		$fromname	= $config->getValue('fromname');
		$subject	= JText::sprintf('COM_CITRUSCART_CITRUSCART_USER_EMAIL_REMINDER', $config->getValue('sitename'));
		$body		= JText::sprintf('COM_CITRUSCART_USERNAME_REMINDER_EMAIL_TEXT', $config->getValue('sitename'), $username, $url);

		if (!JMail::sendMail($from, $fromname, $email, $subject, $body))
		{
			$this->setError('COM_CITRUSCART_ERROR_SENDING_REMINDER_EMAIL');
			return false;
		}

		return true;
	}
}
