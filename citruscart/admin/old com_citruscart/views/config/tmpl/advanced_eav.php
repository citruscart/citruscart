<?php 
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_ROWS'); ?>
            </th>
            <td style="width: 150px;"><input type="text" name="eav_textarea_rows" value="<?php echo $this -> row -> get('eav_textarea_rows', '20'); ?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_COLUMNS'); ?>
            </th>
            <td style="width: 150px;"><input type="text" name="eav_textarea_columns" value="<?php echo $this -> row -> get('eav_textarea_columns', '50'); ?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_WIDTH'); ?>
            </th>
            <td style="width: 150px;"><input type="text" name="eav_textarea_width" value="<?php echo $this -> row -> get('eav_textarea_width', '300'); ?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_HEIGHT'); ?>
            </th>
            <td style="width: 150px;"><input type="text" name="eav_textarea_height" value="<?php echo $this -> row -> get('eav_textarea_height', '200'); ?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_EAV_CONTENT_PLUGIN_TEXTAREA'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('eavtext_content_plugin', 'class="inputbox"', $this -> row -> get('eavtext_content_plugin', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_EAV_INTEGER_THOUSANDS_SEPARATOR'); ?>
            </th>
            <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('eavinteger_use_thousand_separator', 'class="inputbox"', $this -> row -> get('eavinteger_use_thousand_separator', '0')); ?>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
