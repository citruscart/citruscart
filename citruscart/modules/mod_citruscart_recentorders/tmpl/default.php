<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

?>

<?php $options = array('num_decimals'=>'0'); ?>
    
<table class="table table-striped table-bordered" style="margin-bottom: 5px;">
<thead>
<tr>
    <th colspan="3"><?php echo JText::_('COM_CITRUSCART_RECENT_ORDERS'); ?></th>
</tr>
</thead>
<tbody>
<tr>
    <th><?php echo JText::_('COM_CITRUSCART_CUSTOMER'); ?></th>
    <th style="text-align: center;"><?php echo JText::_('COM_CITRUSCART_DATE'); ?></th>
    <th style="text-align: right;"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
</tr>
<?php
foreach ($orders as $order)
{
    ?>
    <tr>
        <td><a href="<?php echo $order->link; ?>"><?php echo $order->user_name; ?></a></td>
        <td style="text-align: center;"><?php echo JHTML::_('date', $order->created_date, Citruscart::getInstance()->get('date_format')); ?></td>
        <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $order->order_total, $order->currency ); ?></td>
    </tr>
    <?php
} 
?>
</tbody>
</table>
