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

    <p><?php echo JText::_('COM_CITRUSCART_THIS_REPORTS_ON_LOW_STOCK_PRODUCTS'); ?></p>
    
    <div class="note">
    	    
    	    <table class="adminlist table table-bordered table-striped">
    	    	<thead>
	    	    	<th><?php echo JText::_('COM_CITRUSCART_PRODUCT_NAME'); ?>:</th>
	    	    	<th><?php echo JText::_('COM_CITRUSCART_SELECT_QUANTITY_RANGE'); ?>:</th>
	    	    	<th><?php echo JText::_('COM_CITRUSCART_SELECT_CATEGORY'); ?>:</th>
	    	    	<th><?php echo JText::_('COM_CITRUSCART_SELECT_PRODUCTS_TO_SHOW'); ?>:</th>
    	    	</thead>
    	    	<tbody>
    	    		<tr>
    	    		   <td>
    	    			<input type="text" name="filter_name" id="filter_name" value="<?php echo $state->filter_name; ?>" />
    	    	       </td>
    	    	       <td>	
    	    			<span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
				   		<input type="text" class="input input-tiny" name="filter_quantity_from" id="filter_quantity_from" value="<?php echo $state->filter_quantity_from; ?>" />
				    	<span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
				   		<input type="text" class="input input-tiny" name="filter_quantity_to" id="filter_quantity_to" value="<?php echo $state->filter_quantity_to; ?>" />
    	    		   </td>
    	    		   <td>
    	    			<?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'javascript:submitbutton(\'view\').click;'); ?>
		  				<?php echo CitruscartSelect::category( $state->filter_category, 'filter_category', $attribs, 'category', true ); ?>
    	    		   </td>
    	    		   <td>
    	    			<?php $attribs = array('class' => 'inputbox', 'size' => '1' ); ?>
						<?php
								$arr = array(
									JHTML::_('select.option',  '', JText::_( JText::_('COM_CITRUSCART_ALL') ) ),
									JHTML::_('select.option',  '1', JText::_( JText::_('COM_CITRUSCART_ENABLED') ) ),
									JHTML::_('select.option',  '0',  JText::_( JText::_('COM_CITRUSCART_DISABLED') ) )
								);
				
								echo JHtml::_( 'select.radiolist', $arr, 'filter_enabled', $attribs, 'value', 'text', $state->filter_enabled );
						?>
					   </td>  	    				
    	    		</tr>
    	    	</tbody>
    	    	
    	    </table>
    	   	
	</div>