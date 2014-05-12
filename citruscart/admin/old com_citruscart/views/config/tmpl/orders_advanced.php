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
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_FORCE_SSL_ON_CHECKOUT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('force_ssl_checkout', '' , $this -> row -> get('force_ssl_checkout', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_INITIAL_ORDER_STATE'); ?>
            </th>
            <td><?php echo CitruscartSelect::orderstate($this -> row -> get('initial_order_state', '15'), 'initial_order_state'); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_INITIAL_ORDER_STATE_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_PENDING_ORDER_STATE'); ?>
            </th>
            <td><?php echo CitruscartSelect::orderstate($this -> row -> get('pending_order_state', '1'), 'pending_order_state'); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_PENDING_ORDER_STATE_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ORDER_NUMBER_PREFIX'); ?>
            </th>
            <td><input type="text" name="order_number_prefix" value="<?php echo $this -> row -> get('order_number_prefix', ''); ?>" class="inputbox" size="10" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ORDER_NUMBER_PREFIX_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_POS_CLEAN_REQUESTS_HOURS'); ?>
            </th>
            <td><input type="text" name="pos_request_clean_hours" value="<?php echo $this -> row -> get('pos_request_clean_hours', 24); ?>" class="inputbox" size="10" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_POS_CLEAN_REQUESTS_HOURS_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_POS_CLEAN_REQUESTS'); ?>
            </th>
            <td><a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=pos&task=deleterequests'); ?>" class="btn"><?php echo JText::_('COM_CITRUSCART_POS_CLEAN_REQUESTS_CLICK'); ?></a>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_POS_CLEAN_REQUESTS_DESC'); ?>
            </td>
        </tr>
    </tbody>
</table>
