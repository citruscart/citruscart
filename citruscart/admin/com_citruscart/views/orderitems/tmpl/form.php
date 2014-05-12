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
defined('_JEXEC') or die('Restricted access');?>
<?php $form = $this->form; ?>
<?php $row = $this->row; JFilterOutput::objectHTMLSafe( $row );
?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >

<table style="width: 100%;">
<tr>
    <td style="width: 65%; vertical-align: top;">

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onBeforeDisplayOrderitemForm', array( $row ) );
    ?>


			<table class="table table-striped table-bordered">
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?>:
                    </td>
                    <td>
                        <input name="order_id" value="<?php echo $row->order_id; ?>" size="48" maxlength="250" type="text" />
                        <?php if (!empty($row->order_id)) { ?>
                        <br/>
                        <a href="index.php?option=com_citruscart&view=orders&task=view&id=<?php echo $row->order_id; ?>"><?php echo JText::_('COM_CITRUSCART_VIEW_ORDER'); ?></a>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_PRODUCT_ID'); ?>:
                    </td>
                    <td>
                        <input name="product_id" value="<?php echo $row->product_id; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERITEM_NAME'); ?>:
                    </td>
                    <td>
                        <input name="orderitem_name" value="<?php echo $row->orderitem_name; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERITEM_SKU'); ?>:
                    </td>
                    <td>
                        <input name="orderitem_sku" value="<?php echo $row->orderitem_sku; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERITEM_QUANTITY'); ?>:
                    </td>
                    <td>
                        <input name="orderitem_quantity" value="<?php echo $row->orderitem_quantity; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERITEM_FINAL_PRICE'); ?>:
                    </td>
                    <td>
                        <input name="orderitem_final_price" value="<?php echo $row->orderitem_final_price; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
			</table>


    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplayOrderitemFormLeftColumn', array( $row ) );
    ?>

    </td>
    <td style="width: 35%; vertical-align: top;">

        <?php
            // fire plugin event here to enable extending the form
            JDispatcher::getInstance()->trigger('onBeforeDisplayOrderitemFormRightColumn', array( $row ) );
        ?>

        <?php
            // fire plugin event here to enable extending the form
            JDispatcher::getInstance()->trigger('onAfterDisplayOrderitemFormRightColumn', array( $row ) );
        ?>
    </td>
</tr>
</table>

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplayOrderitemForm', array( $row ) );
    ?>

    <input type="hidden" name="id" value="<?php echo $row->orderitem_id; ?>" />
    <input type="hidden" name="task" value="" />
</form>