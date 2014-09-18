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
   $doc->addScript(JUri::root(true).'/media/citruscart/js/jquery.zoom.js');
   $doc->addStyleSheet(JUri::root(true).'/media/citruscart/css/imagezoom.css');

   JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php JHtml::_('script', 'media/citruscart/js/common.js', false, false); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart_inventory_check.js', false, false); ?>
<?php
   JHtml::_('stylesheet', 'media/citruscart/css/citruscart.css');
   $state = $this->state;
   $item = $this->row;
   $product_image = CitruscartHelperProduct::getImage($item->product_id, '', '', 'full', true, false, array(), true );
   $product_image_thumb = CitruscartHelperProduct::getImage($item->product_id, '', $item->product_name, 'full', false, false, array(), true );

   $app = JFactory::getApplication();
   ?>
<div id="citruscart" class="dsc-wrap products view product-<?php echo $item->product_id; ?> <?php echo $item->product_classes; ?>">
   <?php if ( $this->defines->get( 'display_citruscart_pathway' ) ) : ?>
   <div id='citruscart_breadcrumb'>
      <?php echo CitruscartHelperCategory::getPathName( $this->cat->category_id, 'links', true ); ?>
   </div>
   <?php endif; ?>
   <!-- row-fluid div starts -->
   <div class="row-fluid">
      <!-- citruscart product div starts -->
      <div id="citruscart_product" class="dsc-wrap">
         <?php if ( !empty( $this->onBeforeDisplayProduct ) ) : ?>
         <div id='onBeforeDisplayProduct_wrapper'>
            <?php echo $this->onBeforeDisplayProduct; ?>
         </div>
         <?php endif; ?>
         <!-- row-fluid div starts -->
         <div class="row-fluid">
            <div class="span2 col-md-2 citruscart-view-product-gallery" >
               <?php echo CitruscartHelperProduct::getGalleryLayout( $this, $item->product_id, $item->product_name, $item->product_full_image ); ?>
            </div>
            <div class="span5 col-md-5 citruscart-view-product-main-image">
               <?php echo CitruscartUrl::popupImage( $product_image, $product_image_thumb, array( 'update' => false, 'img' => true ) ); ?>
               <input type="hidden" id="product_main_image" value="<?php echo $product_image;?>"/>
               <div>
               </div>
            </div>
            <div id="citruscart_product_buy_info" class="span5 col-md-5 citruscart_add_info">
               <!-- product name unorder list starts -->

                  <!-- product properties list starts -->

                	     <h3>
                        	<?php echo htmlspecialchars_decode( $item->product_name ); ?>
                    	 </h3>
						<hr>
                    	 <ul class="citruscart-rating pull-left">
                        	<!-- list starts -->
                        	<li>
                           <?php if ( $this->defines->get( 'product_review_enable', '0' ) ) { ?>
                           <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating, $this ); ?>
                           <?php if ( !empty( $item->product_comments ) ) : ?>
                           <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
                           <?php endif; ?>
                           <?php } ?>
                        </li>
                     </ul>
                     <div id="citruscart_model_sku">
	                     <!-- unorder list ends -->
	                     <?php if ( !empty( $item->product_model ) || !empty( $item->product_sku ) ) : ?>
	                     <?php if ( !empty( $item->product_model ) ) : ?>
	                   		<small><strong><?php echo JText::_('COM_CITRUSCART_MODEL'); ?></strong>: <?php echo $item->product_model; ?></small>
	                     <?php endif; ?>
	                     <?php if ( !empty( $item->product_sku ) ) : ?>
	                     <?php echo "|";?>
	                     <small><strong><?php echo JText::_('COM_CITRUSCART_SKU'); ?></strong>:<?php echo $item->product_sku; ?></small>
	                     <?php endif; ?>
	                     <?php endif; ?>
                     </div>
                     <?php if ( $this->defines->get( 'shop_enabled', '1' ) ) : ?>
                     <div class="dsc-wrap product_buy" style="" id="product_buy_<?php echo $item->product_id; ?>">
                        <?php echo CitruscartHelperProduct::getCartButton( $item->product_id ); ?>
                     </div>
                     <?php endif; ?>
                     <!--------------------------------
                        Triggering the share Button
                        ------------------------->
		     <?php echo CitruscartHelperProduct::getProductShareButtons( $this, $item->product_id ); ?>
                     <?php  echo $this->onBeforeDisplayProductDescription;?>
                     <div class="citruscart_product_desc">
                        <?php echo $this->product_description_short; ?>
                     </div>
                     <!--------------------------------
                        Triggering the share Button
                        ------------------------->
                     <div class="citruscart_product_shareButtons">
                        <?php echo $this->onAfterDisplayProductDescription;?>
                     </div>
                 <!-- product properties list ends -->
            </div>
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
         </div>
         <!-- row-fluid div ends -->
         <!-- review div starts -->
         <?php if ( $this->defines->get( 'ask_question_enable', '1' ) ) : ?>
         <div id="product_questions"  class="pull-right">
            <?php
               $uri = JFactory::getURI( );
               $return_link = base64_encode( $uri->__toString( ) );
               $asklink = "index.php?option=com_citruscart&view=products&task=askquestion&id={$item->product_id}&return=" . $return_link;

               if ( $this->defines->get( 'ask_question_modal', '1' ) )
               {
               	$height = $this->defines->get( 'ask_question_showcaptcha', '1' ) ? '570' : '440';
               	$asktxt = CitruscartUrl::popup( "{$asklink}.&tmpl=component", JText::_('COM_CITRUSCART_ASK_A_QUESTION_ABOUT_THIS_PRODUCT'),
               			array('id'=>'askQuestion',
               				'width' => '690', 'height' => "{$height}"
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
            <?php if ( !empty( $this->product_comments ) ):?>
            <?php echo $this->product_comments; ?>
            <?php endif;?>
         </div>
      </div>
      <!-- citruscart product div ends -->
   </div>
   <!-- row-fluid div ends -->
</div>
<!-- citruscart div ends -->
<script type="text/javascript">
   /* Method to get Image Zoom onj Mouse hover
    * @params image index
    */

   jQuery(document).ready(function(){
   jQuery('#citruscart_main_image<?php echo $item->product_id;?>').zoom();
   });

   jQuery(document).ready(function(){
   jQuery('#main_image').zoom();
   });

</script>
