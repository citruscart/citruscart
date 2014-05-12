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

// Add CSS
$document->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_my_orderitems/tmpl/mod_citruscart_my_orderitems.css');

$resize = false;
$options = array();
if ($params->get('display_image_width'))
{
	$options['width'] = $params->get('display_image_width');
}
if ($params->get('display_image_height'))
{
	$options['height'] = $params->get('display_image_height');
}

if (!empty($products))
{
    // Loop through the products to display
    foreach ($products as $product) : ?>
        <div class="mod_citruscart_my_orderitems_item">
            <?php if ($params->get('display_image')) : ?>
                <div class="mod_citruscart_my_orderitems_item_image">
                <a href="<?php echo JRoute::_( $product->link ); ?>">
                <?php echo CitruscartHelperProduct::getImage($product->product_id, 'id', $product->product_name, 'thumb', false, $resize, $options); ?>
                </a>
                </div>
            <?php endif; ?>
            <span class="mod_citruscart_my_orderitems_item_name"><a href="<?php echo JRoute::_( $product->link ); ?>"><?php echo $product->product_name; ?></a></span>
        </div>
	<?php endforeach;
}
    elseif ($display_null == '1')
{
    echo JText::_( $null_text );
}