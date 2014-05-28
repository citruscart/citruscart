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
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php $state = $vars->state; ?>
<?php $items = @$vars->items; ?>
<table class="table table-striped table-bordered" style="clear: both;">
	<thead>
		<tr>
			<th style="width: 5px;"><?php echo JText::_('COM_CITRUSCART_NUM'); ?>
			</th>
			<th style="width: 50px;"><?php echo JText::_('COM_CITRUSCART_ID'); ?>
			</th>
			<th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_NAME'); ?>
			</th>
			<th style="text-align: left; width: 267px;"><?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>
			</th>
			<th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_DATE'); ?>
			</th>

			<th style="width: 100px;"><?php echo JText::_('COM_CITRUSCART_NUMBERS_OF_ITEMS'); ?>
			</th>
			<th style="width: 85px;"><?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="20"></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $i=0; $k=0; $subtotal = 0; ?>
	<?php foreach ($items as $item) : ?>
		<tr class='row<?php echo $k; ?>'>
			<td align="center"><?php echo $i + 1; ?>
			</td>
			<td style="text-align: center;"><?php echo $item->user_id;?>
			</td>
			<td style="text-align: left;">
				<a href="index.php?option=com_citruscart&view=users&task=view&id=<?php echo $item->user_id;?>">
					<?php echo @$item->name; ?>
				</a>
			</td>
			<td style="text-align: left;"><?php echo @$item->email; ?>
			</td>
			<td style="text-align: left;"><?php echo JHTML::_('date', $item->last_updated, Citruscart::getInstance()->get('date_format')); ?>
			</td>
			<td style="text-align: center;"><?php echo $item->total_items; ?>
			</td>
			<td style="text-align: center;">
			<?php echo CitruscartHelperBase::currency($item->subtotal); ?>
			</td>
		</tr>
		<?php ++$i; $k = (1 - $k); ?>
		<?php endforeach; ?>

		<?php if (!count($items)) : ?>
		<tr>
			<td colspan="10" align="center"><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
