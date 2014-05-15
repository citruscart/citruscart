<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Citruscart Design http://www.Citruscart.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	JHtml::_('stylesheet', 'media/citruscart/css/citruscart.css');
 	$form = $this->form2;
   	$row = $this->row;
   	$state = $this->state;
   	$items = $this->items;
   	$baseLink = $this->baseLink;
  	$default_group = Citruscart::getInstance()->get( 'default_user_group', 1 );
?>
<h3><?php echo JText::_('COM_CITRUSCART_SET_RATES_FOR'); ?>: <?php echo $row->shipping_method_name; ?></h3>

<div class="note" style="width: 95%; text-align: center; margin-left: auto; margin-right: auto;">
	<?php echo JText::_('COM_CITRUSCART_BE_SURE_TO_SAVE_YOUR_WORK'); ?>:
	<button onclick="document.adminForm.toggle.checked=true; checkAll(<?php echo count( $items ); ?>); document.getElementById('shippingTask').value='saverates'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_CHANGES'); ?></button>
</div>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( JFactory::getApplication()->input->get('view') ); ?>

    <table class="adminlist table table-striped table-bordered">
        <tr>
            <td align="left" >
            </td>

            <td nowrap="nowrap">
            	<table class="adminlist table table-striped table-bordered">
            	<thead>
            	<tr>
            		<th></th>
                    <th><?php echo JText::_('COM_CITRUSCART_GEOZONE'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_USER_GROUP'); ?></th>
            		<th><?php echo JText::_('COM_CITRUSCART_WEIGHT_RANGE'); ?></th>
            		<th><?php echo JText::_('COM_CITRUSCART_PRICE'); ?></th>
            		<th><?php echo JText::_('COM_CITRUSCART_HANDLING_FEE'); ?></th>
            		<th></th>
            	</tr>
            	</thead>
            	<tbody>
            	<tr>
            		<td>
            			<?php echo JText::_('COM_CITRUSCART_COMPLETE_THIS_FORM_TO_ADD_A_NEW_RATE'); ?>:
                	</td>
            		<td>
                		<?php echo CitruscartSelect::geozone("", "geozone_id", 2); ?>
                		<input type="hidden" name="shipping_method_id" value="<?php echo $row->shipping_method_id; ?>" />
            		</td>
            		<td>
            			<?php echo CitruscartSelect::groups($default_group); ?>
            		</td>
            		<td>
            			<input id="shipping_rate_weight_start" name="shipping_rate_weight_start" value="" />
            			<?php echo JText::_('COM_CITRUSCART_TO'); ?>
                		<input id="shipping_rate_weight_end" name="shipping_rate_weight_end" value="" />
                	</td>
            		<td>
            			<input id="shipping_rate_price" name="shipping_rate_price" value="" />
            		</td>
                    <td>
                        <input id="shipping_rate_handling" name="shipping_rate_handling" value="" />
                    </td>
            		<td>
            			<input type="button" onclick="document.getElementById('shippingTask').value='createrate'; document.adminForm.submit();" value="<?php echo JText::_('COM_CITRUSCART_CREATE_RATE'); ?>" class="btn" />
            		</td>
            	</tr>
            	</tbody>
            	</table>
            </td>
        </tr>
    </table>

	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 20px;">
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
        		 </th>
                <th style="text-align: center;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_GEO_ZONE', "tbl.geozone_id", $state->direction, $state->order ); ?>
                </th>
				<th>
                  <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USER_GROUP', "g.ordering", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "tbl.shipping_rate_price", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_WEIGHT_RANGE', "tbl.shipping_rate_weight_start", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_HANDLING_FEE', "tbl.shipping_rate_handling", $state->direction, $state->order ); ?>
                </th>
				<th>
				</th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'shipping_rate_id' ); ?>
				</td>
                <td style="text-align: center;">
                    <?php echo CitruscartSelect::geozone($item->geozone_id, "geozone[{$item->shipping_rate_id}]", 2); ?>
                </td>
                <td style="text-align: center;">
                   <?php echo CitruscartSelect::groups($item->group_id, "groups[{$item->shipping_rate_id}]" ); ?>
                </td>
				<td style="text-align: center;">
					<input type="text" name="price[<?php echo $item->shipping_rate_id; ?>]" value="<?php echo $item->shipping_rate_price; ?>" />
				</td>
				<td style="text-align: center;">
				    <input type="text" name="weight_start[<?php echo $item->shipping_rate_id; ?>]" value="<?php echo $item->shipping_rate_weight_start; ?>" />
				    <?php echo JText::_('COM_CITRUSCART_TO'); ?>
				    <input type="text" name="weight_end[<?php echo $item->shipping_rate_id; ?>]" value="<?php echo $item->shipping_rate_weight_end; ?>" />
				</td>
				<td style="text-align: center;">
					<input type="text" name="handling[<?php echo $item->shipping_rate_id; ?>]" value="<?php echo $item->shipping_rate_handling; ?>" />
				</td>
				<td style="text-align: center;">
					[<a href="<?php echo $baseLink; ?>&shippingTask=deleterate&cid[]=<?php echo $item->shipping_rate_id; ?>&return=<?php echo base64_encode($baseLink."&shippingTask=setrates&sid={$row->shipping_method_id}&tmpl=component"); ?>">
						<?php echo JText::_('COM_CITRUSCART_DELETE_RATE'); ?>
					</a>
					]
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
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="sid" value="<?php echo $row->shipping_method_id; ?>" />
	<input type="hidden" name="shippingTask" id="shippingTask" value="setrates" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>