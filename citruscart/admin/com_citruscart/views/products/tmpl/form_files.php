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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');?>
<?php
$form = @$this->form;
$row = @$this->row;
$helper_product = new CitruscartHelperProduct();
?>
<table class="table table-striped table-bordered">

    <?php
    if (empty($row->product_id))
    {
        // doing a new product, so display a notice
        ?>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_FILES'); ?>:</td>
        <td>
            <div class="note well">
                <?php echo JText::_('COM_CITRUSCART_CLICK_APPLY_TO_BE_ABLE_TO_ADD_FILES_TO_THE_PRODUCT'); ?>
            </div>
        </td>
    </tr>
    <?php
    }
    else
    {
        // display lightbox link to manage files
        ?>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_FILES'); ?>:</td>
        <td><?php
        Citruscart::load( 'CitruscartUrl', 'library.url' );
        ?> [<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=products&task=setfiles&id=".$row->product_id."&tmpl=component", JText::_('COM_CITRUSCART_MANAGE_FILES') ); ?>] <?php $files = $helper_product->getFiles( $row->product_id ); ?>
            <div id="current_files">
                <?php foreach (@$files as $file) : ?>
                [<a href="<?php echo "index.php?option=com_citruscart&view=productfiles&task=delete&cid[]=".$file->productfile_id."&return=".base64_encode("index.php?option=com_citruscart&view=products&task=edit&id=".$row->product_id); ?>"> <?php echo JText::_('COM_CITRUSCART_REMOVE'); ?>
                </a>] [<a href="<?php echo "index.php?option=com_citruscart&view=productfiles&task=downloadfile&id=".$file->productfile_id."&product_id=".$row->product_id; ?>"> <?php echo JText::_('COM_CITRUSCART_DOWNLOAD');?>
                </a>]
                <?php echo $file->productfile_name; ?>
                <br />
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_FILES_PATH_OVERRIDE'); ?>:</td>
        <td><input name="product_files_path" id="product_files_path" value="<?php echo @$row->product_files_path; ?>" size="75" maxlength="255" type="text" />
            <div class="note well">
                <?php echo JText::_('COM_CITRUSCART_IF_NO_FILE_PATH_OVERRIDE_IS_SPECIFIED_MESSAGE'); ?>
                <ul>
                    <li>/images/com_citruscart/files/[SKU]</li>
                    <li>/images/com_citruscart/files/[ID]</li>
                </ul>
                <?php echo JText::_('COM_CITRUSCART_CHANGING_FILE_PATH_NOTE'); ?>
            </div>
        </td>
    </tr>

    <?php
    }
    ?>
</table>
