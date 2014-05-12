<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

 ?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php Citruscart::load( 'CitruscartHelperBase', 'helpers._base' ); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 20px;">
                	<?php echo JText::_('COM_CITRUSCART_ID'); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo JText::_('COM_CITRUSCART_ORDER'); ?>
                </th>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_ORDER_STATUS'); ?>
                </th>
				 <th>
                	<?php echo JText::_('COM_CITRUSCART_DO_COMPLETED_ORDER_TASKS'); ?>?
                </th>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_NOTIFY_CUSTOMER'); ?>
                </th>
                 <th>
                	<?php echo JText::_('COM_CITRUSCART_COMMENTS'); ?>
                </th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>" target="_blank">
						<?php echo $item->order_id; ?>
					</a>
					<input type="hidden" name="cid[]" value="<?php echo $item->order_id; ?>"/>
				</td>
				<td style="text-align: left;">
				    <div>
					<a href="<?php echo $item->link; ?>" target="_blank">
						<?php echo JHTML::_('date', $item->created_date, Citruscart::getInstance()->get('date_format')); ?>
					</a>
					</div>
                    <?php echo $item->user_name .' [ '.$item->user_id.' ]'; ?>
                    &nbsp;&nbsp;&bull;&nbsp;&nbsp;<?php echo $item->email .' [ '.$item->user_username.' ]'; ?>
                    <br/>
                    <b><?php echo JText::_('COM_CITRUSCART_SHIP_TO'); ?></b>:
                    <?php
                    if (empty($item->shipping_address_1))
                    {
                       echo JText::_('COM_CITRUSCART_UNDEFINED_SHIPPING_ADDRESS');
                    }
                       else
                    {
                        echo $item->shipping_address_1.", ";
                        echo $item->shipping_address_2 ? $item->shipping_address_2.", " : "";
                        echo $item->shipping_city.", ";
                        echo $item->shipping_zone_name." ";
                        echo $item->shipping_postal_code." ";
                        echo $item->shipping_country_name;
                    }
                    ?>
                    <?php
                    if (!empty($item->order_number))
                    {
                        echo "<br/><b>".JText::_('COM_CITRUSCART_ORDER_NUMBER')."</b>: ".$item->order_number;
                    }
                    ?>
				</td>

				<td style="text-align: center;">
			    <?php echo CitruscartSelect::orderstate( $item->order_state_id, 'new_orderstate_id[]' ); ?>
    	    	</td>

    	    	<td style="text-align: center;">
			    <?php if (empty($item->completed_tasks)) {
        	      echo '<input id="completed_tasks" name="completed_tasks['.$item->order_id.']" type="checkbox" />' ;
        	     } else {
        	     echo '<input id="completed_tasks" name="completed_tasks['.$item->order_id.']" type="checkbox" checked="checked" disabled="disabled" />' ;
        	     }?>
    	    	</td>

    	    	<td style="text-align: center;">
			     <?php echo '<input id="new_orderstate_notify" name="new_orderstate_notify['.$item->order_id.']" type="checkbox" />' ; ?>
    	    	</td>

    	    <td style="text-align: center;">
			     <textarea id="new_orderstate_comments" style="width: 90%;" rows="5" name="new_orderstate_comments[<?php echo $item->order_id; ?>]" ></textarea>

			</td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>

			<?php if (!count($items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="20">
					&nbsp;
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" id="task" value="" />

	<?php echo $this->form['validate']; ?>
</form>