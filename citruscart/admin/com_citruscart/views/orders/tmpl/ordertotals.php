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
<?php $totals = $this->row; ?>
<?php Citruscart::load( 'CitruscartHelperBase', 'helpers._base' ); ?>

<table class="table table-striped table-bordered">
<thead>
	<tr>
		<th colspan="4" style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_ORDER_TOTALS'); ?></th>
	</tr>
</thead>
<tbody>
	<tr>
		<th style="width: 100px;" class="key">
			<?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>:
		</th>
		<td>
            <?php echo CitruscartHelperBase::currency( $totals->order_subtotal) ?>
        </td>
	</tr>
	<tr>
		<th style="width: 100px;" class="key">
			 <?php echo JText::_('COM_CITRUSCART_TAX'); ?>:
		</th>
		<td>
            <?php echo CitruscartHelperBase::currency( $totals->order_tax) ?>
		</td>
	</tr>
	<tr>
		<th style="width: 100px;" class="key">
			 <?php echo JText::_('COM_CITRUSCART_SHIPPING_COSTS'); ?>:
		</th>
		<td>
		    <?php echo CitruscartHelperBase::currency( $this->shipping_total->shipping_rate_price ); ?>
		</td>
	</tr>
    <tr>
        <th style="width: 100px;" class="key">
             <?php echo JText::_('COM_CITRUSCART_HANDLING_COSTS'); ?>:
        </th>
        <td>
            <?php echo CitruscartHelperBase::currency( $this->shipping_total->shipping_rate_handling ); ?>
        </td>
    </tr>
	<tr>
		<th style="width: 100px;" class="key">
			 <?php echo JText::_('COM_CITRUSCART_SHIPPING_TAX'); ?>:
		</th>
		<td>
		    <?php echo CitruscartHelperBase::currency( $this->shipping_total->shipping_tax_total ); ?>
		</td>
	</tr>
	<tr>
		<th style="width: 100px;" class="key">
			<label for="grand_total" style="color:#1432F2;font-size:16px;">
			 <?php echo JText::_('COM_CITRUSCART_GRAND_TOTAL'); ?>:
			</label>
		</th>
		<td style="color:#1432F2;font-size:16px;">
            <?php echo CitruscartHelperBase::currency( $totals->order_total ); ?>
		</td>
	</tr>
	</tbody>
</table>
