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
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_ADDRESS'); ?><br />
            </th>
            <td><input type="text" name="shop_email" value="<?php echo $this -> row -> get('shop_email', ''); ?>" class="inputbox" size="35" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_ADDRESS_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_FROM_NAME'); ?><br />
            </th>
            <td><input type="text" name="shop_email_from_name" value="<?php echo $this -> row -> get('shop_email_from_name', ''); ?>" class="inputbox" size="35" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_FROM_NAME_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISABLE_GUEST_SIGNUP_EMAIL'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('disable_guest_signup_email', 'class="inputbox"', $this -> row -> get('disable_guest_signup_email', '0')); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_DISABLE_GUEST_SIGNUP_EMAIL_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_OBFUSCATE_GUEST_EMAIL'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('obfuscate_guest_email', 'class="inputbox"', $this -> row -> get('obfuscate_guest_email', '0')); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_OBFUSCATE_GUEST_EMAIL_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_ORDER_STATUS_UPDATE_EMAIL_TO_USER_WHEN_ORDER_PAYMENT_IS_RECEIVED'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('autonotify_onSetOrderPaymentReceived', 'class="inputbox"', $this -> row -> get('autonotify_onSetOrderPaymentReceived', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ADDITIONAL_EMAIL_ADDRESSES_TO_RECEIVE_ORDER_NOTIFICATIONS'); ?><br />
            </th>
            <td><textarea name="order_emails" style="width: 250px;" rows="10">
                                                        <?php echo $this -> row -> get('order_emails', ''); ?>
                                                    </textarea>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ADDITIONAL_EMAIL_ADDRESSES_TO_RECEIVE_ORDER_NOTIFICATIONS_DESC'); ?>
            </td>
        </tr>
    </tbody>
</table>
