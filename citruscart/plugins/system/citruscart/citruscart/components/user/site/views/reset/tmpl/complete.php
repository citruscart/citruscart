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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>

<div class="componentheading">
	<?php echo JText::_('COM_CITRUSCART_RESET_YOUR_PASSWORD'); ?>
</div>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=completereset' ); ?>" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="password1" class="hasTip" title="<?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_PASSWORD1_TIP_TITLE'); ?>::<?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_PASSWORD1_TIP_TEXT'); ?>"><?php echo JText::_('COM_CITRUSCART_PASSWORD'); ?>:</label>
			</td>
			<td>
				<input id="password1" name="password1" type="password" class="required validate-password" />
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="password2" class="hasTip" title="<?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_PASSWORD2_TIP_TITLE'); ?>::<?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_PASSWORD2_TIP_TEXT'); ?>"><?php echo JText::_('COM_CITRUSCART_VERIFY_PASSWORD'); ?>:</label>
			</td>
			<td>
				<input id="password2" name="password2" type="password" class="required validate-password" />
			</td>
		</tr>
	</table>

	<button type="submit" class="validate"><?php echo JText::_('COM_CITRUSCART_SUBMIT'); ?></button>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>