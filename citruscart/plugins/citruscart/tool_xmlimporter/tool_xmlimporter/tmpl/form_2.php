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
defined('_JEXEC') or die('Restricted access');
?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $vars->state; ?>
<?php echo $vars->token; ?>

    <p><?php echo JText::_('COM_CITRUSCART_THIS_TOOL_IMPORTS_DATA_FROM_AN_XML_FILE_TO_CITRUSCART'); ?></p>

    <div class="note">
        <span style="float: right; font-size: large; font-weight: bold;"><?php echo JText::_('COM_CITRUSCART_STEP_TWO_OF_THREE'); ?></span>
        <p><?php echo JText::_('COM_CITRUSCART_PLEASE_REVIEW_THE_FOLLOWING_INFORMATION'); ?></p>
    </div>

    <fieldset>
        <legend><?php echo JText::_('COM_CITRUSCART_FILE_INFORMATION'); ?></legend>
            <table class="admintable">
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_XML_FILE'); ?>: *
                    </td>
                    <td>
                    	<?php echo $state->uploaded_file; ?>
                        <input type="hidden" name="uploaded_file" id="uploaded_file" size="48" value="<?php echo $state->uploaded_file; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
               <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_IMAGES_ZIP_FILE'); ?>: *
                    </td>
                    <td>
                    	<?php echo $state->uploaded_images_zip_file; ?>
                        <input type="hidden" name="uploaded_images_zip_file" id="uploaded_images_zip_file" size="48" value="<?php echo $state->uploaded_images_zip_file; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_('COM_CITRUSCART_FILES_ZIP_FILE'); ?>: *
                    </td>
                    <td>
                    	<?php echo $state->uploaded_files_zip_file; ?>
                        <input type="hidden" name="uploaded_files_zip_file" id="uploaded_files_zip_file" size="48" value="<?php echo $state->uploaded_files_zip_file; ?>" />
                    </td>
                    <td>

                    </td>
                </tr>

            </table>
    <br />

    </fieldset>
