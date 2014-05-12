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
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>

<div class='componentheading'>
	<span><?php echo JText::_('COM_CITRUSCART_ORDER_HISTORY'); ?></span>
</div>

<form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">
	<table class="adminlist">
		<thead>
			<th style="text-align: center; width: 200px;">
				<?php echo JText::_('COM_CITRUSCART_ORDER_DATE'); ?>
			</th>
			<th style="width: 60px;">
				<?php echo JText::_('COM_CITRUSCART_ORDER_NUMBER'); ?>
			</th>
			<th style="text-align: center; width: 80px">
				<?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
			</th>
			<th style="text-align: center; width: 160px;">
				<?php echo JText::_('COM_CITRUSCART_STATUS');  ?>
			</th>
			<th style="text-align: center">
				<?php echo JText::_('COM_CITRUSCART_CONTACT_US'); ?>
			</th>
		</thead>
		<tfoot>
            <tr>
                <td colspan="20">
                    <div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
     	</tfoot>
     	<tbody>
		<?php $i=0; $k=0; ?>
		<?php foreach ($items as $item) : ?>
		<tr class='row <?php echo $k; ?>'>
			<td align="center">
				<?php echo JHTML::_('date', $item->created_date, Citruscart::getInstance()->get('date_format')); ?>
			</td>
			<td align="center">
				<a href="<?php echo JRoute::_( $item->link_view ); ?>">
					<?php echo $item->order_id; ?>
				</a>
			</td>
			<td style="text-align: right;">
                    <?php echo AmigosHelperBase::currency( $item->order_total); ?>
              </td>
              <td style="text-align: center;">
                    <?php echo $item->order_state_name; ?>
                </td>
              <td style="text-align: center;">
              	<a href="index.php/contact-us.html?view=rsform">
              		<?php echo JText::_('COM_CITRUSCART_CONTACT_US'); ?>
              	</a>
            </td>
		</tr>
		<?php ++$i; $k = (1 - $k); ?>
		<?php endforeach; ?>

		<?php if (!count($items)) : ?>
            <tr>
                <td colspan="10" align="center">
                    <?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
                </td>
            </tr>
            <?php endif; ?>
     	</tbody>
	</table>
</form>