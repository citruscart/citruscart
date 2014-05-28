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

    <p><?php echo JText::_('COM_CITRUSCART_THIS_REPORTS_ON_MOST_DOWNLOADED_FILES'); ?></p>
    <div class="note">
    	
    	<!-- most downloaded files table starts -->
    	<table class="adminlist table table-bordered table-striped">
    		<thead>
    		    <th>
    				<?php echo JText::_('COM_CITRUSCART_FILE_NAME'); ?>:
    			</th>
    			<th>
    				<?php echo JText::_('COM_CITRUSCART_PRODUCT_NAME'); ?>:
    			</th>
    			<th>
    				<?php echo JText::_('COM_CITRUSCART_SELECT_DOWNLOADS_RANGE'); ?>:
    			</th>
    		</thead>
    		<tbody>
    		<tr>
    		    <td><input type="text" name="filter_file_name" id="filter_file_name" value="<?php echo $state->filter_file_name; ?>" /></td> 	
				
				<td><input type="text" name="filter_product_name" id="filter_product_name" value="<?php echo $state->filter_product_name; ?>" /></td>
		  	   	<td>
			    	<span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
			    	<?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S' ); ?>
			    	<span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
			    	<?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S' ); ?>
				</td>
			</tr>
			</tbody>
		</table><!-- most downloaded files table ends -->
	</div>