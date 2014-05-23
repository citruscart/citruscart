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

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>
<div class="lightbox-select">
    <form action="<?php echo JRoute::_( $form['action'] )?>" method="post" id="adminForm" name="adminFormSearch" enctype="multipart/form-data" class="dsc-wrap">
        <h1 class="pull-left"><?php echo JText::_('COM_CITRUSCART_SELECT_PRODUCTS_FOR'); ?>: <?php echo $row->category_name; ?></h1>

        <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>
        <input type="hidden" name="task" value="selectproducts" />
    </form>
</div>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data" class="dsc-wrap dsc-clear">

<div class="note_green lightbox-select">

    <p class="dsc-wrap">
        <?php echo JText::_('COM_CITRUSCART_FOR_CHECKED_ITEMS'); ?>:
        <button class="btn btn-success" onclick="document.getElementById('task').value='selected_switch'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_CHANGE_STATUS'); ?></button>
    </p>

	<table class="table table-striped table-bordered dsc-clear">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.product_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.product_name", $state->direction, $state->order ); ?>
                </th>
                <th>
	                <?php echo JText::_('COM_CITRUSCART_STATUS'); ?>
                </th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'product_id' ); ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->product_id; ?>
				</td>
				<td style="text-align: left;">
					<?php echo $item->product_name; ?>
				</td>
				<td style="text-align: center;">
					<?php $table = JTable::getInstance('ProductCategories', 'CitruscartTable'); ?>
					<?php
                    $keynames = array();
                    $keynames['product_id'] = $item->product_id;
                    $keynames['category_id'] = $row->category_id;
					?>
					<?php $table->load( $keynames ); ?>
					<?php echo CitruscartGrid::enable(isset($table->product_id), $i, 'selected_'); ?>
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
					<?php echo $this->pagination->getListFooter();
					?>
					 <div class="pagination pagination-toolbar">
                    	<?php echo $this->pagination->getPagesLinks(); ?>
                	</div>
				</td>
				</tr>
		</tfoot>
	</table>
	<input type="hidden" name="limit" value="<?php echo $this->state->limit;?>" />

	<input type="hidden" name="limitstart" value="<?php echo $this->state->limitstart;?>" />

	<input type="hidden" name="task" id="task" value="selectproducts" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />
	<input type="hidden" name="filter" value="<?php echo $state->filter; ?>" />

	<?php echo $this->form['validate']; ?>
</div>
</form>