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

<!-- citruscart div starts -->
<div id="citruscart" class="dsc-wrap products view product-<?php echo $item->product_id; ?> <?php echo $item->product_classes; ?>">

    <?php if ( $this->defines->get( 'display_citruscart_pathway' ) ) : ?>
        <div id='citruscart_breadcrumb'>
            <?php echo CitruscartHelperCategory::getPathName( $this->cat->category_id, 'links', true ); ?>
        </div>
    <?php endif; ?>
   
    <?php if ( $this->defines->get( 'enable_product_detail_nav' ) && (!empty($this->surrounding['prev']) || !empty($this->surrounding['next'])) ) { ?>
        <div class="pagination">
            <ul id='citruscart_product_navigation'>
                <?php if ( !empty( $this->surrounding['prev'] ) ) { ?>
                    <li class='prev'>
                        <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&task=view&id=" . $this->surrounding['prev'] . "&Itemid=" . $this->getModel()->getItemid( $this->surrounding['prev'] ) ); ?>">
                            <?php echo JText::_( "COM_CITRUSCART_PREVIOUS" ); ?>
                        </a>
                    </li>
                <?php } ?>
                <?php if ( !empty( $this->surrounding['next'] ) ) { ?>
                    <li class='next'>
                        <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&task=view&id=" . $this->surrounding['next'] . "&Itemid=" . $this->getModel()->getItemid( $this->surrounding['next'] ) ); ?>">
                            <?php echo JText::_( "COM_CITRUSCART_NEXT" ); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <!-- row-fluid div starts -->
    <div class="row-fluid">
     
    <!-- citruscart product div starts -->
    <div id="citruscart_product" class="dsc-wrap">

        <?php if ( !empty( $this->onBeforeDisplayProduct ) ) : ?>
            <div id='onBeforeDisplayProduct_wrapper'>
            <?php echo $this->onBeforeDisplayProduct; ?>
            </div>
        <?php endif; ?>

        <!-- <div id='citruscart_product_header' class="dsc-wrap">
            <h2 class="product_name">
                <?php echo htmlspecialchars_decode( $item->product_name ); ?>
            </h2>
        </div> -->

    <!-- row-fluid div starts -->    
    <div class="row-fluid">
     
        <div id="product_image" class="dsc-wrap product_image">
        
            <?php  echo CitruscartUrl::popup( $product_image, $product_image_thumb, array( 'update' => false, 'img' => true ) ); ?>
            <div>
	            <?php
				if ( isset( $item->product_full_image ) )
				{
					echo CitruscartUrl::popup( $product_image, JText::_('COM_CITRUSCART_VIEW_LARGER'),
							array(
								'update' => false, 'img' => true
							) );
				}
				?>
            </div>
        </div>            
        
        <!-- product name unorder list starts -->
        <ul class="unstyled">
        
            <!-- product name list starts -->
        	<li class="center">              
		        <h3 class="productheader">
		                <?php echo htmlspecialchars_decode( $item->product_name ); ?>
		        </h3>
		    </li><!-- product name list ends -->
		    
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
	     	                  
        </div><!-- row-fluid div ends -->
        
        <!-- review div starts -->
        <div class="productreview">
        <!-- unorder list starts -->
        <ul class="unstyled">       
	       <!-- list starts -->
	       <li>
	        <?php if ( $this->defines->get( 'product_review_enable', '0' ) ) { ?>
	            <div class="dsc-wrap">
	              <div class="pull-left">
	                <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating, $this ); ?>
	                <?php if ( !empty( $item->product_comments ) ) : ?>
	                <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
	                <?php endif; ?>
	               </div>
	            </div>
	        <?php } ?>	        
	        </li> 
	        <li>                       
	        <?php echo CitruscartHelperProduct::getProductShareButtons( $this, $item->product_id ); ?>      
	        </li>
	        
        </ul><!-- unorder list ends -->
        </div><!-- review div ends --> 
         
        <?php if ( $this->defines->get( 'ask_question_enable', '1' ) ) : ?>
        <div id="product_questions" class="dsc-wrap dsc-clear">
            <?php
				$uri = JFactory::getURI( );
				$return_link = base64_encode( $uri->__toString( ) );
				$asklink = "index.php?option=com_citruscart&view=products&task=askquestion&id={$item->product_id}&return=" . $return_link;

				if ( $this->defines->get( 'ask_question_modal', '1' ) )
				{
					$height = $this->defines->get( 'ask_question_showcaptcha', '1' ) ? '570' : '440';
					$asktxt = CitruscartUrl::popup( "{$asklink}.&tmpl=component", JText::_('COM_CITRUSCART_ASK_A_QUESTION_ABOUT_THIS_PRODUCT'),
							array(
								'width' => '490', 'height' => "{$height}"
							) );
				}
				else
				{
					$asktxt = "<a href='{$asklink}'>";
					$asktxt .= JText::_('COM_CITRUSCART_ASK_A_QUESTION_ABOUT_THIS_PRODUCT');
					$asktxt .= "</a>";
				}
			?>
            [<?php echo $asktxt; ?>]
        </div>
        <?php endif; ?>

        <?php // display this product's group ?>
        <?php echo $this->product_children; ?>

        <?php if ( $this->product_description ) : ?>
            <div id="product_description" class="dsc-wrap">
                <?php if ( $this->defines->get( 'display_product_description_header', '1' ) ) : ?>
                    <div id="product_description_header" class="citruscart_header dsc-wrap">
                        <span><?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?></span>
                    </div>
                <?php endif; ?>
                <?php echo $this->product_description; ?>
            </div>
        <?php endif; ?>

        <?php  echo CitruscartHelperProduct::getGalleryLayout( $this, $item->product_id, $item->product_name, $item->product_full_image ); ?>
        <?php // display the files associated with this product ?>
        <?php echo $this->files; ?>

        <?php // display the products required by this product ?>
        <?php echo $this->product_requirements; ?>

        <?php // display the products associated with this product ?>
		    <?php if ( $this->defines->get( 'display_relateditems' ) ) : ?>
    	    <?php echo $this->product_relations; ?>
				<?php endif; ?>

        <?php if ( !empty( $this->onAfterDisplayProduct ) ) : ?>
            <div id='onAfterDisplayProduct_wrapper' class="dsc-wrap">
            <?php echo $this->onAfterDisplayProduct; ?>
            </div>
        <?php endif; ?>

        <div class="product_review dsc-wrap" id="product_review">
            <?php if ( !empty( $this->product_comments ) )
			{
				echo $this->product_comments;
			} ?>
        </div>

      </div><!-- citruscart product div ends -->
    </div><!-- row-fluid div ends -->
</div><!-- citruscart div ends -->
