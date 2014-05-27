<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
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
	<?php  echo '<div id="citruscart_popular_products" class="citruscart_products_'.$params->get('display_style','flat').'">'; ?>

	<ul  class="citruscart_products_images">
    <?php foreach (@$products as $product) : ?>
	   <li class="citruscart_products_image_list">

		<!-- <div class="citruscart_product_item<?php if ($params->get('display_style','flat') == 'grid') echo ' grid' .$params->get('display_grid_items' ,'3'); ?>"> -->
		<?php if ($params->get('display_image','1') != '0') : ?>
			<?php if ($params->get('display_image_link','1') != '0') : ?>

				<a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>" >
				<?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
				</a>
			<?php else : ?>
				<?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
			<?php endif; ?>
		<?php endif; ?>

		<h5>
			<a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>"><?php echo $product->product_name; ?></a>
		</h5>

        <?php if ($params->get('display_price','1') != '0') : ?><span class="product_price"><?php echo CitruscartHelperProduct::dispayPriceWithTax($product->price, $product->tax, Citruscart::getInstance()->get('display_prices_with_tax')) ?></span><?php endif; ?>
       <!--
        <?php if ($params->get('display_description','1') != '0' && $product->product_description_short != null) : ?><p class="product_description"><?php echo $product->product_description_short ?></p><?php endif; ?>
</div>
	   -->

		 </li>

		<?php  endforeach; ?>
		</ul>

	<?php  echo '</div>';
	?>

<?php
}
    elseif ($display_null == '1')
{
    $text = JText::_( $null_text );
    echo $text;
}