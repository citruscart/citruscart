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
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php $state = $this->state; ?>
<?php $form = $this->form;
?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>



<h1><?php echo JText::_('COM_CITRUSCART_SET_PRICES_FOR'); ?>: <?php echo $row->product_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( $app->input->get('view') ); ?>

<div class="note" style="width: 96%; margin-left: auto; margin-right: auto; margin-bottom: 20px;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_ADD_A_NEW_PRICE'); ?></div>
    <div style="float: right;">
        <button class="btn btn-primary" onclick="document.getElementById('task').value='createprice'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_CREATE_PRICE'); ?></button>
    </div>
    <div class="reset"></div>
	<table class="table table-striped table-bordered">
    	<thead>
        	<tr>
        		<th><?php echo JText::_('COM_CITRUSCART_DATE_RANGE'); ?></th>
        		<th><?php echo JText::_('COM_CITRUSCART_QUANTITY_RANGE'); ?></th>
        		<th><?php echo JText::_('COM_CITRUSCART_GROUP'); ?></th>
        		<th><?php echo JText::_('COM_CITRUSCART_PRICE'); ?></th>
        	</tr>
        	</thead>
        	<tbody>
        	<tr>
        		<td style="text-align: center;">
            		<?php echo JHTML::calendar( "", "createprice_date_start", "createprice_date_start", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small') ); ?>
            		<?php echo JText::_('COM_CITRUSCART_TO'); ?>
            		<?php echo JHTML::calendar( "", "createprice_date_end", "createprice_date_end", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small') ); ?>
        		</td>
        		<td style="text-align: center;">
        			<input type="text" id="createprice_quantity_start" name="createprice_quantity_start" value="" class="input-small" />
        			<?php echo JText::_('COM_CITRUSCART_TO'); ?>
            		<input type="text" id="createprice_quantity_end" name="createprice_quantity_end" value="" class="input-small" />
            	</td>
            	<td style="text-align: center;">
        			<?php echo CitruscartSelect::groups('', 'createprice_group_id'); ?>
        		</td>
        		<td style="text-align: center;">
        			<input type="text" id="createprice_price" name="createprice_price" value="" class="input-small" />
        		</td>
        	</tr>
    	</tbody>
	</table>
</div>

<div class="note_green" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_CURRENT_PRICES'); ?></div>
    <div style="float: right;">
        <button class="btn btn-success" onclick="document.adminForm.toggle.checked=true;Joomla.checkAll(<?php echo count( $items ); ?>); document.getElementById('task').value='saveprices'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES'); ?></button>
    </div>
    <div class="reset"></div>
	<table class="table table-striped table-bordered">
		<thead>
            <tr>
                <th style="width: 20px;">
                	<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "tbl.product_price", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_DATE_RANGE', "tbl.product_price_startdate", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_QUANTITY_RANGE', "tbl.price_quantity_start", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_GROUP', "tbl.group_id", $state->direction, $state->order ); ?>
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
				<?php

					echo CitruscartGrid::checkedout( $item, $i, 'product_price_id' );
				?>
				<input type="hidden" name="cid[]" value ="<?php echo $item->product_price_id;?>" />
				</td>
				<td style="text-align: center;">
					<input type="text" name="price[<?php echo $item->product_price_id; ?>]" value="<?php echo $item->product_price; ?>" class="input-small" />
				</td>
				<td style="text-align: center;">
                	<?php echo JHTML::calendar( $item->product_price_startdate, "date_start[{$item->product_price_id}]", "date_start_{$item->product_price_id}", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small') ); ?>
                	<?php echo JText::_('COM_CITRUSCART_TO'); ?>
                	<?php echo JHTML::calendar( $item->product_price_enddate, "date_end[{$item->product_price_id}]", "date_end_{$item->product_price_id}", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small') ); ?>
				</td>
				<td style="text-align: center;">
					<input type="text" name="quantity_start[<?php echo $item->product_price_id; ?>]" value="<?php echo $item->price_quantity_start; ?>" class="input-small"/>
					<?php echo JText::_('COM_CITRUSCART_TO'); ?>
					<input type="text" name="quantity_end[<?php echo $item->product_price_id; ?>]" value="<?php echo $item->price_quantity_end; ?>" class="input-small" />
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartSelect::groups($item->group_id, "price_group_id[{$item->product_price_id}]",array('class'=>'input-small')); ?>
				</td>
				<td style="text-align: center;">
					[<a href="index.php?option=com_citruscart&controller=productprices&task=delete&cid[]=<?php echo $item->product_price_id; ?>&return=<?php echo base64_encode("index.php?option=com_citruscart&controller=products&task=setprices&id={$row->product_id}&tmpl=component"); ?>">
						<?php echo JText::_('COM_CITRUSCART_DELETE_PRICE'); ?>
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
	<input type="hidden" name="id" value="<?php echo $app->input->getInt('id'); ?>" />
	<input type="hidden" name="task" id="task" value="setprices" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />
	<?php echo $this->form['validate']; ?>
</div>
</form>