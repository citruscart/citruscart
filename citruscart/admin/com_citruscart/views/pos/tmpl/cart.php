<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');?>
<?php JHTML::_('stylesheet', 'Citruscart.css', 'media/citruscart/css/');?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php $items = $this -> items;?>
<div class="cartitems">

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th style="width: 20px;">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
				</th>
				<th colspan="2" style="text-align: left;">
				<?php echo JText::_('COM_CITRUSCART_PRODUCT');?>
				</th>
				<th style="width: 50px;">
				<?php echo JText::_('COM_CITRUSCART_QUANTITY');?>
				</th>
				<th style="width: 50px;">
				<?php echo JText::_('COM_CITRUSCART_TOTAL');?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0;
			$k = 0;
			$subtotal = 0;
			?>
			<?php foreach ($items as $item) : ?>

			<tr class="row<?php echo $k;?>">
				<td style="border-bottom: 1px solid #E5E5E5; width: 20px; text-align: center;">
				<input type="checkbox" id="cb<?php echo $i;?>" name="cid[<?php echo $item -> cart_id;?>]" value="<?php echo $item -> product_id;?>" onclick="isChecked(this.checked);" />
				</td>
				<td style="border-bottom: 1px solid #E5E5E5; text-align: center; width: 50px;">
				<?php echo CitruscartHelperProduct::getImage($item -> product_id, 'id', $item -> product_name, 'full', false, false, array('width' => 48));?>
				</td>
				<td style="border-bottom: 1px solid #E5E5E5;">
					<a href="index.php?option=com_citruscart&view=products&task=edit&id=<?php echo $item->product_id;?>">
					<?php echo $item -> product_name;?>
					</a>
					<br/>
					<?php if (!empty($item->attributes_names)) : ?>
	                	<?php echo $item->attributes_names; ?>
	                	<br/>
	                <?php endif; ?>
	                <?php if (!empty($this->onDisplayCartItem[$i])) : ?>
	                	<?php echo $this->onDisplayCartItem[$i]; ?>
	                	<br/>
	                <?php endif; ?>
	                    <input name="product_attributes[<?php echo $item->cart_id; ?>]" value="<?php echo $item->product_attributes; ?>" type="hidden" />
				</td>
				<td style="border-bottom: 1px solid #E5E5E5; width: 50px; text-align: center;">
				<?php $type = 'text';
					if($item -> product_parameters -> get('hide_quantity_cart') == '1') {
						$type = 'hidden';
						echo $item -> product_qty;
					}
				?>

				<input name="quantities[<?php echo $item -> cart_id;?>]" type="<?php echo $type;?>" size="3" maxlength="3" value="<?php echo $item -> product_qty;?>" />

				<!-- Keep Original quantity to check any update to it when going to checkout -->
				<input name="original_quantities[<?php echo $item -> cart_id;?>]" type="hidden" value="<?php echo $item -> product_qty;?>" />
				</td>
				<td style="border-bottom: 1px solid #E5E5E5; text-align: right;">
				<?php $product_total = ($item -> product_price) * ($item -> product_qty);?>
				<?php echo CitruscartHelperBase::currency($product_total); $i++; ?>
				</td>
			</tr>
			<?php $subtotal = $subtotal + $product_total;?>
			<?php endforeach;?>
			<tr>
				<td colspan="3" style="border-bottom: 1px solid #E5E5E5; text-align: left;">
				<input type="submit" class="btn btn-danger" value="<?php echo JText::_('COM_CITRUSCART_REMOVE_SELECTED');?>" onclick="CitruscartSubmitForm('removeItems')" name="remove" />
				</td>
				<td colspan="2" style="border-bottom: 1px solid #E5E5E5; ">
				<input style="float: right;" type="submit" class="button btn btn-primary" value="<?php echo JText::_('COM_CITRUSCART_UPDATE_QUANTITIES');?>" onclick="CitruscartSubmitForm('updateQty')" name="update" />

				</td>
			</tr>
			<tr>
				<td colspan="4" style="border-bottom: 1px solid #E5E5E5;  text-align: right; font-weight: bold;">
				<?php echo JText::_('COM_CITRUSCART_SUBTOTAL');?>
				</td>
				<td style="border-bottom: 1px solid #E5E5E5; text-align: right;">
				<?php echo CitruscartHelperBase::currency($subtotal);?>
				</td>
			</tr>

		</tbody>
	</table>
</div>
<input type="hidden" name="boxchecked" value="" />