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
<?php $doc = JFactory::getDocument(); ?>
<?php $doc->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_pricefilters/tmpl/mod_citruscart_pricefilters.css'); ?>

<h5><?php echo "Price filters";?></h5>
<ul id="citruscart_pricefilter_mod" class="unstyled nav nav-tab">
	
<?php if (empty($priceRanges)) { ?>
    <li class="emptyfilter">
    	<?php echo JText::_('COM_CITRUSCART_NO_CATEGORY_MANUFACTURER_FOUND'); ?>
    </li>
<?php }else{?>	

	<?php $i = 1;?>
	<?php foreach ($priceRanges as $link => $range ) : ?>
		<?php $selected = JRequest::getInt('rangeselected') ?>
		<?php $class = ($selected == $i) ? 'range selected' : 'range';?>	
		<li class="product_price_filters <?php echo $class;?>"  >
			<a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products".$link."&rangeselected=".$i ); ?>">
				<span><?php echo $range; ?></span>
			</a>
		</li>	
	<?php $i++;?>
	<?php endforeach; ?>
	
<?php }?>

</ul>

<div style="float: right;">
<?php if (!empty($show_remove)) : ?>
    <a href="<?php echo JRoute::_( $remove_pricefilter_url ); ?>"><?php echo JText::_('COM_CITRUSCART_REMOVE_FILTER') ?></a>
<?php endif; ?>
</div>
