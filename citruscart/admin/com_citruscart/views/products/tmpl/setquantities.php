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
$app = JFactory::getApplication();
$view = $app->input->getString('view');
?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php JHTML::_('script', 'core.js', 'media/system/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row;

?>

<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_('COM_CITRUSCART_SET_QUANTITIES_FOR'); ?>: <?php echo $row->product_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <?php echo CitruscartGrid::pagetooltip( $view ); ?>

<div class="note_green" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_CURRENT_QUANTITIES'); ?></div>
    <div style="float: right;">
        <button class="btn btn-success" onclick="document.adminForm.toggle.checked=true; Joomla.checkAll(<?php echo count( $items ); ?>); document.getElementById('task').value='savequantities'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES'); ?></button>
    </div>
    <div class="reset"></div>
    <table class="table table-striped table-bordered" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 20px;">
                    #
                </th>
                <th style="width: 20px;">
                <?php echo JHtmlGrid::checkall 	($name = 'cid',$tip = 'JGLOBAL_CHECK_ALL', 	$action = 'Joomla.checkAll(this)'); 		?>
                    <!-- <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(<?php echo count( $items ); ?>);" /> -->
                </th>
                <th style="text-align: left;">
                    <?php echo JText::_('COM_CITRUSCART_ATTRIBUTE_NAMES'); ?>
                </th>
                <th style="text-align: center;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_QUANTITY', "tbl.quantity", $state->direction, $state->order ); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td style="text-align: center;">
                    <?php echo $this->pagination->limitstart + $i + 1; ?>
                </td>
                <td style="text-align: center;">
                	<?php echo  $checked = JHTML::_('grid.id', $i, $item->productquantity_id);?>
                	<input type="hidden" name="cid[]" value="<?php echo $item->productquantity_id;?>"/>
                    <?php //echo CitruscartGrid::checkedout( $item, $i, 'productquantity_id' ); ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $item->product_attribute_names; ?>
                </td>
                <td style="text-align: center;">
                    <input type="text" name="quantity[<?php echo $item->productquantity_id; ?>]" value="<?php echo $item->quantity; ?>" />
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
    <input type="hidden" name="id" value="<?php echo (($row->product_id)) ? $row->product_id : $app->input->getInt('id'); ?>" />
    <input type="hidden" name="task" id="task" value="setquantities" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
    <input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

    <?php echo $this->form['validate']; ?>
</div>
</form>