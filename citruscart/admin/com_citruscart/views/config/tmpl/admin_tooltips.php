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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_DASHBOARD_NOTE'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_dashboard_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_dashboard_disabled', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_CONFIGURATION_NOTE'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_config_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_config_disabled', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_TOOLS_NOTE'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_tools_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_tools_disabled', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_USER_DASHBOARD_NOTE'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_users_view_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_users_view_disabled', '0')); ?>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>