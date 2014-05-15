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
<fieldset class="citruscart-expanded" id="customer-pane">
	<legend class="citruscart-collapse-processed"><?php echo JText::_('COM_CITRUSCART_CUSTOMER_INFORMATION')?></legend>
	<div id="citruscart_customer">
		<div class="note">
			<?php echo JText::_('COM_CITRUSCART_ORDER_INFORMATION_WILL_BE_SENT_TO_YOUR_ACCOUNT_E-MAIL_LISTED_BELOW')?>
		</div>
			<div class="citruscart_checkout_method_user_email">
				<?php
					if($this->user->id)
						$email_address = $this->user->email;
					else
						$email_address = '';
				?>

				<?php echo JText::_('COM_CITRUSCART_E-MAIL_ADDRESS');?>: <span id="user_email_span"><?php echo $email_address; ?></span>
				<input type="text" id="email_address" name="email_address" value="<?php echo $email_address; ?>"/>
				<input type="button" id="email_address_button_edit" onclick="citruscartCheckoutToogleEditEmail( 'user_email_validation',document.adminForm, true );" value="<?php echo JText::_('COM_CITRUSCART_EDIT');?>" />
				<input type="button" id="email_address_button_cancel" onclick="citruscartCheckoutToogleEditEmail( 'user_email_validation',document.adminForm, false );" value="<?php echo JText::_('COM_CITRUSCART_CANCEL');?>" />
			</div>
			<div id="user_email_validation"></div>
	</div>
</fieldset>