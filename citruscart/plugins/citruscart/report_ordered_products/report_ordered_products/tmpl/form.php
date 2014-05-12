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
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $state = $vars->state; ?>

<p><?php echo JText::_('COM_CITRUSCART_THIS_REPORT_DISPLAYS_ORDERED_PRODUCTS'); ?></p>

<div class="note">
	
	<!-- ordered products table starts -->
	<table class="adminlist table table-bordered table-striped">
		<thead>
			<th><?php echo JText::_('COM_CITRUSCART_ENTER_PRODUCT_NAME'); ?>:</th>
			<th><?php echo JText::_('COM_CITRUSCART_MANUFACTURER'); ?>:</th>
			<th><?php echo JText::_('COM_CITRUSCART_SELECT_DATE_RANGE'); ?>:</th>
		</thead>
		<tbody>
			<tr>
			   <td><input type="text" name="filter_product_name" id="filter_product_name" value="<?php echo $state->filter_product_name; ?>" style="width: 250px;" /></td>
			   <td><?php echo CitruscartSelect::manufacturer( $state->filter_manufacturer_id, 'filter_manufacturer_id', array('class' => 'inputbox', 'size' => '1'), null, true ) ?></td>
               <td>
	               <?php $attribs = array('class' => 'inputbox', 'size' => '1'); ?>
				   <?php echo CitruscartSelect::reportrange( $state->filter_range ? $state->filter_range : 'custom', 'filter_range', $attribs, 'range', true ); ?>
				   <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
				   <?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S' ); ?>
				   <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
				   <?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S' ); ?>
               	</td>	   
			</tr>
		</tbody>
	</table><!-- ordered products table ends -->
</div>