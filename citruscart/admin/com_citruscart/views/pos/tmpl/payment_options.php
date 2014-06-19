<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
 ?>
<div class="well well-small note">
	<?php echo count($this->payment_plugins) ? JText::_('COM_CITRUSCART_PAYMENT_NOTE_1').":" : JText::_('COM_CITRUSCART_PAYMENT_NOTE_2');?>
</div>
<?php if(count($this->payment_plugins)):?>
	<?php foreach($this->payment_plugins as $payment_plugin):?>
	<input value="<?php echo $payment_plugin->element; ?>" onclick="CitruscartGetPaymentForm('<?php echo $payment_plugin->element; ?>', 'payment_form_div'); $('validation_message').set('html', ''); $('payment_form_div').addClass('note')" name="payment_plugin" type="radio" <?php echo (!empty($payment_plugin->checked)) ? "checked" : ""; ?> />
	<?php echo JText::_( $payment_plugin->name ); ?>
	<br />
	<?php endforeach;?>

	 <div id='payment_form_div' <?php if(!empty($this->payment_form_div)) echo 'class="note"';?> style="padding-top: 5px;">
	 <?php if(!empty($this->payment_form_div)):?>
	 	<?php echo $this->payment_form_div;?>
	 <?php endif;?>
	 </div>
<?php endif;?>
