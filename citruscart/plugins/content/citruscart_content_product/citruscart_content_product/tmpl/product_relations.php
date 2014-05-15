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
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
$items = $vars->items;
?>

    <div id="product_relations">
        <div id="product_relations_header" class="citruscart_header">
            <span><?php echo JText::_('COM_CITRUSCART_YOU_MAY_ALSO_BE_INTERESTED_IN'); ?></span>
        </div>

        <?php
        $k = 0;
        foreach ($items as $item): ?>
        <div class="productrelation">
            <div class="productrelation_item">
                <div class="productrelation_image">
                    <a href="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=products&task=view&id='.$item->product_id . '&Itemid=' . $item->itemid ); ?>">
                        <?php echo CitruscartHelperProduct::getImage($item->product_id, 'id', $item->product_name, 'full', false, false, array( 'width'=>64 ) ); ?>
                    </a>
                </div>
                <div class="productrelation_name">
                    <a href="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=products&task=view&id='.$item->product_id . '&Itemid=' . $item->itemid ); ?>">
                        <?php echo $item->product_name; ?>
                    </a>
                </div>
                <div class="productrelation_price" style="vertical-align: middle;" >
                    <?php  echo CitruscartHelperProduct::dispayPriceWithTax($item->product_price, $item->tax, $item->showtax); ?>
                </div>
            </div>
        </div>
        <?php $k = 1 - $k; ?>
        <?php endforeach; ?>

        <div class="reset"></div>
    </div>

<div class="reset"></div>
