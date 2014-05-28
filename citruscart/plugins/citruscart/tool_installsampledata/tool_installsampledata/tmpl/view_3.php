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
<?php $items = $vars->results; ?>
<p><?php echo JText::_('COM_CITRUSCART_THIS_TOOL_INSTALL_SAMPLE_DATA_TO_CITRUSCART'); ?></p>

    <div class="note">
        <span style="float: right; font-size: large; font-weight: bold;"><?php echo JText::_('COM_CITRUSCART_FINAL'); ?></span>
        <p><?php echo JText::_('COM_CITRUSCART_INSTALLATION_RESULTS'); ?></p>
    </div>

    <table class="adminlist" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="text-align: center;">
                    <?php echo JText::_('COM_CITRUSCART_DATA'); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo JText::_('COM_CITRUSCART_AFFECTED_ROWS'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_CITRUSCART_ERRORS'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php $i=0; $k=0; ?>
        <?php $data = array('Manufacturer', 'Category', 'Product');?>
        <?php foreach ($items as $item=>$results) : ?>
			<?php foreach ($results as $result) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td align="center">
                    <?php echo $i + 1; ?>
                </td>
                <td style="text-align: center; width:50px;">
                        <?php echo JText::_('COM_CITRUSCART_COM_CITRUSCART_TABLE'); ?> <?php echo $i + 1; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $result->affectedRows; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $result->error ? $result->error : "-"; ?>
                </td>
            </tr>
			<?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
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