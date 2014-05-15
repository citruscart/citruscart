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
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>
<?php $app = JFactory::getApplication(); ?>
                            
<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_('COM_CITRUSCART_SET_VALUES_FOR'); ?>: <?php echo $row->productattributeoption_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( $app->input->getString('view') ); ?>

	<div class="note" style="width: 96%; margin-left: auto; margin-right: auto;">
	
	    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_ADD_A_NEW_ATTRIBUTE_OPTION_VALUE'); ?></div>

	    <div class="reset"></div>
	    
                <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th style="width: 15px;"><?php echo JText::_('COM_CITRUSCART_FIELD'); ?></th>
                     <th><?php echo JText::_('COM_CITRUSCART_OPERATOR'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_VALUE'); ?></th>
                    <th></th>
                    
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?php echo JText::_('COM_CITRUSCART_COMPLETE_THIS_FORM_TO_ADD_A_NEW_OPTION_VALUE'); ?>:
                    </td>
                    <td>
                        <?php echo CitruscartSelect::productattributeoptionvaluefield( "product_full_image", 'createproductattributeoptionvalue_field' ); ?>
                    </td>
                    <td>
                        <?php echo CitruscartSelect::productattributeoptionvalueoperator( "replace", 'createproductattributeoptionvalue_operator' ); ?>
                    </td>
                    <td>
                        <input type="text" id="createproductattributeoptionvalue_value" name="createproductattributeoptionvalue_value" value="" />
                    </td>
                    <td>
                        <button class="btn btn-primary" onclick="document.getElementById('task').value='createattributeoptionvalue'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_CREATE_VALUE'); ?></button>
                    </td>
                </tr>
                </tbody>
                </table>
                
	</div>

<div class="note_green" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_CURRENT_ATTRIBUTE_OPTION_VALUES'); ?></div>
    <div style="float: right;">
        <button class="btn btn-success" onclick="document.getElementById('task').value='saveattributeoptionvalues'; document.adminForm.toggle.checked=true; checkAll(<?php echo count( @$items ); ?>); document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES'); ?></button>
    </div>
    <div class="reset"></div>
        
	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 20px;">
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                </th>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_FIELD', "tbl.productattributeoptionvalue_field", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_OPERATOR', "tbl.productattributeoptionvalue_operator", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_VALUE', "tbl.productattributeoptionvalue_value", $state->direction, $state->order ); ?>
                </th>
				<th style="width: 100px;">
				</th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'productattributeoptionvalue_id' ); ?>
				</td>
				<td style="text-align: left;">
					<?php echo CitruscartSelect::productattributeoptionvaluefield( $item->productattributeoptionvalue_field, "field[{$item->productattributeoptionvalue_id}]" ); ?>
				</td>
                <td style="text-align: center;">
                    <?php echo CitruscartSelect::productattributeoptionvalueoperator( $item->productattributeoptionvalue_operator, "operator[{$item->productattributeoptionvalue_id}]" ); ?>
                </td>
                <td style="text-align: center;">
                    <input type="text" name="value[<?php echo $item->productattributeoptionvalue_id; ?>]" value="<?php echo $item->productattributeoptionvalue_value; ?>" size="10" />
                </td>
				<td style="text-align: center;">
					[<a href="index.php?option=com_citruscart&controller=productattributeoptionvalues&task=delete&cid[]=<?php echo $item->productattributeoptionvalue_id; ?>&return=<?php echo base64_encode("index.php?option=com_citruscart&controller=products&task=setattributeoptionvalues&id={$row->productattributeoption_id}&tmpl=component"); ?>">
						<?php echo JText::_('COM_CITRUSCART_DELETE_VALUE'); ?>	
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
</div>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="<?php echo $row->productattributeoption_id; ?>" />
	<input type="hidden" name="task" id="task" value="setattributeoptionvalues" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
	
</form>