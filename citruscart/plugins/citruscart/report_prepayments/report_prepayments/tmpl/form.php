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
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $vars->state; ?>

    <p><?php echo JText::_('COM_CITRUSCART_THIS_REPORTS_ON_PRE-PAYMENT_ORDERS'); ?></p>
<div>
<table class="adminlist table table-striped table-bordered">
	<thead>
		<tr>
			<th style="text-align: center;" class="key">
                <?php echo JText::_('COM_CITRUSCART_ID'); ?>
            </th>
            <th style="text-align: center;" class="key">
                <?php echo JText::_('COM_CITRUSCART_DATE_OF_ORDER'); ?>
            </th>
            <th style="text-align: center;" class="key">
                <?php echo JText::_('COM_CITRUSCART_CUSTOMER'); ?>
            </th>
            <th style="text-align: center;" class="key">
                <?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
            </th>
		</tr>
		<tr>
			<th align="left" style="text-align: left;" class="key">
				<span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span> <input id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" class="input-mini" />
				<span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span> <input id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" class="input-mini" />
			</th>
			<th class="key" style="text-align: left;">
				<span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
               	<?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S',array('class'=>'input-mini') ); ?>
				 <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
				 <?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S',array('class'=>'input-mini') ); ?>
 				 <span class="label"><?php echo JText::_('COM_CITRUSCART_TYPE'); ?>:</span>
                 <?php echo CitruscartSelect::datetype( $state->filter_datetype, 'filter_datetype', '', 'datetype' ); ?>
			</th>
			<th class="key">
					<input id="filter_user" name="filter_user" value="<?php echo $state->filter_user; ?>" size="25"/>
			</th>
			<th class="key" style="text-align: left;">
				<span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span> <input id="filter_total_from" name="filter_total_from" value="<?php echo $state->filter_total_from; ?>" size="5" class="input" />
	   <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span> <input id="filter_total_to" name="filter_total_to" value="<?php echo $state->filter_total_to; ?>" size="5" class="input" />
			</th>
		</tr>
	</thead>
</table>
   </div>
