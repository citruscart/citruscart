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
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php Citruscart::load( 'CitruscartHelperBase', 'helpers._base' ); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( JFactory::getApplication()->input->getString('view')  ); ?>

    <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>


	<table class="table table-striped table-bordered">
		<thead>
            <tr>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th>
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.orderitem_id", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_DATE', "o.created_date", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ORDER', "tbl.order_id", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ITEM', "tbl.orderitem_name", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_QUANTITY', "tbl.orderitem_quantity", $state->direction, $state->order ); ?>
                </th>
                <th>
    	            <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "tbl.orderitem_price", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_STATUS', "tbl.orderitem_status", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PAYMENT_STATUS', "op.transaction_status", $state->direction, $state->order ); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
	                <?php $attribs = array('class' => 'input-small', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                	<div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>"  class="input input-tiny" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>"  class="input input-tiny" />
                        </div>
                    </div>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S' ); ?>
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S' ); ?>
                        </div>
                    </div>
                </th>
                <th>
                    <input name="filter_orderid" value="<?php echo $state->filter_orderid; ?>" class="input-small"/>
                </th>
                <th style="text-align: left;">
                	<input name="filter_product_name" value="<?php echo $state->filter_product_name; ?>" class="input-small"/>
                </th>
                <th>
					<!--
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span> <input id="filter_total_from" name="filter_total_from" value="<?php echo $state->filter_total_from; ?>" size="5" class="input" />
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span> <input id="filter_total_to" name="filter_total_to" value="<?php echo $state->filter_total_to; ?>" size="5" class="input" />
                        </div>
                    </div>-->

                </th>
                <th>
                    <!-- <input name="filter_price" value="<?php echo $state->filter_price; ?>" size="25"/>-->
                </th>
                <th>
                    <!-- <input name="filter_status" value="<?php echo $state->filter_status; ?>" size="25"/>-->
                </th>
                <th>
                	<input name="filter_paymentstatus" value="<?php echo $state->filter_paymentstatus; ?>" class="input-small"/>
                </th>
            </tr>
			<tr>
				<th colspan="20" style="font-weight: normal;">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					<div style="float: left;"><?php echo $this->pagination->getListFooter(); ?></div>
				</th>
			</tr>
		</thead>
		</table>
        <table class="table table-striped table-bordered">
		<tfoot>
			<tr>
				<td colspan="20">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					<?php echo $this->pagination->getPagesLinks(); ?>
				</td>
			</tr>
		</tfoot>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'orderitem_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->orderitem_id; ?>
					</a>
				</td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link; ?>">
                        <?php echo JHTML::_('date', $item->created_date, Citruscart::getInstance()->get('date_format')); ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link; ?>">
                        <?php echo $item->order_id; ?>
                    </a>
                    <br/>
                    <?php echo $item->order_state_name; ?>
                    <br/>
                    <?php echo $item->user_name .' [ '.$item->user_id.' ]'; ?>
                </td>
				<td style="text-align: left;">
                    <?php echo $item->orderitem_name .' [ '.$item->product_id.' ]'; ?>
                    <?php if (!empty($item->orderitem_sku)) { ?>
                        <br/>
                        <b><?php echo JText::_('COM_CITRUSCART_SKU'); ?></b>: <?php echo $item->orderitem_sku; ?>
                    <?php } ?>
				</td>
                <td style="text-align: center;">
                    <?php echo $item->orderitem_quantity; ?>
                </td>
				<td style="text-align: center;">
				    <?php echo CitruscartHelperBase::currency( $item->orderitem_price ); ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->orderitem_status; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->transaction_status; ?>
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
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>
