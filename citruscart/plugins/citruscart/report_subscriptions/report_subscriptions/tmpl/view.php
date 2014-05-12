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
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $state = $vars->state; ?>
<?php $items = $vars->items; ?>

<h2><?php echo JText::_('COM_CITRUSCART_RESULTS'); ?></h2>

    <table class="adminlist" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo JText::_('COM_CITRUSCART_ID'); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo JText::_('COM_CITRUSCART_PRODUCT_NAME'); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_CREATED'); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_EXPIRES'); ?>
                </th>

                <th style="text-align: left;">
                    <?php echo JText::_('COM_CITRUSCART_USER'); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_ORDER_STATE'); ?>
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
                    <?php echo $item->subscription_id; ?>
                </td>
                <td style="text-align: left;">
                	<?php // Also product ID, [in brackets] ?>
                    <?php echo "[" . $item->product_id . "] " . JText::_($item->product_name); ?>
                </td>
                <td style="text-align: center;">
                    <?php // Price of subscription ?>
                    <?php echo CitruscartHelperBase::currency( $item->orderitem_final_price ); ?>
                </td>
                <td style="text-align: center;">
                	<?php // JHTML created date ?>
                    <?php echo JHTML::_('date', $item->created_date, Citruscart::getInstance()->get('date_format')); ?>
                </td>
                <td style="text-align: center;">
                    <?php // JHTML expires date ?>
                    <?php echo JHTML::_('date', $item->expires_datetime, Citruscart::getInstance()->get('date_format')); ?>
                </td>
                <td style="text-align: left;">
                	<?php // Also more details on user, such as email and full name ?>
                    <?php echo $item->user_name . ", " . $item->user_username . ", " . "<a href=\"mailto:" . $item->email . "\">" . $item->email . "</a>"; ?>
                </td>
                <td style="text-align: center;">
                    <?php // Order id ?>
                    <?php echo $item->order_id; ?>
                </td>
                <td style="text-align: center;">
                    <?php // Order state ?>
                    <?php
                    	$orderstate = JTable::getInstance('Orderstates', 'CitruscartTable');
            			$orderstate->load( $item->order_state_id);

                    	echo $orderstate->order_state_name;
                    ?>
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
