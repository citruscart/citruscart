<?php
/*Layout for displaying refreshed total amount.*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/');
JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/');
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
$state = @$this->state;
$order = @$this->order;
$items = @$this->orderitems;

echo $items

?>
