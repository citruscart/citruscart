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
$document->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_products/tmpl/mod_citruscart_products.css');
?>
<?php
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

if ($num > 0 && $products)
{
	$k = 0;
	echo '<div class="citruscart_products_'.$params->get('display_style','flat').'">';
	echo '<div class="citruscart_product_box" >';
	?>

	<ul id="citruscart_featured_products" class="citruscart_products_container">
	<?php
    // Loop through the products to display
    foreach ($products as $product) :
    	$k++;
    ?>

	    <li class="citruscart_product">

		<!-- <div class="citruscart_product_item <?php echo $params->get('moduleclass_sfx');?><?php if ($params->get('display_style','flat') == 'grid') echo ' grid' .$params->get('display_grid_items' ,'3'); ?>" >
		-->

		<?php if ($params->get('display_image','1') != '0') : ?>
			<?php if ($params->get('display_image_link','1') != '0') : ?>
				<a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>">
				<?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
				</a>
			<?php else : ?>
				<?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($params->get('display_title','1') != '0') : ?>
	    <h5><a href="<?php echo JRoute::_( $product->link."&Itemid=".$product->itemid ); ?>"><?php echo $product->product_name; ?></a></h5>
		<?php endif; ?>

        <strong>
        	<?php if ($params->get('display_price','1') != '0') : ?>
        		<span class="product_price">
        			<?php echo CitruscartHelperProduct::dispayPriceWithTax($product->price, $product->tax, Citruscart::getInstance()->get('display_prices_with_tax')) ?>
        		</span>
        	<?php endif; ?>
        </strong>
		<?php if ($params->get('display_description','1') != '0' && $product->product_description_short != null) : ?>
			<div class="product-description">
			<?php echo $product->product_description_short ?>
			</div>
		<?php endif; ?>

	</li>
		<?php if ($params->get('display_style','flat') == 'grid' && $params->get('display_grid_items' ,'3') == $k): ?>
			<?php echo '</div>'; ?>
			<?php echo '<div class="citruscart_product_box">'; ?>
		<?php $k = 0; endif; ?>


		<?php  endforeach;
		echo '</ul>';
	echo '</div>';
	echo '</div>';

}
    elseif ($display_null == '1')
{
    $text = JText::_( $null_text );
    echo $text;
}
?>


<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('.Citruscart_product_box .Citruscart_product_item:last-child').addClass('right');
	});
</script>
