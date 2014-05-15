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

defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_EXPIRATION_NOTICE'); ?>
            </th>
            <td><input name="subscriptions_expiring_notice_days" value="<?php echo $this -> row -> get('subscriptions_expiring_notice_days', '14'); ?>" type="text" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_EXPIRATION_NOTICE_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_SUBSCRIPTION_NUMBER'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_subnum', 'class="inputbox"', $this -> row -> get('display_subnum', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_SUBSCRIPTION_NUMBER_DIGITS'); ?>
            </th>
            <td><input type="text" name="sub_num_digits" value="<?php echo $this -> row -> get('sub_num_digits', ''); ?>" class="inputbox" size="10" />
            </td>
            <td></td>
        </tr>
        <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_SUBSCRIPTION_NUMBER'); ?>
        </th>
        <td><input type="text" name="default_sub_num" value="<?php echo $this -> row -> get('default_sub_num', '1'); ?>" class="inputbox" size="10" />
        </td>
        <td></td>
        </tr>
    </tbody>
</table>
