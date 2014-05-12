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
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_PRODUCT_REVIEWS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('product_review_enable', 'class="inputbox"', $this -> row -> get('product_review_enable', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_AUTOMATICALLY_APPROVE_REVIEWS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('product_reviews_autoapprove', 'class="inputbox"', $this -> row -> get('product_reviews_autoapprove', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_REQUIRE_LOGIN_TO_LEAVE_REVIEW'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('login_review_enable', 'class="inputbox"', $this -> row -> get('login_review_enable', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_REQUIRE_PURCHASE_TO_LEAVE_REVIEW'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('purchase_leave_review_enable', 'class="inputbox"', $this -> row -> get('purchase_leave_review_enable', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_USE_CAPTCHA'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('use_captcha', 'class="inputbox"', $this -> row -> get('use_captcha', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_REVIEW_HELPFULNESS_VOTING'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('review_helpfulness_enable', 'class="inputbox"', $this -> row -> get('review_helpfulness_enable', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_SHARE_THIS_LINK'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('share_review_enable', 'class="inputbox"', $this -> row -> get('share_review_enable', '1')); ?>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
