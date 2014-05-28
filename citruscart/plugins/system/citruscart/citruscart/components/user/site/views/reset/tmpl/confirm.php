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
defined('_JEXEC') or die('Restricted access');?>

<div class="componentheading">
	<?php echo JText::_('COM_CITRUSCART_CONFIRM_YOUR_ACCOUNT'); ?>
</div>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=confirmreset' ); ?>" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="token" class="hasTip" title="<?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_TOKEN_TIP_TITLE'); ?>::<?php echo JText::_('COM_CITRUSCART_RESET_PASSWORD_TOKEN_TIP_TEXT'); ?>"><?php echo JText::_('COM_CITRUSCART_TOKEN'); ?>:</label>
			</td>
			<td>
				<input id="token" name="token" type="text" class="required" size="36" />
			</td>
		</tr>
	</table>

	<button type="submit" class="validate"><?php echo JText::_('COM_CITRUSCART_SUBMIT'); ?></button>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
