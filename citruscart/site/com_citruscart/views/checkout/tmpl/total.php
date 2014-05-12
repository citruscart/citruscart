<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

/*Layout for displaying refreshed total amount.*/
JHTML::_('stylesheet', 'menu.css', 'media/citruscart/css/');
JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
$state = $this->state;
$order = $this->order;
$items = $this->orderitems;

echo CitruscartHelperBase::currency( $items );
