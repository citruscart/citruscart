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
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $vars->state; ?>

<p><?php echo JText::_('COM_CITRUSCART_THIS_REPORT_LISTS_ALL_USERS_WHO_OPENED_A_NEW_SUBSCRIPTION_DURING_THE_SELECTED_TIME_PERIOD'); ?></p>
<div>

    <!-- subscriptions table starts -->
	<table class="adminlist table table-bordered table-striped">
	<thead>
		<tr>
			<th style="text-align: center; width: 485px;" class="key">
				<?php echo JText::_('COM_CITRUSCART_SELECT_DATE_RANGE'); ?>
			</th>
			<th style="text-align: left;" class="key">
				<?php echo JText::_('COM_CITRUSCART_ORDER_STATE'); ?>
			</th>
		</tr>
		<tr>
			<th align="left" style="text-align: left;" class="key">
				<?php $attribs = array('class' => 'inputbox', 'size' => '1'); ?>
				<?php echo CitruscartSelect::reportrange( $state->filter_range ? $state->filter_range : 'custom', 'filter_range', $attribs, 'range', true ); ?>
				<span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
				<?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S' ); ?>
			</th>
			<th>	
				<span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
				<?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S' ); ?>
				<input type="hidden" name="filter_datetype" value="created" />
			</th>
			<th align="left" style="text-align: left;" class="key">
				<?php
					$attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();');
					echo CitruscartSelect::orderstate($state->filter_orderstate, 'filter_orderstate', $attribs, 'order_state_id', true );
				?>
			</th>
		</tr>
    </thead>
	</table><!-- subscriptions table ends -->
</div>
