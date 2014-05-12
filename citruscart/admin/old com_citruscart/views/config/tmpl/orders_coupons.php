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
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_COUPONS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('coupons_enabled', 'class="inputbox"', $this -> row -> get('coupons_enabled', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_MULTIPLE_USER_SUBMITTED_COUPONS_PER_ORDER'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('multiple_usercoupons_enabled', 'class="inputbox"', $this -> row -> get('multiple_usercoupons_enabled', '0')); ?>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
