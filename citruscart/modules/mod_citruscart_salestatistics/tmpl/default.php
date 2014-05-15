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

<?php $options_decimal = array('num_decimals'=>'2'); ?>
<?php $options_int = array('num_decimals'=>'0'); ?>
    
<table class="table table-striped table-bordered" style="margin-bottom: 5px;">
<thead>
<tr>
    <th colspan="5"><?php echo JText::_('COM_CITRUSCART_SUMMARY_STATISTICS'); ?></th>
</tr>
</thead>
<tbody>
<tr>
    <th width="100px"><?php echo JText::_('COM_CITRUSCART_RANGE'); ?></th>
    <th style="text-align: center;"><?php echo JText::_('COM_CITRUSCART_TOTAL_ORDERS'); ?></th>
	<th style="text-align: right;"><?php echo JText::_('COM_CITRUSCART_AVERAGE_ORDERS_PER_DAY'); ?></th>
    <th style="text-align: right;"><?php echo JText::_('COM_CITRUSCART_AVERAGE_REVENUE_PER_ORDER'); ?></th>
	<th style="text-align: right;"><?php echo JText::_('COM_CITRUSCART_TOTAL_REVENUE'); ?></th>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_TODAY'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->today->num, $options_int ); ?></td>
	<td style="text-align: right;">&nbsp</td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->today->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->today->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_YESTERDAY'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->yesterday->num, $options_int ); ?></td>
	<td style="text-align: right;">&nbsp</td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->yesterday->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->yesterday->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_LAST_SEVEN_DAYS'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lastseven->num, $options_int ); ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lastseven->average_daily, $options_decimal ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lastseven->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lastseven->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_LAST_MONTH'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lastmonth->num, $options_int ); ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lastmonth->average_daily, $options_decimal ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lastmonth->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lastmonth->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_THIS_MONTH'); ?></a></th>
    <td style="text-align: right;"><?php // echo CitruscartHelperBase::number( $stats->thismonth->num, $options_int ); ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->thismonth->average_daily, $options_decimal ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->thismonth->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->thismonth->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_LAST_YEAR'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lastyear->num, $options_int ); ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lastyear->average_daily, $options_decimal ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lastyear->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lastyear->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_THIS_YEAR'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->thisyear->num, $options_int ); ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->thisyear->average_daily, $options_decimal ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->thisyear->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->thisyear->amount, '', $options_decimal ); ?></td>
</tr>
<tr>
    <th><a href="<?php echo $stats->link; ?>"><?php echo JText::_('COM_CITRUSCART_LIFETIME_SALES'); ?></a></th>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lifetime->num, $options_int ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::number( $stats->lifetime->average_daily, $options_decimal ) ?></td>
	<td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lifetime->average,'', $options_decimal ); ?></td>
    <td style="text-align: right;"><?php echo CitruscartHelperBase::currency( $stats->lifetime->amount, '', $options_decimal ); ?></td>
</tr>

</tbody>
</table>
