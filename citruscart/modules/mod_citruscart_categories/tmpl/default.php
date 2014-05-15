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

Citruscart::load( 'CitruscartSelect', 'library.select' );

$document = JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_categories/tmpl/citruscart_categories.css'); ?>

<ul id="citruscart_categories_mod">
<?php foreach ($items as $item) : ?>
	<?php if (($item->level)<$depthlevel) :?>
	<li class="level<?php echo $item->level?>">
		<a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&filter_category=".$item->category_id.$item->slug."&Itemid=".$item->itemid ); ?>"><?php echo $item->category_name; ?></a>
	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>