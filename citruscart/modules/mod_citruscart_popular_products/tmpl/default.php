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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// Add CSS
$document->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_popular_products/tmpl/mod_citruscart_popular_products.css');

$resize = false;
$options = array();
if ($params->get('display_image_width', '') != '')
{
	$options['width'] = $params->get('display_image_width');
}
if ($params->get('display_image_height', '') != '')
{
	$options['height'] = $params->get('display_image_height');
}

if ($num > 0 && @$products)
{
	?>
	<style>

	</style>



	<?php echo '<div class="citruscart_products_'.$params->get('display_style','flat').'">';
    // Loop through the products to display
    foreach (@$products as $product) : ?>

       <ul id="mod_citruscart_popular_product"  class="popular_products unstyled">
	    <li class="ul-images">

		<div class="citruscart_product_item<?php if ($params->get('display_style','flat') == 'grid') echo ' grid' .$params->get('display_grid_items' ,'3'); ?>">

      	<?php if ($params->get('display_image','1') != '0') : ?>
			<?php if ($params->get('display_image_link','1') != '0') : ?>

				<p class="product_image"><a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>">
				<?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
				</a></p>
			<?php else : ?>
				<p class="product_image"><?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?></p>
			<?php endif; ?>
		<?php endif; ?>
		<h5><a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>"><?php echo $product->product_name; ?></a></h5>

		<?php if ($params->get('display_price','1') != '0') : ?><p class="product_price"><?php echo CitruscartHelperProduct::dispayPriceWithTax($product->price, $product->tax, Citruscart::getInstance()->get('display_prices_with_tax')) ?></p><?php endif; ?>

       <!--
        <?php if ($params->get('display_description','1') != '0' && $product->product_description_short != null) : ?><p class="product_description"><?php echo $product->product_description_short ?></p><?php endif; ?>
	   -->
	  </div>
		 </li>
	</ul>
		<?php  endforeach;
	echo '</div>';
	?>

<?php
}
    elseif ($display_null == '1')
{
    $text = JText::_( $null_text );
    echo $text;
}
