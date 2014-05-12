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
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $state = $vars->state; ?>
<?php $items = $vars->items; ?>

    <table class="adminlist table table-bordered table-striped" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo JText::_('COM_CITRUSCART_PRODUCT_NAME'); ?>
                </th>
                <th style="text-align: center; width : 200px;">
                    <?php echo JText::_('COM_CITRUSCART_MANUFACTURER'); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo JText::_('COM_CITRUSCART_ATTRIBUTES'); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="20">

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
                <td style="text-align: left;">
                    <?php echo "[" . $item->product_id . "] " . JText::_($item->product_name); ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $item->manufacturer_name; ?>
                </td>
                <td style="text-align: left;">
                    <?php echo JText::_($item->orderitem_attribute_names); ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->total_quantity;?>
                </td>
            </tr>
            <?php ++$i; $k = (1 - $k); ?>
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
