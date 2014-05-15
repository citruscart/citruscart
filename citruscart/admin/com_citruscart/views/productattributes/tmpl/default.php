<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>
<?php Citruscart::load( 'CitruscartUrl', 'library.url' ); ?>
<?php $app = JFactory::getApplication();?>

<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_('COM_CITRUSCART_SET_ATTRIBUTES_FOR'); ?>: <?php echo $row->product_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( $app->input->getString('view') ); ?>

<div class="note" style="width: 96%; margin-left: auto; margin-right: auto;">

    <!-- <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_ADD_A_NEW_ATTRIBUTE'); ?></div> -->
	<!-- <div style="float: right;">
        <button class="btn btn-primary" onclick="document.getElementById('task').value='createattribute'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_CREATE_ATTRIBUTE'); ?></button>
    </div> -->
    <div class="reset"></div>

	<table class="table table-striped table-bordered">
    	<thead>
    	<tr>
    		<th><?php echo JText::_('COM_CITRUSCART_ADD_A_NEW_ATTRIBUTE'); ?></th>
    	</tr>
    	</thead>
	    	<tbody>
		    	<tr>
			    	<td colspan="3">
			    		<?php echo JText::_('COM_CITRUSCART_ATTRIBUTE_NAME'); ?>
						<input type="text" id="createproductattribute_name" name="createproductattribute_name" value="" />
						<button class="btn btn-primary" onclick="document.getElementById('task').value='createattribute'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_CREATE_ATTRIBUTE'); ?></button>
		    		</td>
			    </tr>
	    	</tbody>
	</table>
</div>

<div class="note_green" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_CURRENT_ATTRIBUTES'); ?></div>
    <div style="float: right;">
        <button class="btn btn-success" onclick="document.getElementById('task').value='saveattributes'; document.adminForm.toggle.checked=true; checkAll(<?php echo count( $items ); ?>); document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES'); ?></button>
    </div>
    <div class="reset"></div>

	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th>
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ATTRIBUTE', "tbl.productattribute_name", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PARENT_OPTION', "tbl.parent_productattributeoption_id", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ORDER', "tbl.ordering", $state->direction, $state->order ); ?>
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
					<?php echo CitruscartGrid::checkedout( $item, $i, 'productattribute_id' ); ?>
				</td>
				<td style="text-align: left;">
					<input type="text" name="name[<?php echo $item->productattribute_id; ?>]" value="<?php echo $item->productattribute_name; ?>" />
					[<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=products&task=setattributeoptions&id=".$item->productattribute_id."&tmpl=component", JText::_('COM_CITRUSCART_SET_ATTRIBUTE_OPTIONS') ); ?>]
				</td>
				<td style="text-align: left;">
					<?php
					if($item->parent_productattributeoption_id)
					{
						Citruscart::load('CitruscartTableProductAttributeOptions', 'tables.productattributeoptions');
						$opt = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
						$opt->load($item->parent_productattributeoption_id);
						$attribute_id = $opt->productattribute_id;
					}else{
						$attribute_id = 0;
					}
					echo CitruscartSelect::productattributes($attribute_id, $row->product_id, $item->productattribute_id, array('class' => 'inputbox', 'size' => '1'), null, $allowAny = true, $title = 'COM_CITRUSCART_NO_PARENT');

					?>

					<div id="parent_option_select_<?php echo $item->productattribute_id; ?>">

					<?php

					if($item->parent_productattributeoption_id)
					{
						echo CitruscartSelect::productattributeoptions($attribute_id, $item->parent_productattributeoption_id, 'parent['.$item->productattribute_id.']');
					}

					?>

					</div>
				</td>
				<td style="text-align: center;">
					<input type="text" name="ordering[<?php echo $item->productattribute_id; ?>]" value="<?php echo $item->ordering; ?>" size="10" class="input-tiny" />
				</td>
				<td style="text-align: center;">
					[<a href="index.php?option=com_citruscart&controller=productattributes&task=delete&cid[]=<?php echo $item->productattribute_id; ?>&return=<?php echo base64_encode("index.php?option=com_citruscart&controller=products&task=setattributes&id={$row->product_id}&tmpl=component"); ?>">
						<?php echo JText::_('COM_CITRUSCART_DELETE_ATTRIBUTE'); ?>
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
	<input type="hidden" name="id" value="<?php echo $row->product_id; ?>" />
	<input type="hidden" name="task" id="task" value="setattributes" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</div>
</form>