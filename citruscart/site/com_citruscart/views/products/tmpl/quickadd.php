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
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/');
JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/');
$state = $this->state;
$item = $this->row;

 // TODO This tmpl will eventually be used for quickly adding an item to your cart
 // directly from the product list -- this tmpl will load in a lightbox
?>

<div class="productheading">
    <span class="producttitle"><?php echo $item->product_name; ?></span>
    <span class="productmeta">
    <?php
        $sep = '';
        if (!empty($item->product_model)) {
            echo '<b>'.JText::_('COM_CITRUSCART_MODEL').":</b> $item->product_model";
            $sep = "&nbsp;&nbsp;";
        }
        if (!empty($item->product_sku)) {
            echo "$sep <b>".JText::_('COM_CITRUSCART_SKU').":</b> $item->product_sku";
        }
    ?>
    </span>
</div>

<div class="indproduct">
    <div class="productimage">
        <?php echo CitruscartHelperProduct::getImage($item->product_id); ?>
    </div>

    <div class="productbuy">
        <div>
            <span class="price"><?php echo CitruscartHelperBase::currency($item->price); ?></span><br />
            <?php $url = "index.php?option=com_citruscart&format=raw&controller=carts&task=addToCart&productid=".$item->product_id; ?>
            <?php $onclick = 'CitruscartDoTask(\''.$url.'\', \'CitruscartUserShoppingCart\', \'\');' ?>
            <img class="addcart" src="media/com_citruscart/images/addcart.png" alt="" onclick="<?php echo $onclick; ?>" />
        </div>
    </div>

    <div class="reset"></div>
    <div class="productdesc">
       <div class="productdesctitle"><?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?></div>
        <?php echo $item->product_description; ?>
    </div>
</div>

<div class="reset"></div>
