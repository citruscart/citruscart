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

    <p><?php echo JText::_('COM_CITRUSCART_THIS_TOOL_MIGRATES_DATA_FROM_XCART_TO_CITRUSCART'); ?></p>

    <div class="note">
        <span style="float: right; font-size: large; font-weight: bold;"><?php echo JText::_('COM_CITRUSCART_STEP_TWO_OF_THREE'); ?></span>
        <p><?php echo JText::_('COM_CITRUSCART_YOU_PROVIDED_THE_FOLLOWING_INFORMATION'); ?></p>
    </div>

    <fieldset>
        <legend><?php echo JText::_('COM_CITRUSCART_DATABASE_CONNECTION'); ?></legend>
            <table class="admintable">
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_HOST'); ?>:
                    </td>
                    <td>
                        <?php echo $state->host; ?>
                        <input type="hidden" name="host" id="host" size="48" maxlength="250" value="<?php echo $state->host; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_USERNAME'); ?>:
                    </td>
                    <td>
                        <?php echo $state->user; ?>
                        <input type="hidden" name="user" id="user" size="48" maxlength="250" value="<?php echo $state->user; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_PASSWORD'); ?>:
                    </td>
                    <td>
                       *****
                        <input type="hidden" name="password" id="password" size="48" maxlength="250" value="<?php echo $state->password; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_DATABASE_NAME'); ?>:
                    </td>
                    <td>
                        <?php echo $state->database; ?>
                        <input type="hidden" name="database" id="database" size="48" maxlength="250" value="<?php echo $state->database; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_TABLE_PREFIX'); ?>:
                    </td>
                    <td>
                        <?php echo $state->prefix; ?>
                        <input type="hidden" name="prefix" id="prefix" size="48" maxlength="250" value="<?php echo $state->prefix; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_DATABASE_TYPE'); ?>:
                    </td>
                    <td>
                        <?php echo $state->driver; ?>
                        <input type="hidden" name="driver" id="driver" size="48" maxlength="250" value="<?php echo $state->driver; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_DATABASE_PORT'); ?>:
                    </td>
                    <td>
                        <?php echo $state->port; ?>
                        <input type="hidden" name="port" id="port" size="48" maxlength="250" value="<?php echo $state->port; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
    </fieldset>