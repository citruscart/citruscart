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
defined('_JEXEC') or die('Restricted access');

 ?>
<?php $items = (!empty($this->row->orderitems)) ? $this->row->orderitems : array(); ?>
<?php Citruscart::load( 'CitruscartHelperBase', 'helpers._base' ); ?>

        <table class="table table-striped table-bordered">
            <thead>
                <?php if (count($items)) : ?>
                <tr>
                    <th style="width: 20px;">
                        <?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                    </th>
                    <th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></th>
                    <th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
                </tr>
                <?php endif; ?>
            </thead>
            <tbody>
            <?php $i=0; $k=0; ?>
            <?php foreach ($items as $item) : ?>
                <tr class='row<?php echo $k; ?>'>
                    <td style="text-align: center;">
                        <input type="checkbox" id="cb<?php echo $i; ?>" name="products[]" value="<?php echo $item->product_id; ?>" onclick="isChecked(this.checked);" />
                    </td>
                    <td style="text-align: left;">
	                    <?php echo $item->orderitem_name; ?>
	                    <br />
	                    <b><?php echo JText::_('COM_CITRUSCART_PRICE'); ?>:</b>
	                    <?php echo CitruscartHelperBase::currency( $item->orderitem_price ); ?>

	                    <?php if (!empty($item->orderitem_sku)) : ?>
		                    <br />
		                    <b><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</b>
		                    <?php echo $item->orderitem_sku; ?>
		                <?php endif; ?>
	                </td>
                    <td style="text-align: center; vertical-valign: top;">
                        <input name="quantity[<?php echo $item->product_id; ?>]" value="<?php echo $item->orderitem_quantity; ?>" style="width: 30px;" type="text" />
                    </td>
                    <td style="text-align: right; vertical-valign: top;">
                        <?php echo CitruscartHelperBase::currency( $item->orderitem_final_price ); ?>
                    </td>
                </tr>
            <?php $i=$i+1; $k = (1 - $k); ?>
            <?php endforeach; ?>
            <?php
            if (empty($items)) { ?>
	            <tr>
	            <td colspan="5" align="center">
	            <?php echo JText::_('COM_CITRUSCART_NO_ITEMS_IN_ORDER'); ?>
	            </td>
	            </tr>
            <?php } ?>

            <?php if (count($items)) : ?>
                <tr>
                    <td colspan="2" style="text-align: left;">
                        <input class="btn btn-danger" onclick="CitruscartRemoveProducts('<?php echo JText::_('COM_CITRUSCART_PLEASE_SELECT_AN_ITEM_TO_REMOVE'); ?>');" value="<?php echo JText::_('COM_CITRUSCART_REMOVE SELECTED'); ?>" class="btn" type="button" />
                    </td>
                    <td colspan="2" style="text-align: right;">
                        <input class="btn btn-primary"  onclick="CitruscartUpdateProductQuantities();" value="<?php echo JText::_('COM_CITRUSCART_UPDATE_QUANTITIES'); ?>" class="btn" style="float: right;" type="button" />
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>