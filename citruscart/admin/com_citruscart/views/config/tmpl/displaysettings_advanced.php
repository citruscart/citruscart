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
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_PRODUCT_SORT_BY'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_sort_by', 'class="inputbox"', $this -> row -> get('display_sort_by', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_PRODUCT_SORTINGS'); ?>
            </th>
            <td>
            <textarea rows="5" name="display_sortings"><?php echo $this -> row -> get('display_sortings', 'Name|product_name,Price|price,Rating|product_rating'); ?></textarea>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_PRODUCT_SORTINGS_DESC')?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_WORKING_IMAGE_PRODUCT'); ?>
            </th>
            <td><?php echo CitruscartSelect::btbooleanlist('dispay_working_image_product', 'class="inputbox"', $this -> row -> get('dispay_working_image_product', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_WIDTH_OF_UI_LIGHTBOXES'); ?>
            </th>
            <td><input type="text" name="lightbox_width" value="<?php echo $this -> row -> get('lightbox_width', '800'); ?>" class="inputbox" size="10" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_WIDTH_OF_UI_LIGHTBOXES_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HEIGHT_OF_UI_LIGHTBOXES'); ?>
            </th>
            <td><input type="text" name="lightbox_height" value="<?php echo $this -> row -> get('lightbox_height', '480'); ?>" class="inputbox" size="10" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_HEIGHT_OF_UI_LIGHTBOXES_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PROCESS_CONTENT_PLUGIN_PRODUCT_DESC'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('content_plugins_product_desc', 'class="inputbox"', $this -> row -> get('content_plugins_product_desc', '0')); ?>
            </td>
            <td></td>
        </tr>
         <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_CITRUSCART_LINK_IN_FOOTER'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist( 'show_linkback', 'class="inputbox"', $this -> row -> get('show_linkback', '1')); ?>
            </td>
            <td></td>
        </tr>

    </tbody>
</table>
