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

$gallery_data = $this->gallery_data;
?>

<?php
if ( $gallery_data->show_gallery )
{
	?>

<div class="dsc-wrap product_gallery" id="product_gallery">
	<div id="product_gallery_header" class="citruscart_header dsc-wrap">
		<span><?php echo JText::_('COM_CITRUSCART_IMAGES'); ?> </span>
	</div>
	<?php
	$i = 1;
	foreach ( $gallery_data->images as $image )
	{
	    $src = $gallery_data->uri . $image;
	    if (JFile::exists( Citruscart::getPath( 'products_thumbs' ) . "/" . $image )) {
	        $src = $gallery_data->uri . "thumbs/" . $image;
	    }
		?>
    	<div class="dsc-wrap product_gallery_thumb" id="product_gallery_thumb_<?php echo $i;?>">

    	<?php
    		echo CitruscartUrl::popup( $gallery_data->uri . $image, '<img  style="width : 467 height:700; " src="' . $src . '" alt="' . $gallery_data->product_name . '" />', array( 'update' => false, 'img' => true ) ); ?>
    	</div>
    	<?php
    	$i++;
	}
	?>
</div>
	<?php
}
