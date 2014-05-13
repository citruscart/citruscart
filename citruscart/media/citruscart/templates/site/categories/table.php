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

$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');
$state = @$this->state;
$items = @$this->items;
$citems = @$this->citems;
?>

<div id="citruscart" class="products default">

    <?php if ($this->level > 1 && Citruscart::getInstance()->get('display_citruscart_pathway')) : ?>
        <div id='citruscart_breadcrumb'>
            <?php echo CitruscartHelperCategory::getPathName($this->cat->category_id, 'links'); ?>
        </div>
    <?php endif; ?>

    <div id="citruscart_categories">
        <div id='citruscart_category_header'>
            <?php if (isset($state->category_name)) : ?>
                <span><?php echo @$this->title; ?></span>
            <?php else : ?>
                <span><?php  echo JText::_('COM_CITRUSCART_ALL_CATEGORIES'); ?></span>
            <?php endif; ?>

            <div class='category_description'><?php echo $this->cat->category_description; ?></div>
        </div>

        <?php if (!empty($citems)) : ?>
            <div class="citruscart_subcategories">
                <?php
                foreach ($citems as $citem) :
                    $model = JModelLegacy::getInstance('Products', 'CitruscartModel');
                    $model->setState('filter_enabled', '1');
                    $model->setState('filter_category', $citem->category_id);
                    $model->setState('order', 'tbl.ordering');
                    $model->setState('direction', 'ASC');
                    $products = $model->getList();
                    // if there are no products, skip it
                    if (empty($products)) { continue; }
                    ?>
                    <table class="subcategory table table-striped table-bordered" style="width: 100%;">
                    <thead>
                    <tr>
                        <th class="subcategory_name" style="background-color: #DDDDDD;">
                            <?php echo $citem->category_name; ?>
                        </th>
                        <th class="subcategory_price" style="background-color: #DDDDDD;">
                            <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($products as $product)
                    {
                        $itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $citem->category_id, true );
                        $product->itemid = (!empty($itemid)) ? $itemid : JRequest::getInt('Itemid', $itemid);
                        ?>
                        <tr>
                        <td>
                            <a href="<?php echo JRoute::_($product->link."&filter_category=".$citem->category_id."&Itemid=".$product->itemid ); ?>">
                            <?php echo $product->product_name; ?>
                            </a>
                        </td>
                        <td class="subcategory_price">
                            <?php echo CitruscartHelperBase::currency( $product->price ); ?>
                        </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    </table>
                <?php
                endforeach;
                ?>
                <div class="reset"></div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($items)) : ?>
        <table class="subcategory" style="width: 100%;">
        <thead>
        <tr>
            <th class="subcategory_name" style="background-color: #DDDDDD;">
                <?php echo $this->cat->category_name; ?>
            </th>
            <th class="subcategory_price" style="background-color: #DDDDDD;">
                <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) :
            $itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $this->cat->category_id, true );
            $item->itemid = (!empty($itemid)) ? $itemid : JRequest::getInt('Itemid', $itemid);
            ?>
                <tr>
                <td>
                    <a href="<?php echo JRoute::_($item->link."&filter_category=".$this->cat->category_id."&Itemid=".$item->itemid ); ?>">
                    <?php echo $item->product_name; ?>
                    </a>
                </td>
                <td class="subcategory_price">
                    <?php echo CitruscartHelperBase::currency( $item->price ); ?>
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>

    <?php endif; ?>

</div>