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
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_SUBSCRIPTIONS'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_subscriptions', 'class="inputbox"', $this -> row -> get('display_subscriptions', '1')); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ENABLE_SUBSCRIPTIONS_NOTE'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_MY_DOWNLOADS'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_mydownloads', 'class="inputbox"', $this -> row -> get('display_mydownloads', '1')); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ENABLE_MY_DOWNLOADS_NOTE'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_WISHLIST'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_wishlist', 'class="inputbox"', $this -> row -> get('display_wishlist', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_CREDITS'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_credits', 'class="inputbox"', $this -> row -> get('display_credits', '0')); ?>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
