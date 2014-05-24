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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartSelect', 'library.select' );

$doc = JFactory::getDocument();
$doc->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_manufacturers/tmpl/citruscart_manufacturers.css'); ?>

<!-- citruscart manufacturers div starts -->
<div class="citruscart_manufacturer_mod">
<h5><?php echo JText::_( "COM_CITRUSCART_MANUFACTURERS" ); ?></h5>
                 
<ul id="citruscart_manufacturers_mod" class="unstyled nav nav-tab">
<?php foreach ($items as $item) :
?>
<li class="manufacturerslist level<?php echo $item->manufacturer_id?>">
	<!--  TODO  : Need to check whether field name is missing -->
	<!-- <li class="level<?php echo $item->level?>"> -->
		<a href="<?php echo CitruscartHelperRoute::manufacturer($item->manufacturer_id); ?>"><?php echo $item->manufacturer_name; ?></a>
	</li>
<?php endforeach; ?>
</ul>
</div><!-- citruscart manufacturers div ends -->

