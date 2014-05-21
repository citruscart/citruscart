<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
$state = $this->state;
$items = $this->items;
$title = $this->title;
$citems = @$this->citems;
?>

<div id="citruscart" class="products default">

	<div id='citruscart_category_header'>
        <span><?php echo $this->title; ?></span>
        <div class='category_description'><?php echo $this->cat->manufacturer_description; ?></div>
    </div>

    <?php if (!empty($items)) : ?>
        <div id="citruscart_products">
            <?php foreach ($items as $item) : ?>
            <div class="product_item">
                <div class="product_thumb">
                    <div class="product_listimage">
                        <a href="<?php echo JRoute::_( $item->link."&filter_category=".$this->cat->category_id ."&Itemid=".$item->itemid ); ?>">
                            <?php echo CitruscartHelperProduct::getImage($item->product_id); ?>
                        </a>
                    </div>
                    <div class="reset"></div>
                </div>

                <div id="product_buy_<?php echo $item->product_id; ?>" class="product_buy" style="margin-top:20px;">
                    <?php echo $item->product_buy; ?>
                </div>

                <div class="product_info">
                    <div class="product_name">
                        <span>
                            <a href="<?php echo JRoute::_($item->link."&filter_category=".$this->cat->category_id."&Itemid=".$item->itemid ); ?>">
                            <?php echo $item->product_name; ?>
                            </a>
                        </span>
                    </div>
                    <?php if ( Citruscart::getInstance()->get('product_review_enable', '0') ) { ?>
                    <div class="product_rating" style="margin-top:3px;">
                       <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating ); ?>
                       <?php if (!empty($item->product_comments)) : ?>
                       <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
                       <?php endif; ?>
                    </div>
                    <?php } ?>
                    <?php if (!empty($item->product_model) || !empty($item->product_sku)) { ?>
                        <div class="product_numbers">
                            <span class="model">
                                <?php if (!empty($item->product_model)) : ?>
                                    <span class="title"><?php echo JText::_('COM_CITRUSCART_MODEL'); ?>:</span>
                                    <?php echo $item->product_model; ?>
                                <?php endif; ?>
                            </span>
                            <span class="sku">
                                <?php if (!empty($item->product_sku)) : ?>
                                    <span class="title"><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</span>
                                    <?php echo $item->product_sku; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php } ?>

                    <div class="product_minidesc">
                    <?php
                        if (!empty($item->product_description_short))
                        {
                            echo $item->product_description_short;
                        }
                            else
                        {
                            $str = wordwrap($item->product_description, 200, '`|+');
                            $wrap_pos = strpos($str, '`|+');
                            if ($wrap_pos !== false) {
                                echo substr($str, 0, $wrap_pos).'...';
                            } else {
                                echo $str;
                            }
                        }
                    ?>
                    </div>
                    <div class="reset"></div>
                </div>
                <div class="reset"></div>
            </div>
            <div class="reset"></div>
            <?php endforeach; ?>
        </div>

        <form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">
        <div id="products_footer" class="pagination">
            <div id="results_counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
            <?php echo $this->pagination->getListFooter(); ?>
        </div>
        <?php echo $this->form['validate']; ?>
        </form>

    <?php endif; ?>

</div>
