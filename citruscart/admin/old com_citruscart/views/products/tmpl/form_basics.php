<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$form = $this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
?>

<legend>
    <?php echo JText::_('COM_CITRUSCART_BASIC_INFORMATION'); ?>
</legend>
<div style="float: left; margin: 5px;">
    <table class="table table-striped table-bordered">
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_NAME'); ?>:</td>
            <td class="dsc-value"><input type="text" name="product_name" id="product_name" value="<?php echo $row->product_name; ?>" size="48" maxlength="250" />
            </td>
        </tr>
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_ALIAS'); ?>:</td>
            <td class="dsc-value"><input name="product_alias" id="product_alias" value="<?php echo $row->product_alias; ?>" type="text" size="48" maxlength="250" />
            </td>
        </tr>
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_ID'); ?>:</td>
            <td class="dsc-value"><?php
            if (empty($row->product_id))
            {
                ?>
                <div style="color: grey;">
                    <?php echo JText::_('COM_CITRUSCART_AUTOMATICALLY_GENERATED'); ?>
                </div> <?php
            }
            else
            {
                echo $row->product_id;
            }
            ?>
            </td>
        </tr>
    </table>
</div>
<div style="float: left; margin: 5px;">
    <table class="table table-striped table-bordered">
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_MODEL'); ?>:</td>
            <td class="dsc-value"><input type="text" name="product_model" id="product_model" value="<?php echo $row->product_model; ?>" size="48" maxlength="250" />
            </td>
        </tr>
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</td>
            <td class="dsc-value"><input type="text" name="product_sku" id="product_sku" value="<?php echo $row->product_sku; ?>" size="48" maxlength="250" />
            </td>
        </tr>
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:</td>
            <td class="dsc-value"><?php  echo CitruscartSelect::btbooleanlist( 'product_enabled', '', $row->product_enabled ); ?>
            </td>
        </tr>
    </table>
</div>
<div style="float: left; margin: 5px;">
    <table class="table table-striped table-bordered">
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_OVERALL_RATING'); ?>:</td>
            <td class="dsc-value"><?php echo $helper_product->getRatingImage( $row->product_rating, $this ); ?>
            </td>
        </tr>
        <tr>
            <td style="width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_COMMENTS'); ?>:</td>
            <td class="dsc-value"><?php echo $row->product_comments; ?>
            </td>
        </tr>
    </table>
</div>

<div id="default_image" style="float: right; padding: 0px 5px 5px 0px;">
    <?php
    jimport('joomla.filesystem.file');
    if (!empty($row->product_full_image))
    {
        $gallery_url = $helper_product->getGalleryUrl($row->product_id);
        ?>
        <img src="<?php echo $gallery_url; ?>thumbs/<?php echo $row->product_full_image; ?>" class="img-polaroid" />
        <?php
    }
    ?>
</div>
