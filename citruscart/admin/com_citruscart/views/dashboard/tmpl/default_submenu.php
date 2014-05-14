<?php

defined('_JEXEC') or die('Restricted access');
JHtml::_('stylesheet', 'menu.css', 'media/citruscart/css/menu.css');

require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar.php');
$toolbar = new CitruscartToolBar();

Citruscart::load('CitruscartToolbar','helpers.toolbar.php');

$toolbar->renderLinkbar();




