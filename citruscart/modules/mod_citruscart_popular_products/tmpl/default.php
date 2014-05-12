<?php
/**
 * @version    1.5
 * @package    Citruscart
 * @author     Dioscouri Design
 * @link     http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

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
	echo '<div class="citruscart_products_'.$params->get('display_style','flat').'">';
    // Loop through the products to display
    foreach (@$products as $product) : ?>
		<div class="citruscart_product_item<?php if ($params->get('display_style','flat') == 'grid') echo ' grid' .$params->get('display_grid_items' ,'3'); ?>">
        <h4 class="product_title"><a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>"><?php echo $product->product_name; ?></a></h4>
		
		<?php if ($params->get('display_image','1') != '0') : ?>
			<?php if ($params->get('display_image_link','1') != '0') : ?>
				<p class="product_image"><a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>">
				<?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
				</a></p>
			<?php else : ?>
				<p class="product_image"><?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?></p>
			<?php endif; ?>
		<?php endif; ?>

        <?php if ($params->get('display_price','1') != '0') : ?><p class="product_price"><?php echo CitruscartHelperProduct::dispayPriceWithTax($product->price, $product->tax, Citruscart::getInstance()->get('display_prices_with_tax')) ?></p><?php endif; ?>

        <?php if ($params->get('display_description','1') != '0' && $product->product_description_short != null) : ?><p class="product_description"><?php echo $product->product_description_short ?></p><?php endif; ?>
	</div>
		<?php  endforeach;
	echo '</div>';

}  
    elseif ($display_null == '1') 
{
    $text = JText::_( $null_text );
    echo $text;
}