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
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $state = $vars->state; ?>
<?php $items = $vars->items; ?>
<div class="span7">
<h3><?php echo JText::_('COM_CITRUSCART_RESULTS'); ?></h3>

    <table class="table table-striped table-bordered" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="text-align: center;">
                    <?php echo JText::_('COM_CITRUSCART_MANUFACTURER_NAME'); ?>
                </th>
                <th style="text-align: center; width : 200px;">
                    <?php echo JText::_('COM_CITRUSCART_COUNT_OF_ITEMS'); ?>
                </th>
                <th style="text-align: center; width : 200px;">
                    <?php echo JText::_('COM_CITRUSCART_SALES_AMOUNT'); ?>
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
        <?php $i=1; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td align="center">
                    <?php echo $i++; ?>
                </td>
                <td style="text-align: left;">
                    <?php if( strlen( $item->manufacturer_name ) ) echo $item->manufacturer_name; else echo ' - '.JText::_('COM_CITRUSCART_NO_MANUFACTURER').' - ';?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->count_items; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo CitruscartHelperBase::currency( $item->price_total );?>
                </td>
            </tr>
            <?php $k = (1 - $k); ?>
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
</div>
</div>