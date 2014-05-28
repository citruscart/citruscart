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

//CitruscartHelperSubscription::
jimport('joomla.plugin.plugin');
jimport('joomla.application.component.model');

class plgSystemCitruscart_Subscriptions extends JPlugin {
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	var $_element = 'citruscart_subscriptions';

	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
		/*$language = JFactory::getLanguage();
		$language -> load('plg_citruscart_system' . $this -> _element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_citruscart_system' . $this -> _element, JPATH_ADMINISTRATOR, null, true);*/
	}

	/**
	 * Checks the extension is installed
	 *
	 * @return boolean
	 */
	function isInstalled() {
		$success = false;

		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_citruscart' .DIRECTORY_SEPARATOR. 'defines.php')) {
			$success = true;
			// Check the registry to see if our Citruscart class has been overridden
			if (!class_exists('Citruscart')) {
				JLoader::register("Citruscart", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR. "com_citruscart" .DIRECTORY_SEPARATOR. "defines.php");
				JLoader::register("Citruscart", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR. "com_citruscart" .DIRECTORY_SEPARATOR. "defines.php");
			}
		}
		return $success;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function onAfterInitialise() {
		$success = null;
		if (!$this -> isInstalled()) {
			return $success;
		}

		if (!$this -> canRun()) {
			return $success;
		}

		Citruscart::load('CitruscartHelperSubscription', 'helpers.subscription');
		$helper = new CitruscartHelperSubscription();
		$helper -> checkExpired();
		$helper -> checkExpiring();

		return $success;
	}

	/**
	 * Checks params and lastchecked to see if function should run again today
	 *
	 * @return unknown_type
	 */
	function canRun() {
		$success = false;

		// Use config to store & retrieve lastchecked from the __config table
		$config = Citruscart::getInstance();
		$lastchecked = $config -> get('subscriptions_last_checked');
		$date = JFactory::getDate();
		$today = $date -> format("%Y-%m-%d 00:00:00");

		if ($lastchecked < $today) {
			if (JFactory::getApplication() -> isAdmin() && !empty(JFactory::getUser() -> id)) {
				JError::raiseNotice('plgSystemCitruscart_Subscriptions::canRun', sprintf(JText::_('COM_CITRUSCART_CITRUSCART_MSG_SENDING_SUBSCRIPTION_EMAIL_NOTICES'), $lastchecked, $today));
			}
			$success = true;
		}

		return $success;
	}

}
