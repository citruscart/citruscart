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
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $vars->state; ?>
<?php echo $vars->token; ?>
	<p><?php echo JText::_('COM_CITRUSCART_THIS_TOOL_INSTALL_SAMPLE_DATA_TO_CITRUSCART'); ?></p>
    <div class="note">
        <span style="float: right; font-size: large; font-weight: bold;"><?php echo JText::_('COM_CITRUSCART_STEP_TWO_OF_THREE'); ?></span>
        <p><?php echo JText::_('COM_CITRUSCART_YOU_PROVIDED_THE_FOLLOWING_INFORMATION'); ?></p>
    </div>
    <fieldset>
        <legend><?php echo JText::_('COM_CITRUSCART_SAMPLE_DATA_INFORMATION'); ?></legend>
            <table class="admintable">
            	<?php if($state->install_default == '0' || empty($state->install_default)) {?>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_FILE'); ?>: *
                    </td>
                    <td>
                    	<?php echo $state->uploaded_file; ?>
                        <input type="hidden" name="uploaded_file" id="uploaded_file" size="48" value="<?php echo $state->uploaded_file; ?>" />
                    </td>
                </tr>
                <?php }else{ ?>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_DEFAULT_SAMPLE_DATA'); ?>:
                    </td>
                    <td>
                    	<?php echo JText::_(strtoupper($state->sampledata))." ".JText::_('COM_CITRUSCART_STORE'); ?>
        				<input type="hidden" name="sampledata" id="host" size="48" value="<?php echo $state->sampledata; ?>" />
                    </td>
                </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="install_default" id="install_default" value="<?php echo $state->install_default; ?>" />
    <br />

    </fieldset>
