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


class CitruscartModelReset extends JModel
{
	/**
	 * Registry namespace prefix
	 *
	 * @var	string
	 */
	var $_namespace	= 'com_citruscart.reset.';

	/**
	 * Verifies the validity of a username/e-mail address
	 * combination and creates a token to verify the request
	 * was initiated by the account owner.  The token is
	 * sent to the account owner by e-mail
	 *
	 * @since	1.5
	 * @param	string	Username string
	 * @param	string	E-mail address
	 * @return	bool	True on success/false on failure
	 */
	function requestReset($email)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');

		$db = JFactory::getDBO();

		// Make sure the e-mail address is valid
		if (!JMailHelper::isEmailAddress($email))
		{
			$this->setError(JText::_('COM_CITRUSCART_EMAIL_ADDRESS_IS_INVALID'));
			return false;
		}

		// Build a query to find the user
		$query	= 'SELECT id FROM #__users'
				. ' WHERE email = '.$db->Quote($email)
				. ' AND block = 0';

		$db->setQuery($query);

		// Check the results
		if (!($id = $db->loadResult()))
		{
			$this->setError(JText::_('COM_CITRUSCART_COULD_NOT_FIND_USER'));
			return false;
		}

		// Generate a new token
		$token = JApplication::getHash(JUserHelper::genRandomPassword());

		$query	= 'UPDATE #__users'
				. ' SET activation = '.$db->Quote($token)
				. ' WHERE id = '.(int) $id
				. ' AND block = 0';

		$db->setQuery($query);

		// Save the token
		if (!$db->query())
		{
			$this->setError(JText::_('COM_CITRUSCART_DATABASE_ERROR'));
			return false;
		}

		// Send the token to the user via e-mail
		if (!$this->_sendConfirmationMail($email, $token))
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks a user supplied token for validity
	 * If the token is valid, it pushes the token
	 * and user id into the session for security checks.
	 *
	 * @since	1.5
	 * @param	token	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function confirmReset($token)
	{
		global $mainframe;

		if(strlen($token) != 32) {
			$this->setError(JText::_('COM_CITRUSCART_INVALID_TOKEN'));
			return false;
		}

		$db	= JFactory::getDBO();
		$db->setQuery('SELECT id FROM #__users WHERE block = 0 AND activation = '.$db->Quote($token));

		// Verify the token
		if (!($id = $db->loadResult()))
		{
			$this->setError(JText::_('COM_CITRUSCART_INVALID_TOKEN'));
			return false;
		}

		// Push the token and user id into the session
		$mainframe->setUserState($this->_namespace.'token',	$token);
		$mainframe->setUserState($this->_namespace.'id',	$id);

		return true;
	}

	/**
	 * Takes the new password and saves it to the database.
	 * It will only save the password if the user has the
	 * correct user id and token stored in her session.
	 *
	 * @since	1.5
	 * @param	string	New Password
	 * @param	string	New Password
	 * @return	bool	True on success/false on failure
	 */
	function completeReset($password1, $password2)
	{
		jimport('joomla.user.helper');

		global $mainframe;

		// Make sure that we have a pasword
		if ( ! $password1 )
		{
			$this->setError(JText::_('COM_CITRUSCART_MUST_SUPPLY_PASSWORD'));
			return false;
		}

		// Verify that the passwords match
		if ($password1 != $password2)
		{
			$this->setError(JText::_('COM_CITRUSCART_PASSWORDS_DO_NOT_MATCH_LOW'));
			return false;
		}

		// Get the necessary variables
		$db			= JFactory::getDBO();
		$id			= $mainframe->getUserState($this->_namespace.'id');
		$token		= $mainframe->getUserState($this->_namespace.'token');
		$salt		= JUserHelper::genRandomPassword(32);
		$crypt		= JUserHelper::getCryptedPassword($password1, $salt);
		$password	= $crypt.':'.$salt;

		// Get the user object
		$user = new JUser($id);

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin('user');
		
		JFactory::getApplication()->triggerEvent('onBeforeStoreUser', array($user->getProperties(), false));

		// Build the query
		$query 	= 'UPDATE #__users'
				. ' SET password = '.$db->Quote($password)
				. ' , activation = ""'
				. ' WHERE id = '.(int) $id
				. ' AND activation = '.$db->Quote($token)
				. ' AND block = 0';

		$db->setQuery($query);

		// Save the password
		if (!$result = $db->query())
		{
			$this->setError(JText::_('COM_CITRUSCART_DATABASE_ERROR'));
			return false;
		}

		// Update the user object with the new values.
		$user->password			= $password;
		$user->activation		= '';
		$user->password_clear	= $password1;

		// Fire the onAfterStoreUser trigger
		JFactory::getApplication()->triggerEvent('onAfterStoreUser', array($user->getProperties(), false, $result, $this->getError()));

		// Flush the variables from the session
		$mainframe->setUserState($this->_namespace.'id',	null);
		$mainframe->setUserState($this->_namespace.'token',	null);

		return true;
	}

	/**
	 * Sends a password reset request confirmation to the
	 * specified e-mail address with the specified token.
	 *
	 * @since	1.5
	 * @param	string	An e-mail address
	 * @param	string	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function _sendConfirmationMail($email, $token)
	{		
		$config		= JFactory::getConfig();
		$uri		= JFactory::getURI();
		$url		= JURI::base().'index.php?option=com_citruscart&view=reset&layout=confirm';
		$sitename	= $config->getValue('sitename');

		// Set the e-mail parameters
		$from		= $config->getValue('mailfrom');
		$fromname	= $config->getValue('fromname');
		$subject	= JText::sprintf('COM_CITRUSCART_CITRUSCART_PASSWORD_RESET', $sitename);
		$body		= JText::sprintf('COM_CITRUSCART_PASSWORD_RESET_CONFIRMATION_EMAIL_TEXT', $sitename, $token, $url);

		// Send the e-mail
		if (!JMail::sendMail($from, $fromname, $email, $subject, $body))
		{
			$this->setError('COM_CITRUSCART_ERROR_SENDING_CONFIRMATION_EMAIL');
			return false;
		}

		return true;
	}
}
