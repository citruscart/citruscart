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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// if DSC is not loaded all is lost anyway
/*if (!defined('_DSC')) { return; } */

 $text = $params->get( 'text', 'Citruscart Dashboard' );

$doc = JFactory::getDocument();

// Check the registry to see if our Citruscart class has been overridden
/*if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );
  */
$img = Citruscart::getURL()."images/citruscart.png";

require JModuleHelper::getLayoutPath('mod_citruscart_quickicon', $params->get('layout', 'default'));
