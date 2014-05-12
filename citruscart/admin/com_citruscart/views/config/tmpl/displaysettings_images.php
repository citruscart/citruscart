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
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_DEFAULT_CATEGORY_IMAGE'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('use_default_category_image', '' , $this -> row -> get('use_default_category_image', '1')) ; ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_PRODUCT_IMAGE_HEIGHT'); ?>
            </th>
            <td><input type="text" name="product_img_height" value="<?php echo $this -> row -> get('product_img_height', ''); ?>" />
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_PRODUCT_IMAGE_WIDTH'); ?>
            </th>
            <td><input type="text" name="product_img_width" value="<?php echo $this -> row -> get('product_img_width', ''); ?>" />
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_RECREATE_PRODUCT_THUMBNAILS'); ?>
            </th>
            <td><a href="index.php?option=com_citruscart&view=products&task=recreateThumbs" onClick="return confirm('<?php echo JText::_('Are you sure? Remember to save your new Configuration Values before doing this!'); ?>');"><?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_RECREATE_THE_PRODUCT_THUMBNAILS'); ?> </a>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_CATEGORY_IMAGE_HEIGHT'); ?>
            </th>
            <td><input type="text" name="category_img_height" value="<?php echo $this -> row -> get('category_img_height', ''); ?>" />
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_CATEGORY_IMAGE_WIDTH'); ?>
            </th>
            <td><input type="text" name="category_img_width" value="<?php echo $this -> row -> get('category_img_width', ''); ?>" />
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_RECREATE_CATEGORY_THUMBNAILS'); ?>
            </th>
            <td><a href="index.php?option=com_citruscart&view=categories&task=recreateThumbs" onClick="return confirm('<?php echo JText::_('Are you sure? Remember to save your new Configuration Values before doing this!'); ?>');"><?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_RECREATE_THE_CATEGORY_THUMBNAILS'); ?> </a>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_MANUFACTURER_IMAGE_HEIGHT'); ?>
            </th>
            <td><input type="text" name="manufacturer_img_height" value="<?php echo $this -> row -> get('manufacturer_img_height', ''); ?>" />
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_MANUFACTURER_IMAGE_WIDTH'); ?>
            </th>
            <td><input type="text" name="manufacturer_img_width" value="<?php echo $this -> row -> get('manufacturer_img_width', ''); ?>" />
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_RECREATE_MANUFACTURER_THUMBNAILS'); ?>
            </th>
            <td><a href="index.php?option=com_citruscart&view=manufacturers&task=recreateThumbs" onClick="return confirm('<?php echo JText::_('COM_CITRUSCART_ARE_YOU_SURE_REMEMBER_TO_SAVE_YOUR_NEW_CONFIGURATION_VALUES'); ?>');"><?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_RECREATE_THE_MANUFACTURER_THUMBNAILS'); ?> </a>
            </td>
        </tr>
    </tbody>
</table>
