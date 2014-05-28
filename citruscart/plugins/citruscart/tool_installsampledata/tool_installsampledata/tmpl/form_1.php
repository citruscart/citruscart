<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');?>
<?php	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php	$state = $vars->state;?>
<?php	echo $vars->token;?>
<p>
	<?php	echo JText::_('COM_CITRUSCART_THIS_TOOL_INSTALL_SAMPLE_DATA_TO_CITRUSCART');?>
</p>
<div class="note">
	<span style="float: right; font-size: large; font-weight: bold;">
		<?php	echo JText::_('COM_CITRUSCART_STEP_ONE_OF_THREE');?>
	</span>
	<p>
		<?php	echo JText::_('COM_CITRUSCART_PLEASE_PROVIDE_THE_REQUESTED_INFORMATION');?>
	</p>
</div>
<fieldset>
	<?php
	$options = array();
	$options[] = JHTML::_('select.option', 'electronic', JText::_('COM_CITRUSCART_ELECTRONIC_STORE'));
	$options[] = JHTML::_('select.option', 'clothing', JText::_('COM_CITRUSCART_CLOTHING_STORE'));
	?>
	<table class="admintable">
		<tr id="sampledataupload" >
			<td width="100" align="right" class="key">
			<?php	echo JText::_('COM_CITRUSCART_FILE');?>: *
			</td>
			<td>
			<input type="file" name="file" id="file" size="48" value="<?php	echo $state->file;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="dsc-key">
			<?php	echo JText::_('COM_CITRUSCART_INSTALL_DEFAULT_DATA');?>:
			</td>
			<td class="dsc-value">
			<input type="checkbox" name="install_default" id="install_default" class="" onclick="Dsc.showHideDiv('sampledatatype');" />
			<?php  echo JHTML::_('select.genericlist', $options, 'sampledata', 'class="inputbox" style="display:none;"', 'value', 'text', 'electronic', 'sampledatatype');?>
			</td>
		</tr>
	</table>
</fieldset>
