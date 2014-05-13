<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>
<?php $app = JFactory::getApplication();?>

<h1><?php echo JText::_('COM_CITRUSCART_SET_OPTIONS_FOR'); ?>: <?php echo $row->productattribute_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( $app->input->getString('view') ); ?>

	<div class="note" style="width: 96%; margin-left: auto; margin-right: auto;">

	    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_ADD_A_NEW_ATTRIBUTE_OPTION'); ?></div>

	    <div class="reset"></div>

                <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th><?php echo JText::_('COM_CITRUSCART_NAME'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_PRICE_PREFIX'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_PRICE'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_WEIGHT_PREFIX'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_WEIGHT'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_CODE'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_IS_BLANK'); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?php echo JText::_('COM_CITRUSCART_COMPLETE_THIS_FORM_TO_ADD_A_NEW_OPTION'); ?>:
                    </td>
                    <td>
                        <input type="text" id="createproductattributeoption_name" name="createproductattributeoption_name" class="input-mini" />
                    </td>
                    <td>
                        <?php echo CitruscartSelect::productattributeoptionprefix( "+", 'createproductattributeoption_prefix' ,array('class'=>'input-mini')); ?>
                    </td>
                    <td>
                        <input type="text" id="createproductattributeoption_price" name="createproductattributeoption_price" class="input-mini"/>
                    </td>
                    <td>
                        <?php echo CitruscartSelect::productattributeoptionprefix( "+", 'createproductattributeoption_prefix_weight',array('class'=>'input-mini') ); ?>
                    </td>
                    <td>
                        <input type="text" id="createproductattributeoption_weight" name="createproductattributeoption_weight" class="input-mini"/>
                    </td>
                    <td>
                        <input type="text" id="createproductattributeoption_code" name="createproductattributeoption_code"  class="input-mini" />
                    </td>
                    <td>
	                    <?php echo CitruscartSelect::booleans( 0, 'createproductattributeoption_blank', array('class' => 'input-mini', 'size' => '1'), null, false, 'Select State', 'Yes', 'No' );?>
                    </td>
                    <td>
                        <button class="btn btn-primary" onclick="document.getElementById('task').value='createattributeoption'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_CREATE_OPTION'); ?></button>
                    </td>
                </tr>
                </tbody>
                </table>

	</div>

<div class="note_green">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_CURRENT_ATTRIBUTE_OPTIONS'); ?></div>
    <div style="float: right;">
        <button class="btn btn-success" onclick="document.getElementById('task').value='saveattributeoptions'; document.adminForm.toggle.checked=true; checkAll(<?php echo count( $items ); ?>); document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES'); ?></button>
    </div>
    <div class="reset"></div>

	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th>
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_OPTION', "tbl.productattributeoption_name", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE_PREFIX', "tbl.productattributeoption_prefix", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "tbl.productattributeoption_price", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_WEIGHT_PREFIX', "tbl.productattributeoption_prefix_weight", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_WEIGHT', "tbl.productattributeoption_weight", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_CODE', "tbl.productattributeoption_code", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PARENT_OPTION', "tbl.parent_productattributeoption_id", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_IS_BLANK'); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ORDER', "tbl.ordering", $state->direction, $state->order ); ?>
                </th>
                <th>
				</th>
				<th>
				</th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td>
					<?php echo CitruscartGrid::checkedout( $item, $i, 'productattributeoption_id' ); ?>
				</td>
				<td>
					<input type="text" class="input-mini"  name="name[<?php echo $item->productattributeoption_id; ?>]" value="<?php echo $item->productattributeoption_name; ?>" />
				</td>
                <td>
                    <?php echo CitruscartSelect::productattributeoptionprefix( $item->productattributeoption_prefix, "prefix[{$item->productattributeoption_id}]",array('class'=>'span1') ); ?>
                </td>
                <td>
                    <input type="text" name="price[<?php echo $item->productattributeoption_id; ?>]" value="<?php echo $item->productattributeoption_price; ?>" class="input-mini" />
                </td>
                <td>
                    <?php echo CitruscartSelect::productattributeoptionprefix( $item->productattributeoption_prefix_weight, "prefix_weight[{$item->productattributeoption_id}]",array('class'=>'span1') ); ?>
                </td>
                <td>
                    <input type="text" name="weight[<?php echo $item->productattributeoption_id; ?>]" value="<?php echo $item->productattributeoption_weight; ?>" class="input-mini" />
                </td>
                <td>
                    <input type="text" name="code[<?php echo $item->productattributeoption_id; ?>]" value="<?php echo $item->productattributeoption_code; ?>" class="input-mini" />
                </td>
                <td>
					<?php
					if($item->parent_productattributeoption_id)
					{
						Citruscart::load('CitruscartTableProductAttributeOptions', 'tables.productattributeoptions');
						$opt = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
						$opt->load($item->parent_productattributeoption_id);
						$attribute_id = $opt->productattribute_id;
					}
					else
					{
						$attribute_id = 0;
					}
						echo CitruscartSelect::productattributes($attribute_id, $row->product_id, $item->productattributeoption_id, array('class' => 'input-mini'), null, $allowAny = true, $title = 'COM_CITRUSCART_NO_PARENT');
					?>
					<div id="parent_option_select_<?php echo $item->productattributeoption_id; ?>">
					<?php
						if($item->parent_productattributeoption_id)
						{
							echo CitruscartSelect::productattributeoptions($attribute_id, $item->parent_productattributeoption_id, 'parent['.$item->productattributeoption_id.']',array('class'=>'input-mini'));
						}
					?>
					</div>
				</td>
        <td>
	      	<?php echo CitruscartSelect::booleans( $item->is_blank, 'blank['.$item->productattributeoption_id.']', array('class' => 'input-mini'), null, false, 'Select State', 'Yes', 'No' );?>
				</td>
				<td>
					<input type="text" class="span1" name="ordering[<?php echo $item->productattributeoption_id; ?>]" value="<?php echo $item->ordering; ?>" class="input-mini" />
				</td>
				<td>
					[<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=products&task=setattributeoptionvalues&id=".$item->productattributeoption_id."&tmpl=component", JText::_('COM_CITRUSCART_SET_VALUES') ); ?>]
				</td>
				<td>[<a  href="index.php?option=com_citruscart&controller=productattributeoptions&task=delete&cid[]=<?php echo $item->productattributeoption_id; ?>&return=<?php echo base64_encode("index.php?option=com_citruscart&controller=products&task=setattributeoptions&id={$row->productattribute_id}&tmpl=component"); ?>">
						<small><?php echo JText::_('COM_CITRUSCART_DELETE_OPTION'); ?></small>
					</a>]
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
</div>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="<?php echo $row->productattribute_id; ?>" />
	<input type="hidden" name="task" id="task" value="setattributeoptions" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>

</form>