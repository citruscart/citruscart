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

JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
$items = $this->items;

$show_manufacturer = Citruscart::getInstance()->get('show_manufacturer_productcompare', '1');
$show_srp = Citruscart::getInstance()->get('show_srp_productcompare', '1');
$show_addtocart = Citruscart::getInstance()->get('show_addtocart_productcompare', '1');
$show_rating = Citruscart::getInstance()->get('show_rating_productcompare', '1');
$show_model = Citruscart::getInstance()->get('show_model_productcompare', '1');
$show_sku = Citruscart::getInstance()->get('show_sku_productcompare', '1');
?>
<a name="citruscart-compare"></a>
<h1><?php echo JText::_('COM_CITRUSCART_COMPARE')?></h1>
<?php if(count($items)):?>
<div id="citruscartProductCompareScroll">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr class="row0">
				<td valign="middle" class="first-cell center">
					<?php echo JText::_('COM_CITRUSCART_COMPARE')?>
				</td>
					<?php foreach($items as $item):?>
				<td align="center" valign="top" class="border-left">
					<a title="<?php echo JText::_('COM_CITRUSCART_REMOVE_PRODUCT_COMPARISON'); ?>" class="close-img" href="<?php echo JRoute::_('index.php?index.php?option=com_citruscart&view=productcompare&task=remove&id='.$item->productcompare_id);?>">
						<img src="<?php echo Citruscart::getURL('images');?>closebox.gif">
					</a>
						<?php echo CitruscartHelperProduct::getImage($item->product_id, '', $item->product_name); ?>
				</td>
						<?php endforeach;?>
			</tr>
			<tr valign="top"  class="row0">
				<td></td>
					<?php foreach($items as $item):?>
				<td align="center" class="border-left">
					<a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=products&task=view&id='. $item->product_id)?>">
					<?php echo $item->product_name?>
					</a>
					 <div class="reset"></div>
					<a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=products&task=view&id='. $item->product_id)?>">
						<span class="arrow">>></span> <?php echo JText::_('Learn More')?> <span class="arrow"><<</span>
					</a>
				</td>
					<?php endforeach;?>
			</tr>
			<?php if($show_srp ):?>
			<tr  class="row1">
				<td>
					<?php echo JText::_('COM_CITRUSCART_SRP')?>
				</td>
					<?php foreach($items as $item):?>
				<td align="center" class="border-left">
				<?php if( $show_addtocart ):?>
					<div id="product_buy_<?php echo $item->product_id; ?>" class="product_buy">
						<?php echo CitruscartHelperProduct::getCartButton( $item->product_id, 'product_buy', array() );?>
					</div>
				<?php else:?>
				<?php echo CitruscartHelperBase::currency($item->product_price); ?>
				<?php endif;?>

				</td>
					<?php endforeach;?>
			</tr>
			<?php endif?>
			<?php if( $show_rating ):?>
			<tr  class="row0">
				<td>
					<?php echo JText::_('COM_CITRUSCART_AVERAGE_CUSTOMER_RATING')?>
				</td>
					<?php foreach($items as $item):?>
				<td align="center" class="border-left">
					<?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating, $this ); ?>
				</td>
					<?php endforeach;?>
			</tr>
			<?php endif?>

			<?php if( $show_manufacturer ):?>
			<tr  class="row1">
				<td>
					<?php echo JText::_('COM_CITRUSCART_MANUFACTURER')?>
				</td>
				<?php foreach($items as $item):?>
				<td align="center" class="border-left">
					<?php echo $item->manufacturer_name?>
				</td>
				<?php endforeach;?>
			</tr>
			<?php endif;?>

			<?php if( $show_model ):?>
			<tr  class="row0">
				<td>
					<?php echo JText::_('COM_CITRUSCART_MODEL')?>
				</td>
				<?php foreach($items as $item):?>
				<td align="center" class="border-left">
					<?php echo $item->product_model?>
				</td>
				<?php endforeach;?>
			</tr>
			<?php endif;?>

			<?php if( $show_sku ):?>
			<tr  class="row1">
				<td>
					<?php echo JText::_('COM_CITRUSCART_SKU')?>
				</td>
				<?php foreach($items as $item):?>
				<td align="center" class="border-left">
					<?php echo $item->product_sku?>
				</td>
				<?php endforeach;?>
			</tr>
			<?php endif;?>
		</tbody>
	</table>
</div>
<?php else:?>
<div style="text-align: center;">
<p><?php echo JText::_('COM_CITRUSCART_NO_PRODUCTS_SELECTED')?></p>
</div>
<?php endif;?>