<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');?>
<?php $form = $this->form; ?>
<?php $row = $this->row; JFilterOutput::objectHTMLSafe( $row ); ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >


			<table class="table table-striped table-bordered">
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_ENABLED'); ?>:
                    </td>
                    <td>
                        <?php echo CitruscartSelect::btbooleanlist(  'subscription_enabled', '', $row->subscription_enabled ); ?>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_LIFETIME_SUBSCRIPTION'); ?>:
                    </td>
                    <td>
                        <?php echo CitruscartSelect::btbooleanlist(  'lifetime_enabled', '', $row->lifetime_enabled ); ?>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_USER_ID'); ?>:
                    </td>
                    <td>
                        <input name="user_id" value="<?php echo $row->user_id; ?>" size="15" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_PRODUCT_ID'); ?>:
                    </td>
                    <td>
                        <input name="product_id" value="<?php echo $row->product_id; ?>" size="15" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?>:
                    </td>
                    <td>
                        <input name="order_id" value="<?php echo $row->order_id; ?>" size="15" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERITEM_ID'); ?>:
                    </td>
                    <td>
                        <input name="orderitem_id" value="<?php echo $row->orderitem_id; ?>" size="15" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_CREATED'); ?>:
                    </td>
                    <td>
                        <?php echo JHTML::calendar( $row->created_datetime, "created_datetime", "created_datetime", '%Y-%m-%d %H:%M:%S' ); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_EXPIRATION_DATE'); ?>:
                    </td>
                    <td>
                        <?php echo JHTML::calendar( $row->expires_datetime, "expires_datetime", "expires_datetime", '%Y-%m-%d %H:%M:%S' ); ?>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_TRANSACTION_ID'); ?>:
                    </td>
                    <td>
                        <input name="transaction_id" value="<?php echo $row->transaction_id; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->subscription_id; ?>" />
			<input type="hidden" name="task" value="" />

</form>