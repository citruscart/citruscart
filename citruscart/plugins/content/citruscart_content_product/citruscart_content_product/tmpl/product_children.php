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
JHTML::_('stylesheet', 'citruscart.css', 'media/citruscart/css/');
JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
$items = $vars->items;
$form = $this->form;
?>
<?php if($items):?>
<form action="<?php echo JRoute::_( 'index.php?option=com_citruscart&controller=products&view=products&id="'.$vars->product_id ); ?>" method="post" class="adminform" name="adminFormChildren" enctype="multipart/form-data" >

    <div class="reset"></div>

    <div id="product_children">
        <div id="product_children_header" class="Citruscart_header">
            <span><?php echo JText::_('COM_CITRUSCART_SELECT_THE_ITEMS_TO_ADD_TO_YOUR_CART'); ?></span>
        </div>

        <table class="adminlist">
        <thead>
        <tr>
            <th style="text-align: left;">
                <?php echo JText::_('COM_CITRUSCART_PRODUCT_NAME'); ?>
            </th>
            <th style="text-align: left;">
                <?php echo JText::_('COM_CITRUSCART_SKU'); ?>
            </th>
            <th style="text-align: center;">
                <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>
            </th>
            <th style="text-align: center;">
                <?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        $k = 0;
        foreach ($items as $item): ?>
        <tr>
            <td style="text-align: left;">
                <?php echo $item->product_name; ?>
            </td>
            <td style="text-align: left;">
                <?php echo $item->product_sku; ?>
            </td>
            <td style="text-align: center;">
                <?php  echo CitruscartHelperProduct::dispayPriceWithTax($item->product_price, $item->tax, $item->showtax); ?>
            </td>
            <td style="text-align: center;">
                <input type="text" name="quantities[<?php echo $item->product_id; ?>]" value="1" size="5" />
            </td>
        </tr>
        <?php $k = 1 - $k; ?>
        <?php endforeach; ?>
        </tbody>
        </table>

        <div class="reset"></div>

        <div id="validationmessage_children"></div>

        <!-- Add to cart button --->
        <div id='add_to_cart_children' style="display: block; float: right;">
            <input type="hidden" name="product_id" value="<?php echo $vars->product_id; ?>" />
            <input type="hidden" name="filter_category" value="<?php echo $vars->filter_category; ?>" />
            <input type="hidden" id="task" name="task" value="" />
            <?php echo JHTML::_( 'form.token' ); ?>

            <?php $onclick = "citruscartFormValidation( '".JRoute::_( $vars->validation )."', 'validationmessage_children', 'addchildrentocart', document.adminFormChildren );"; ?>

            <?php
            if (empty($item->product_check_inventory) || (!empty($item->product_check_inventory) && empty($this->invalidQuantity)) ) :
                switch (Citruscart::getInstance()->get('cartbutton', 'image'))
                {
                    case "button":
                        ?>
                        <input onclick="<?php echo $onclick; ?>" value="<?php echo JText::_('COM_CITRUSCART_ADD_TO_CART'); ?>" type="button" class="btn" />
                        <?php
                        break;
                    case "image":
                    default:
                        ?>
                        <img class='addcart' src='<?php echo Citruscart::getUrl('images')."addcart.png"; ?>' alt='<?php echo JText::_('COM_CITRUSCART_ADD_TO_CART'); ?>' onclick="<?php echo $onclick; ?>" />
                        <?php
                        break;
                }
            endif;
            ?>
        </div>

        <div class="reset"></div>
    </div>

<div class="reset"></div>

</form>
<?php endif;?>
