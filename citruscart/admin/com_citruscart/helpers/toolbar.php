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
// no direct access
defined('_JEXEC') or die('Restricted access');


if (version_compare(JVERSION, '3.0', 'ge'))
{
	require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar30.php');
	class CitruscartToolBar extends CitruscartToolBar30
	{

		public static function &getAnInstance($option = null, $config = array()) {

			if (!class_exists( $className )) {
				$className = 'CitruscartToolbar';
			}
			$instance = new $className($config);

			return $instance;

		}


		public function __construct($config = array()) {}

	}


		class JToolbarButtonCitruscart extends JToolbarButtonCitruscart30 {

		}

}
else
{
	require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar25.php');
	class CitruscartToolBar extends CitruscartToolBar25
	{

		public static function &getAnInstance($option = null, $config = array()) {

			if (!class_exists( $className )) {
				$className = 'CitruscartToolbar';
			}
			$instance = new $className($config);

			return $instance;

		}


		public function __construct($config = array()) {}
		}

		class JButtonCitruscart extends JToolbarButtonCitruscart25 {

		}

}
