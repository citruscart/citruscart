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

 ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>

<?php
if (JRequest::getVar('windowtask') == 'close')
{
?>
    <script type="text/javascript">
    window.parent.CitruscartAddProductsToOrder();
    </script>
<?php
}
?>

<div class="note" style="width: 95%; text-align: center; margin-left: auto; margin-right: auto;">
    <button class="btn btn-success" onclick="document.getElementById('task').value='addproducts'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_ADD_SELECTED_PRODUCTS_TO_ORDER'); ?></button>
</div>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm"  enctype="multipart/form-data">

    <table>
        <tr>
            <td align="left" width="100%">
                <input type="text" name="filter" value="<?php echo $state->filter; ?>" />
                <button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_SEARCH'); ?></button>
                <button class="btn btn-danger" onclick="CitruscartFormReset(this.form);"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
            </td>
            <td nowrap="nowrap">
                <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                <?php echo CitruscartSelect::category( $state->filter_category, 'filter_category', $attribs, 'category', true ); ?>
            </td>
        </tr>
    </table>

    <table class="table table-striped table-bordered" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="width: 50px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.product_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.product_name", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "price", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
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
                    <?php echo CitruscartHelperBase::currency( $item->price ); ?>
                </td>
                <td style="text-align: center;">
                    <input name="quantity[<?php echo $item->product_id; ?>]" type="text" value="1" style="width: 30px;" />
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

    <input type="hidden" name="task" id="task" value="selectproducts" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
    <input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

    <?php echo $this->form['validate']; ?>
</form>