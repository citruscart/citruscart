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
defined('_JEXEC') or die('Restricted access'); ?>
<?php
//$form =$this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
$gallery_path = $helper_product->getGalleryPath($row->product_id);
$gallery_url = $helper_product->getGalleryUrl($row->product_id);
$images = $helper_product->getGalleryImages($gallery_path);
?>

<?php if (!empty($images)) {
	?>
<fieldset id="product-form-gallery">
    <legend><?php echo JText::_( "COM_CITRUSCART_GALLERY" ); ?></legend>
    <ul class="inline">
    <?php  foreach ($images as $image): ?>
        <li class="pull-left">
            <img src="<?php echo $gallery_url; ?>thumbs/<?php echo $image; ?>" class="img-polaroid" />
            <div class="dsc-clear">
    			<a class="set-default-gallery-image" data-image="<?php echo $image; ?>" data-product_id="<?php echo $row->product_id; ?>" href="javascript:void(0);" data-href="index.php?option=com_citruscart&view=products&format=raw&tmpl=component&task=setDefaultImage&product_id=<?php echo $row->product_id; ?>&image=<?php echo $image; ?>"><?php echo JText::_('COM_CITRUSCART_MAKE_DEFAULT'); ?></a>
    			<br/>
    			[<a style="text-align:center;" class="delete-gallery-image text text-error" data-product_id="<?php echo $row->product_id; ?>" href="javascript:void(0);" data-href="index.php?option=com_citruscart&view=products&format=raw&tmpl=component&task=deleteImage&product_id=<?php echo $row->product_id; ?>&image=<?php echo $image; ?>"><?php echo JText::_('COM_CITRUSCART_DELETE'); ?></a>]
			</div>
        </li>
      <?php endforeach;?>
    </ul>

</fieldset>
<?php } ?>