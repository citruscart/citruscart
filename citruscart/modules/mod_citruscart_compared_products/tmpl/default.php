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
 ?>
<?php
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JUri::root().'modules/mod_citruscart_compared_products/tmpl/citruscart_compared_products.css');
	//JHTML::_('stylesheet', 'citruscart_compared_products.css', 'modules/mod_citruscart_compared_products/tmpl/'); ?>
<?php $items = $helper -> getComparedProducts(); ?>

<?php if(count($items)){?>
<div id="citruscartComparedProducts">
	<ul>
		<?php foreach($items as $item):?>
		<li>
			<a href="<?php echo JRoute::_($item->link)?>">
				<?php echo $item -> product_name; ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<div class="compared-right">
	<a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=productcompare"); ?>" title="<?php echo JText::_('COM_CITRUSCART_COMPARED_PRODUCTS')?>"><?php echo JText::_('COM_CITRUSCART_COMPARE_NOW'); ?></a>
</div>
	<?php }else{ ?>
		<?php echo JText::_('COM_CITRUSCART_NO_COMPARED_PRODUCTS'); ?>
	<?php } ?>
