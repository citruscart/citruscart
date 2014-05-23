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
<style>
.ul-images{
	margin-left :15px;
	margin-bottom:5px;
	padding:5px 10px;
	border:1px solid #ccc;
	text-align: center;
	width:200px;
	height:360px;
	}

	#citruscart .citruscart-product-main-images {
	width:250px;
	height:250px;
}
</style>

<div id="citruscart" class="products default">

	<div id='citruscart_category_header'>
        <span><?php echo $this->title; ?></span>
        <div class='category_description'><?php echo $this->cat->manufacturer_description; ?></div>
    </div>

    <?php if (!empty($items)) : ?>
        <div id="citruscart_products" style="display:inline-flex;">
            <?php foreach ($items as $item) : ?>
            <div class="product_item">
            <!-- manufacturers unorder list ends -->
            <ul class="unstyled" style="display:inline-flex;">
            	<li class="ul-images">
     	                <a href="<?php echo JRoute::_( $item->link."&filter_category=".$this->cat->category_id ."&Itemid=".$item->itemid ); ?>">
              	            <?php echo CitruscartHelperProduct::getImage($item->product_id); ?>
           	            </a>

                    <?php if ( Citruscart::getInstance()->get('product_review_enable', '0') ) { ?>
                    <div class="product_rating" style="margin-top:3px;">
                       <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating ); ?>
                       <?php if (!empty($item->product_comments)) : ?>
                       <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
                       <?php endif; ?>
                    </div>
                    <?php } ?>
                       <div class="manu_product_name">
                        <span>
                            <a href="<?php echo JRoute::_($item->link."&filter_category=".$this->cat->category_id."&Itemid=".$item->itemid ); ?>">
                            <?php echo $item->product_name; ?>
                            </a>
                        </span>
                        <br/>
                        <h5><span id="product_price_<?php echo $item->product_id; ?>" class="product_price">
					    	<?php  echo CitruscartHelperProduct::dispayPriceWithTax($item->price, @$item->tax, @$this->show_tax); ?>
					    	 <!-- For UE States, we should let the admin choose to show (+19% vat) and (link to the shipping rates) -->
					    	<br />
					    	<?php if(Citruscart::getInstance()->get( 'display_prices_with_shipping') && !empty($item->product_ships)):?>
					    	<?php echo CitruscartUrl::popup( JRoute::_($this->shipping_cost_link.'&tmpl=component'), JText::_('COM_CITRUSCART_LINK_TO_SHIPPING_COST') ); ?>
					    	<?php endif;?>
					   		 </span></h5>

                </li>
               </ul><!-- manufacturers unorder list ends -->

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
