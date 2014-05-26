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
<?php	
$form = $this->form;
$row = $this->row; 
JFilterOutput::objectHTMLSafe( $row );
JHTML::_('behavior.tooltip'); 
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >

			<table class="table table-striped table-bordered">
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_USER'); ?>:
                    </td>
                    <td>                    	
                     	<?php 
                     	   	$user_element = CitruscartSelect::userelement( $row->user_id, 'user_id' ); ?>
                        <?php echo $user_element['select']; ?>
                        <?php echo $user_element['clear']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_AMOUNT'); ?>:
                    </td>
                    <td>
                       	<input name="credit_amount" type="text" size="20" value="<?php echo isset($row->credit_amount) ? $row->credit_amount : ""; ?>" maxlength="250" />
                    </td>
                </tr>
				<tr>
					<td title="<?php echo JText::_('COM_CITRUSCART_CREDIT_TYPE').'::'.JText::_('COM_CITRUSCART_CREDIT_TYPE_TIP'); ?>" class="key hasTip" style="width: 100px; text-align: right;">
						<?php echo JText::_('COM_CITRUSCART_TYPE'); ?>:
					</td>
					<td>
					<?php echo CitruscartSelect::credittype( $row->credittype_code, 'credittype_code' ); ?>
						<?php  if(isset($row->credittype)) { ?>
						<?php echo CitruscartSelect::credittype( $row->credittype_code, 'credittype_code' ); ?>
						<?php }  ?>
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
                    </td>
                    <td>
                    	<?php echo CitruscartSelect::btbooleanlist( 'credit_enabled', '', $row->credit_enabled ); ?>
                    	<?php /* if(isset($row->credit_enabled)) { ?>
                     	<?php echo CitruscartSelect::btbooleanlist( 'credit_enabled', '', $row->credit_enabled ); ?>
                     	<?php }  */?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_CAN_BE_WITHDRAWN'); ?>:
                    </td>
                    <td>
                    	<?php echo CitruscartSelect::btbooleanlist( 'credit_withdrawable', '', isset($row->credit_withdrawable) ? $row->credit_withdrawable : "" ); ?>
                    </td>
                </tr>
				<tr>
					<td title="<?php echo JText::_('COM_CITRUSCART_CREDIT_CODE').'::'.JText::_('COM_CITRUSCART_CREDIT_CODE_TIP'); ?>" class="key hasTip" style="width: 100px; text-align: right;">
						<?php echo JText::_('COM_CITRUSCART_CODE'); ?>:
					</td>
					<td>
						<input name="credit_code" type="text" size="40" value="<?php echo isset($row->credit_code) ? $row->credit_code : "";  ?>" maxlength="250" />
					</td>
				</tr>
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_COMMENTS'); ?>:
					</td>
					<td>
						<textarea name="credit_comments" rows="10" cols="35"><?php echo isset($row->credit_comments) ? $row->credit_comments : "";  ?></textarea>
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?>:
                    </td>
                    <td>
                        <?php
                        if (!empty($row->order_id))
                        {
                            ?>
                            <a href="index.php?option=com_citruscart&view=orders&task=view&id=<?php echo $row->order_id; ?>" target="_blank"><?php echo JText::_('COM_CITRUSCART_VIEW_ORDER').": " .$row->order_id; ?></a>
                            <?php
                        } else {
                            echo JText::_('COM_CITRUSCART_NONE');
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_BALANCE_UPDATED'); ?>:
                    </td>
                    <td>
                    	<?php echo CitruscartGrid::boolean( isset($row->credits_updated) ? $row->credits_updated : "" ); ?>
                     </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_BALANCE_BEFORE'); ?>:
                    </td>
                    <td>
                    	<?php echo isset($row->credit_balance_before) ? $row->credit_balance_before : ""; ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_BALANCE_AFTER'); ?>:
                    </td>
                    <td>
                    	<?php echo isset($row->credit_balance_after) ? $row->credit_balance_after : ""; ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_WITHDRAWABLE_BALANCE_BEFORE'); ?>:
                    </td>
                    <td>
                    	<?php echo $row->withdrawable_balance_before;?>
                    	<?php  echo isset($row->withdrawable_balance_before) ? $row->withdrawable_balance_before : ""; ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_('COM_CITRUSCART_WITHDRAWABLE_BALANCE_AFTER'); ?>:
                    </td>
                    <td>
                    	<?php echo isset($row->withdrawable_balance_after) ? $row->withdrawable_balance_after : ""; ?>
                    </td>
                </tr>
			</table>
			<input type="hidden" name="credit_id" value="<?php echo $row->credit_id; ?>" />
			<input type="hidden" name="task" value="" />

</form>
