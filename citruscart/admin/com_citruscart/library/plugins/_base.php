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

// TODO Make all Citruscart plugins extend this _base file, to reduce code redundancy
define('DS' ,'/' );
/** Import library dependencies */
jimport('joomla.plugin.plugin');
jimport('joomla.utilities.string');
require_once(JPATH_SITE.'/libraries/dioscouri/library/plugin.php');
class CitruscartPluginBase extends DSCPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	protected $_element = '';

	var $_log_file = '';

	/**
	 * Gets the row from the __plugins DB table that corresponds to this plugin
	 *
	 * @return object
	 */
	protected function _getMe() {
		if (empty($this -> _row)) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_citruscart' . DS . 'tables');
			$table = JTable::getInstance('Shipping', 'CitruscartTable');
			$table -> load(array('element' => $this -> _element, 'folder' => 'Citruscart'));
			$this -> _row = $table;
		}
		return $this -> _row;
	}

	/**
	 * Make the standard Citruscart Tables avaiable in the plugin
	 */
	protected function includeCitruscartTables() {
		// Include Citruscart Tables Classes
		JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_citruscart' . DS . 'tables');
	}

	/**
	 * Include a particular Citruscart Model
	 * @param $name the name of the mode (ex: products)
	 */
	protected function includeCitruscartModel($name) {
		if (strtolower($name) != 'base')
			Citruscart::load('CitruscartModel' . ucfirst(strtolower($name)), 'models.' . strtolower($name));
		else
			Citruscart::load('CitruscartModelBase', 'models._base');
	}

	/**
	 * Include a particular Custom Model
	 * @param $name the name of the model
	 * @param $plugin the name of the plugin in which the model is stored
	 * @param $group the group of the plugin
	 */
	protected function includeCustomModel($name, $plugin = '', $group = 'Citruscart') {
		if (empty($plugin)) {
			$plugin = $this -> _element;
		}

		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			// Joomla! 1.6+ code here
			if (!class_exists('CitruscartModel' . $name))
				JLoader::import('plugins.' . $group . '.' . $plugin . '.' . $plugin . '.models.' . strtolower($name), JPATH_SITE);
		} else {
			// Joomla! 1.5 code here
			if (!class_exists('CitruscartModel' . $name))
				JLoader::import('plugins.' . $group . '.' . $plugin . '.models.' . strtolower($name), JPATH_SITE);
		}

	}

	/**
	 * add a user-defined table to list of available tables (including the Citruscart tables
	 * @param $plugin the name of the plugin in which the table is stored
	 * @param $group the group of the plugin
	 */
	protected function includeCustomTables($plugin = '', $group = 'Citruscart') {

		if (empty($plugin)) {
			$plugin = $this -> _element;
		}

		$this -> includeCitruscartTables();
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			// Joomla! 1.6+ code here
			$customPath = JPATH_SITE . DS . 'plugins' . DS . $group . DS . $plugin . DS . $plugin . DS . 'tables';
		} else {
			// Joomla! 1.5 code here
		$customPath = JPATH_SITE . DS . 'plugins' . DS . $group . DS . $plugin . DS . 'tables';
		}

		JTable::addIncludePath($customPath);
	}

	/**
	 * Include a particular Custom View
	 * @param $name the name of the view
	 * @param $plugin the name of the plugin in which the view is stored
	 * @param $group the group of the plugin
	 */
	protected function includeCustomView($name, $plugin = '', $group = 'Citruscart') {
		if (empty($plugin)) {
			$plugin = $this -> _element;
		}
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			// Joomla! 1.6+ code here
			if (!class_exists('CitruscartView' . $name))
				JLoader::import('plugins.' . $group . '.' . $plugin . '.' . $plugin . '.views.' . strtolower($name), JPATH_SITE);

		} else {
			// Joomla! 1.5 code here
			if (!class_exists('CitruscartView' . $name))
				JLoader::import('plugins.' . $group . '.' . $plugin . '.views.' . strtolower($name), JPATH_SITE);

		}

	}

	protected function writeToLog($text) {
		static $first = true;
		jimport('joomla.filesystem.file');

		$dump = '';
		if (JFile::exists($this -> _log_file)) {
			$dump = file_get_contents($this -> _log_file);
		}
		if ($first) {
			$dump = "\n\n" . Date('d.n.Y - H:i.s ', time()) . $text;
			$first = false;
		} else {
			// Dump at the head of the file
			$dump = "\n\n" . Date('d.n.Y - H:i.s ', time()) . $text . $dump;
		}
		JFile::write($this -> _log_file, $dump);
	}

	public function clearLog() {
		if (JFile::exists($this -> _log_file)) {
			JFile::write($this -> _log_file, '');
		}
	}

	/**
	 * Override this to avoid overwriting of other constants
	 * (we have custom language file for that)
	 * @see JPlugin::loadLanguage()
	 */
	function loadLanguage($extension = '', $basePath = JPATH_BASE, $overwrite = false) {

		if (version_compare(JVERSION, '1.6.0', 'ge')) {

			if (empty($extension)) {
				$extension = 'plg_' . $this -> _type . '_' . $this -> _name;
			}

			$language = JFactory::getLanguage();
			$lang = $language -> getTag();

			$path = JLanguage::getLanguagePath($basePath, $lang);

			if (!strlen($extension)) {
				$extension = 'joomla';
			}
			$filename = ($extension == 'joomla') ? $lang : $lang . '.' . $extension;
			$filename = $path . '/' . $filename;

			$result = false;
			if (isset($language -> _paths[$extension][$filename])) {
				// Strings for this file have already been loaded
				$result = true;
			} else {
				// Load the language file
				$result = $language -> load($extension, $basePath, null, $overwrite);

				// Check if there was a problem with loading the file
				if ($result === false) {
					// No strings, which probably means that the language file does not exist
					$path = JLanguage::getLanguagePath($basePath, $language -> getDefault());
					$filename = ($extension == 'joomla') ? $language -> getDefault() : $language -> getDefault() . '.' . $extension;
					$filename = $path . DS . $filename . '.ini';

					//				$result = $language->load( $filename, $extension, $overwrite );
				}

			}

		} else {

			if (empty($extension)) {
				$extension = 'plg_' . $this -> _type . '_' . $this -> _name;
			}

			$language = JFactory::getLanguage();
			$lang = $language -> _lang;

			$path = JLanguage::getLanguagePath($basePath, $lang);

			if (!strlen($extension)) {
				$extension = 'joomla';
			}

			$filename = ($extension == 'joomla') ? $lang : $lang . '.' . $extension;
			$filename = $path . DS . $filename . '.ini';

			$result = false;
			if (isset($language -> _paths[$extension][$filename])) {
				// Strings for this file have already been loaded
				$result = true;
			} else {
				// Load the language file
				$result = $language -> _load($filename, $extension, $overwrite);

				// Check if there was a problem with loading the file
				if ($result === false) {
					// No strings, which probably means that the language file does not exist
					$path = JLanguage::getLanguagePath($basePath, $language -> _default);
					$filename = ($extension == 'joomla') ? $language -> _default : $language -> _default . '.' . $extension;
					$filename = $path . DS . $filename . '.ini';

					$result = $language -> _load($filename, $extension, $overwrite);
				}

			}

		}

		return $result;

	}

}
