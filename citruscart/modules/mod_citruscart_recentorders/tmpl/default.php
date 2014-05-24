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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
?>

<?php $options = array('num_decimals'=>'0'); ?>
<table class="table table-striped" style="margin-bottom: 5px; ">
<thead>
<tr>
    <th colspan="4" style="background-color:#FF9B13; color:#FFFFFF"><?php echo JText::_('COM_CITRUSCART_RECENT_ORDERS'); ?></th>
</tr>
</thead>
<tbody>
	<tr>
    <th><?php echo JText::_('COM_CITRUSCART_CUSTOMER'); ?></th>
    <th style="text-align: center;"><?php echo JText::_('COM_CITRUSCART_DATE'); ?></th>
    <th style="text-align: right;"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
    <th><?php echo JText::_('MOD_CITRUSCART_RECENT_ORDERS_ACTION');?></th>
</tr>
<?php foreach ($orders as $order):?>
    <tr>
        <td><a href="<?php echo $order->link; ?>"><?php echo $order->user_name; ?></a></td>
        <td style="text-align: center;"><?php echo JHTML::_('date', $order->created_date, Citruscart::getInstance()->get('date_format')); ?></td>
        <td style="text-align: right;"><span class="label label-warning lead"><?php echo CitruscartHelperBase::currency( $order->order_total, $order->currency ); ?></span></td>
        <td><label><a class="label label-info" href="<?php echo $order->link; ?>"><?php echo JText::_('MOD_CITRUSCART_RECENT_ORDERS_DETAILS');?></a></label></td>
    </tr>
 <?php endforeach;?>
</tbody>
</table>
