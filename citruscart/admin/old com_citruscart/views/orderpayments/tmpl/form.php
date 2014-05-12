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
defined('_JEXEC') or die('Restricted access');?>
<?php $form = $this->form; ?>
<?php $row = $this->row; JFilterOutput::objectHTMLSafe( $row ); ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >


			<table class="table table-striped table-bordered">
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?>:
                    </td>
                    <td>
                        <input name="order_id" value="<?php echo $row->order_id; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERPAYMENT_TYPE'); ?>:
                    </td>
                    <td>
                        <input name="orderpayment_type" value="<?php echo $row->orderpayment_type; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERPAYMENT_AMOUNT'); ?>:
                    </td>
                    <td>
                        <input name="orderpayment_amount" value="<?php echo $row->orderpayment_amount; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDERPAYMENT_DATE'); ?>:
                    </td>
                    <td>
                        <?php echo JHTML::calendar( $row->created_date, "created_date", "created_date", '%Y-%m-%d %H:%M:%S' ); ?>
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
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_TRANSACTION_STATUS'); ?>:
                    </td>
                    <td>
                        <input name="transaction_status" value="<?php echo $row->transaction_status; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_TRANSACTION_DETAILS'); ?>:
                    </td>
                    <td>
                        <textarea cols="50" rows="10" name="transaction_details"><?php echo $row->transaction_details; ?></textarea>
                    </td>
                </tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row->orderpayment_id; ?>" />
			<input type="hidden" name="task" value="" />

</form>