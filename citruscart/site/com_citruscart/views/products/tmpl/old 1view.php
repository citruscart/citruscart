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

JHTML::_('behavior.modal');
$doc = JFactory::getDocument();

JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php JHtml::_('script', 'media/citruscart/js/common.js', false, false); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart_inventory_check.js', false, false); ?>
<?php
$state = $this->state;
$item = $this->row;
$product_image = CitruscartHelperProduct::getImage($item->product_id, '', '', 'full', true, false, array(), true );
$product_image_thumb = CitruscartHelperProduct::getImage($item->product_id, '', $item->product_name, 'full', false, false, array(), true );

$app = JFactory::getApplication();
?>
<style>
.citruscart_image {
    text-decoration: none;
    display: block;
    width: 50px;
    height: 80px;
    float: left;
    margin: 0 3px 3px 0;
    opacity: 1;
    -webkit-transform: scale(1,1);
    -webkit-transition-timing-function: ease-out;
    -webkit-transition-duration: 250ms;
    -moz-transform: scale(1,1);
    -moz-transition-timing-function: ease-out;
    -moz-transition-duration: 250ms;
}
.citruscart_image:hover {
    opacity: .7;
    -webkit-transform: scale(1.05,1.07);
    -webkit-transition-timing-function: ease-out;
    -webkit-transition-duration: 250ms;
    -moz-transform: scale(1.05,1.07);
    -moz-transition-timing-function: ease-out;
    -moz-transition-duration: 250ms;
    position: relative;
    z-index: 99;
}

</style>
    <?php if ( $this->defines->get( 'display_citruscart_pathway' ) ) : ?>
        <div id='citruscart_breadcrumb'>
            <?php echo CitruscartHelperCategory::getPathName( $this->cat->category_id, 'links', true ); ?>
        </div>
    <?php endif; ?>
    <div class="row">
  <div class="col-md-2">.col-md-4</div>
  <div class="col-md-6">.col-md-4</div>
  <div class="col-md-4">.col-md-4</div>
</div>
<div class="row">
		<div class="col-md-2">
		<?php echo CitruscartHelperProduct::getGalleryLayout( $this, $item->product_id, $item->product_name, $item->product_full_image ); ?>
       <?php echo $this->files; ?>
	</div>
	
	<div class="col-md-6">
		<div id="citruscart_product">
		 	<!--<div id="product_image" class="dsc-wrap product_image">-->

            	<?php  echo CitruscartUrl::popup( $product_image, $product_image_thumb, array( 'update' => false, 'img' => true ) ); ?>
            	<p>
	            <?php if ( isset( $item->product_full_image ) ):?>
				<?php echo CitruscartUrl::popup( $product_image, JText::_('COM_CITRUSCART_VIEW_LARGER'),array('update' => false, 'img' => true) );?>
				<?php endif;?>

            	</p>
          	<!--</div>-->
          </div>
	</div>
	
	<!--
	<h3 class="productheader" style="text-align:center;">
	    <?php // echo htmlspecialchars_decode( $item->product_name ); ?>
	 </h3> -->
	
	
<div class="col-md-4">
	<div id="citruscart_product">
		
		 <ul class="list-unstyled">

		    <!-- product properties list starts -->
		    <li class="pull-right productproperties">

	        <?php if ( !empty( $item->product_model ) || !empty( $item->product_sku ) ) : ?>
	            <div id='citruscart_product_header'>

	                <?php if ( !empty( $item->product_model ) ) : ?>
	                    <span class="model">
	                        <span class="title"><?php echo JText::_('COM_CITRUSCART_MODEL'); ?>:</span>
	                        <?php echo $item->product_model; ?>
	                    </span>
	                <?php endif; ?>

	                <?php if ( !empty( $item->product_sku ) ) : ?>
	                    <span class="sku">
	                        <span class="title"><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</span>
	                        <?php echo $item->product_sku; ?>
	                    </span>
	                <?php endif; ?>
	            </div>
	        <?php endif; ?>

	       <?php if ( $this->defines->get( 'shop_enabled', '1' ) ) : ?>
	            <div class="dsc-wrap product_buy" style="" id="product_buy_<?php echo $item->product_id; ?>">
	                <?php echo CitruscartHelperProduct::getCartButton( $item->product_id ); ?>
	            </div>
	        <?php endif; ?>
	     </li><!-- product properties list ends -->

	     </ul>


	</div>
</div>
</div>






















