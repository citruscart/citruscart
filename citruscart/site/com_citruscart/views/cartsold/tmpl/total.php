<?php
/*Layout for displaying refreshed total amount.*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'menu.css', 'media/citruscart/css/');
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
$state = @$this->state;
$order = @$this->order;
$items = @$this->orderitems;

echo $items

?>
