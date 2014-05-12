<?php

defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root().'/media/citruscart/css/menu.css');
//JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/');

require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar.php');
$toolbar = new CitruscartToolBar();
$toolbar->renderLinkbar();

?>
