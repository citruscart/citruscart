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
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
$state = @$this->state;
$item = @$this->row;
?>

<div id="citruscart" class="products view">

    <div id='citruscart_breadcrumb'>
        <?php echo CitruscartHelperCategory::getPathName($this->cat->category_id, 'links', true); ?>
    </div>

    <div id="citruscart_product">

        <?php if (!empty($this->onBeforeDisplayProduct)) : ?>
            <div id='onBeforeDisplayProduct_wrapper'>
            <?php echo $this->onBeforeDisplayProduct; ?>
            </div>
        <?php endif; ?>

        <div id='citruscart_product_header'>
            <span class="product_name">
                <?php echo $item->product_name; ?>
            </span>

            <div class="product_rating">
                <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating ); ?>
                <?php if (!empty($item->product_comments)) : ?>
                <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
                <?php endif; ?>
            </div>

            <div class="product_numbers">
                <?php if (!empty($item->product_model)) : ?>
                    <span class="model">
                        <span class="title"><?php echo JText::_('COM_CITRUSCART_MODEL'); ?>:</span>
                        <?php echo $item->product_model; ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($item->product_sku)) : ?>
                    <span class="sku">
                        <span class="title"><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</span>
                        <?php echo $item->product_sku; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="product_image">
            <?php echo CitruscartUrl::popup( CitruscartHelperProduct::getImage($item->product_id, '', '', 'full', true), CitruscartHelperProduct::getImage($item->product_id), array('update' => false, 'img' => true)); ?>
            <div>
            <?php
                if (isset($item->product_full_image))
                {
                    echo CitruscartUrl::popup( CitruscartHelperProduct::getImage($item->product_id, '', '', 'full', true), "View Larger", array('update' => false, 'img' => true ));
                }
            ?>
            </div>
        </div>

       <?php if ($this->product_description) : ?>
            <div class="reset"></div>

            <div id="product_description">
                <?php if (Citruscart::getInstance()->get('display_product_description_header', '1')) : ?>
                    <div id="product_description_header" class="citruscart_header">
                        <span><?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?></span>
                    </div>
                <?php endif; ?>
                <?php echo $this->product_description; ?>
            </div>
        <?php endif; ?>

        <?php // display the files associated with this product ?>
        <?php echo $this->files; ?>

        <?php // display the gallery images associated with this product if there is one ?>
        <?php $path = CitruscartHelperProduct::getGalleryPath($item->product_id); ?>
        <?php $images = CitruscartHelperProduct::getGalleryImages( $path, array( 'exclude'=>$item->product_full_image ) ); ?>
        <?php
        jimport('joomla.filesystem.folder');
        if (!empty($path) && !empty($images))
        {
            ?>

            <div class="reset"></div>
            <div class="product_gallery">
                <div id="product_gallery_header" class="citruscart_header">
                    <span><?php echo JText::_('COM_CITRUSCART_IMAGES'); ?></span>
                </div>
                <?php
                $uri = CitruscartHelperProduct::getUriFromPath( $path );
                foreach ($images as $image)
                {
                    ?>
                    <div class="subcategory">
                        <div class="subcategory_thumb">
                            <?php echo CitruscartUrl::popup( $uri.$image, '<img src="'.$uri."thumbs/".$image.'" />' , array('update' => false, 'img' => true)); ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="reset"></div>
            </div>
            <?php
        }
        ?>

        <div class="reset"></div>

        <?php if (!empty($this->onAfterDisplayProduct)) : ?>
            <div id='onAfterDisplayProduct_wrapper'>
            <?php echo $this->onAfterDisplayProduct; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
