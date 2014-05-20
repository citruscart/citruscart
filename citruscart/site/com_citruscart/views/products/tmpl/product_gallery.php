<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$gallery_data = $this->gallery_data;
$product_id = $app->input->getInt('id');
$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'media/citruscart/js/jquery.zoom.js');

$doc->addStyleSheet(JUri::root(true).'/media/citruscart/css/imagezoom.css');

$product_image = CitruscartHelperProduct::getImage($product_id, '', '', 'full', true, false, array(), true );
$product_image_thumb = CitruscartHelperProduct::getImage($product_id, '', '', 'thumb', true, false, array(), true );

?>

<?php if ( $gallery_data->show_gallery ):?>

<!--<div class="dsc-wrap product_gallery" id="product_gallery"> -->
	<!-- <div id="product_gallery_header" class="citruscart_header dsc-wrap">
		<span><?php // echo JText::_('COM_CITRUSCART_IMAGES'); ?> </span>
	</div> -->
	<?php $i = 1; ?>
	<ul class="unstyled" id="citruscart-product-gallery-list<?php $product_id?>">
	<?php foreach ( $gallery_data->images as $image ):?>
		<?php
	    $src = $gallery_data->uri . $image;
	    if (JFile::exists( Citruscart::getPath( 'products_thumbs' ) . DS . $image )) {
	        $src = $gallery_data->uri . "thumbs/" . $image;
		    }
		   $product_image = $gallery_data->uri."/".$image;
		    $id ="citruscart_alt_image".$product_id."_".$i;

		    $alt_id ="citruscart_product_image_alt".$product_id."_".$i;
		    $onclick="changeAltImage('/ $i /')";
		?>
		<li>
		<a href="#" data-image="<?php echo JRoute::_($src);?>" >
		<!-- id="citruscart_alt_image<?php echo $i;?>"  -->
		 	<img id="<?php echo $id;?>" onclick="changeAltImage(<?php echo $alt_id?>)" class="citruscart_image-product_gallery_thumb"
		 		 src="<?php echo $gallery_data->uri . "thumbs/" . $image;?>"
		 		  data-zoom-image="<?php echo $src;?>"
		 		  href="<?php echo $src;?>"

		 	 />
		 	 <img id="<?php echo $alt_id;?>"  src="<?php echo $product_image;?>" style="display:none;"/>

		 </a>
		<?php // echo CitruscartUrl::popup( $gallery_data->uri . $image, '<img class="dsc-wrap product_gallery_thumb"  style="width : 250 height:350; " src="' . $src . '" alt="' . $gallery_data->product_name . '" />', array( 'update' => false, 'img' => true ) ); ?>
		<?php //echo CitruscartUrl::popup( $gallery_data->uri . $image, '<img id="'.$id.'" onclick="' . $onclick . '"  class="dsc-wrap product_gallery_thumb" width="50px" height="50px" " src="' . $src . '" alt="' . $gallery_data->product_name . '" />', array( 'update' => false, 'img' => true ) ); ?>
		</li>
				<!--
    	<div class="dsc-wrap product_gallery_thumb" id="product_gallery_thumb_<?php echo $i;?>">
    	<?php //	echo CitruscartUrl::popup( $gallery_data->uri . $image, '<img class="dsc-wrap product_gallery_thumb"  style="width : 250 height:350; " src="' . $src . '" alt="' . $gallery_data->product_name . '" />', array( 'update' => false, 'img' => true ) ); ?>
    	  </div>
    	<?php $i++; ?>
        -->
    	<?php  endforeach;	?>
    	</ul>
    	<!--</div>
</div> -->

	<?php endif;?>
<style type="text/css">
	/*set a border on the images to prevent shifting*/
 #gallery_01 img{border:2px solid white;}

 /*Change the colour*/
 .active img{border:2px solid #333 !important;}
</style>
<script type="text/javascript">
	var a = 0;

	function changeAltImage(id){
		jQuery("#citruscart_main_image<?php echo $product_id;?>").attr('src',id.src);
		jQuery(".zoomImg").attr('src',id.src);
		a = a+1;
		var main_image_src=jQuery("#product_main_image").val();
		var html ="<li><a href='#' ><img src='"+main_image_src +"' onclick='setMainImage(this)' class='citruscart_image-product_gallery_thumb' />"+
	 		  "<img id='<?php echo $alt_id;?>'  src='"+main_image_src +"' style='display:none;'/>"+
			  "</a></li>";
		if(a == 1){
			jQuery("#citruscart-product-gallery-list<?php $product_id?>").append(html);
		}
	}

	function setMainImage(html){
		jQuery("#citruscart_main_image<?php echo $product_id;?>").attr('src',html.src);
		jQuery(".zoomImg").attr('src',html.src);
	}
</script>
