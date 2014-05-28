<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php $state = $vars->state; ?>
<?php $items = $vars->items; ?>

    <table class="adminlist table table-bordered table-striped" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo JText::_('COM_CITRUSCART_ID'); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo JText::_('COM_CITRUSCART_NAME'); ?>
                </th>
                <th style="width: 70px;">
                    <?php echo JText::_('COM_CITRUSCART_MODEL'); ?>
                </th>
                <th style="width: 70px;">
                    <?php echo JText::_('COM_CITRUSCART_SKU'); ?>
                </th>
                <th style="width: 50px;">
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
                <td style="text-align: center;">
                        <?php echo $item->product_id; ?>
                </td>
                <td style="text-align: left;">
                	<a href="index.php?option=com_citruscart&view=products&task=edit&id=<?php echo $item->product_id; ?>">
                        <?php echo JText::_($item->product_name); ?>
                    </a>
                      <?php if($item->product_quantity < 1) echo '<span style="color: #F00; font-style: italic; margin-left: 40px;">'.JText::_('COM_CITRUSCART_OUT_OF_STOCK').'</span>';?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->product_model; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->product_sku; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->product_quantity; ?>
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
