<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class CitruscartVersion {

public static function getPreviousVersion() {

	jimport('joomla.filesystem.file');
	$target = JPATH_ADMINISTRATOR.'/components/com_citruscart/pre-version.txt';
	if(JFile::exists($target)) {
		$rawData = JFile::read($target);
		$info = explode("\n", $rawData);
		$version = trim($info[0]);
	} else {
		//if no file is found then assume its latest
		$version = '2.6.7';
	}
	return $version;

}

/**
	 * Populates global constants holding the Akeeba version
	 */
	public static function load_version_defines()
	{
		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/version.php'))
		{
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/version.php');
		}

		if(!defined('Citruscart_VERSION')) define("Citruscart_VERSION", "svn");
		if(!defined('Citruscart_PRO')) define('Citruscart_PRO', false);
		if(!defined('Citruscart_DATE')) {
			jimport('joomla.utilities.date');
			$date = new JDate();
			define( "Citruscart_DATE", $date->format('Y-m-d') );
		}
		if(!defined('Citruscart_ATTRIBUTES_MIGRATED')) define('Citruscart_ATTRIBUTES_MIGRATED', false);
	}

}