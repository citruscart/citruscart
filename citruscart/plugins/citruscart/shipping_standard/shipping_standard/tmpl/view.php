<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php

JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $form = $this->form2;
?>
<?php
if($this->item){
	$row = $this->item;

}else{
	JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables/');
	$row =JTable::getInstance('shippingmethods','Citruscarttable');
}

//print_r($row);
JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ); ?>" method="post" name="adminForm" enctype="multipart/form-data">
<div class="well">
	<legend><?php echo JText::_('COM_CITRUSCART_FORM'); ?></legend>

	<div style="width: 65%; float: left;">
	<table class="table table-striped admintable adminlist"  >
		<tr>
			<td width="100" align="right" class="key">
				<label for="shipping_method_name">
				<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="shipping_method_name" id="shipping_method_name" value="<?php echo $row->shipping_method_name; ?>" size="48" maxlength="250" />
			</td>
		</tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="tax_class_id">
                <?php echo JText::_('COM_CITRUSCART_TAX_CLASS'); ?>:
                </label>
            </td>
            <td>
                <?php echo CitruscartSelect::taxclass( $row->tax_class_id, 'tax_class_id', '', 'tax_class_id', false ); ?>
            </td>
        </tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="shipping_method_enabled">
				<?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
				</label>
			</td>
			<td>
				<?php echo CitruscartSelect::btbooleanlist('shipping_method_enabled', '', $row->shipping_method_enabled ); ?>
			</td>
		</tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="shipping_method_type">
                <?php echo JText::_('COM_CITRUSCART_TYPE'); ?>:
                </label>
            </td>
            <td>
                <?php echo CitruscartSelect::shippingtype( $row->shipping_method_type, 'shipping_method_type', '', 'shipping_method_type', false ); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="subtotal_minimum">
                <?php echo JText::_('COM_CITRUSCART_MINIMUM_SUBTOTAL_REQUIRED'); ?>:
                </label>
            </td>
            <td>
                <input type="text" name="subtotal_minimum" id="subtotal_minimum" value="<?php echo $row->subtotal_minimum; ?>" size="10" />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="subtotal_maximum">
                <?php echo JText::_('COM_CITRUSCART_SHIPPING_METHODS_SUBTOTAL_MAX'); ?>:
                </label>
            </td>
            <td>
                <input type="text" name="subtotal_maximum" id="subtotal_maximum" value="<?php echo $row->subtotal_maximum; ?>" size="10" />
            </td>
        </tr>
	</table>
    </div>

    <div class="well note" style="width: 25%; float: left; padding-right: 20px;">
        <span style="font-weight: bold; font-size: 13px; text-transform: uppercase;"><?php echo JText::_('COM_CITRUSCART_NOTE'); ?>:</span>
        <?php echo JText::_('COM_CITRUSCART_SHIPPING_TYPE_HELP_TEXT'); ?>:
        <ul>
            <li><?php echo JText::_('COM_CITRUSCART_FLAT_RATE_PER_ITEM_HELP_TEXT'); ?></li>
            <li><?php echo JText::_('Weight-Based Per Item HELP TEXT'); ?></li>
            <li><?php echo JText::_('COM_CITRUSCART_WEIGHT-BASED_PER_ITEM_HELP_TEXT'); ?></li>
            <li><?php echo JText::_('COM_CITRUSCART_WEIGHT-BASED_PER_ORDER_HELP_TEXT'); ?></li>
            <li><?php echo JText::_('COM_CITRUSCART_PRICE-BASED_PER_ITEM_HELP_TEXT'); ?></li>
            <li><?php echo JText::_('COM_CITRUSCART_QUANTITY-BASED_PER_ORDER_HELP_TEXT'); ?></li>
            <li><?php echo JText::_('COM_CITRUSCART_PRICE-BASED_PER_ORDER_HELP_TEXT'); ?></li>
        </ul>
    </div>

    <div style="clear: both;"></div>

	<input type="hidden" name="shipping_method_id" value="<?php echo $row->shipping_method_id; ?>" />
	<input type="hidden" id="shippingTask" name="shippingTask" value="<?php echo $form['shippingTask']; ?>" />


	</div>
</form>