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

$item = $this->item;
//$form = @$this->form;
$form['action'] ="index?option=com_citruscart&view=products";

$values =$this->values;
$values['return']=(isset($values['return'])) ? $values['return'] :"";
$formName = 'adminForm_'.$item->product_id;

$return = base64_encode( JUri::getInstance()->__toString() );
if( strlen($values['return'] ) )
	$return = $values['return'];

$working_image = Citruscart::getInstance()->get( 'dispay_working_image_product', 1);
$display_wishlist = Citruscart::getInstance()->get( 'display_wishlist', 0);

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
$js_strings = array( 'COM_CITRUSCART_UPDATING_ATTRIBUTES' );
CitruscartHelperBase::addJsTranslationStrings( $js_strings );
$changed_attr = isset( $values['changed_attr'] ) ? $values['changed_attr'] : -1;
$changed_pao = -1;
if( $changed_attr > -1 ) {
	$changed_pao = $values['attribute_'.$changed_attr];
}


?>

<div>
    <div id="validationmessage_<?php echo $item->product_id; ?>"></div>

    <form action="<?php echo JRoute::_( $form['action'] ); ?>" method="post" class="adminform" name="<?php echo $formName; ?>" enctype="multipart/form-data" >

    <!--base price-->
    <span id="product_price_<?php echo $item->product_id; ?>" class="product_price">
    	<?php  echo CitruscartHelperProduct::dispayPriceWithTax($item->price, $item->tax, $this->show_tax); ?>
    	 <!-- For UE States, we should let the admin choose to show (+19% vat) and (link to the shipping rates) -->
    	<?php if(Citruscart::getInstance()->get( 'display_prices_with_shipping') && !empty($item->product_ships)):?>
    	<?php echo CitruscartUrl::popup( JRoute::_($this->shipping_cost_link.'&tmpl=component'), JText::_('COM_CITRUSCART_LINK_TO_SHIPPING_COST') ); ?>
    	<?php endif;?>
    </span>

    <?php if (!empty($item->product_listprice_enabled)) : ?>
        <div class="product_listprice">
        <span class="title"><?php echo JText::_('COM_CITRUSCART_LIST_PRICE'); ?>:</span>
        <del><?php echo CitruscartHelperBase::currency($item->product_listprice); ?></del>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->display_cartbutton)) : ?>

    <!--attribute options-->
    <div id='product_attributeoptions_<?php echo $item->product_id; ?>' class="product_attributeoptions">
    <?php
    // Selected attribute options (for child attributes)
    $selected_opts = (!empty($this->selected_opts)) ? json_decode($this->selected_opts) : 0;

    if(!count($selected_opts))
    {
    	$selected_opts = 0;
    }

    $attributes = CitruscartHelperProduct::getAttributes( $item->product_id, $selected_opts );

    $default = CitruscartHelperProduct::getDefaultAttributeOptions($attributes);


    // First view of the page: select the first value in the list
    if(!$selected_opts)
    {
    	$selected_opts = $default;
    	$selected_opts[] = 0;
    }

    foreach ($attributes as $attribute)
    {
        ?>
        <div class="pao" id='productattributeoption_<?php echo $attribute->productattribute_id; ?>'>
        <?php    echo "<span>".$attribute->productattribute_name." : </span>";?>

		<?php
        $key = 'attribute_'.$attribute->productattribute_id;
        $selected = (!empty($values[$key])) ? $values[$key] : $default[$attribute->productattribute_id];

        $attribs = array('class' => 'inputbox productattribute',
        				'size' => '1',
	        			'onchange'=>"Citruscart.UpdateChangedAttribute( document.".$formName.", ".$attribute->productattribute_id.");
                       	 Citruscart.UpdateAddToCart( '".$this->page."','product_buy_".$item->product_id."', document.".$formName.", ".$working_image." );",
	        			'changed_attr' => $changed_attr,
	        			'changed_pao' => $changed_pao,
	        			'pid'	=> $item->product_id
						);
        echo CitruscartSelect::productattributeoptions( $attribute->productattribute_id, $selected, $key, $attribs, null, $selected_opts  );

        ?>

        </div>
        <?php
    }
	if( count( $attributes ) ) : ?>
    <input type="hidden" name="changed_attr" value="" />
	<?php
	endif;

	if (!empty($this->onDisplayProductAttributeOptions)) : ?>
        <div class='onDisplayProductAttributeOptions_wrapper'>
        <?php echo $this->onDisplayProductAttributeOptions; ?>
        </div>
    <?php endif; ?>

    </div>

    <div id='product_quantity_input_<?php echo $item->product_id; ?>' class="product_quantity_input">
        <?php if ($item->product_parameters->get('hide_quantity_input') == '1') { ?>
            <input type="hidden" name="product_qty" value="<?php echo $item->product_parameters->get('default_quantity', '1'); ?>" />
        <?php } elseif ($item->quantity_restriction && $item->quantity_min == $item->quantity_max) { ?>
            <input type="hidden" name="product_qty" value="<?php echo $item->quantity_min; ?>" />
        <?php } else { ?>
        <span class="title"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?>:</span>
        <br/>
        <input type="text"  name="product_qty" value="<?php echo $item->_product_quantity; ?>"  class="form-control" size="5" />
        <?php } ?>
    </div>
	<br/>
    <!-- Add to cart button -->
    <div id='add_to_cart_<?php echo $item->product_id; ?>' class="add_to_cart" style="display: block;">
        <input type="hidden" name="product_id" value="<?php echo $item->product_id; ?>" />
        <input type="hidden" name="filter_category" value="<?php echo $this->filter_category; ?>" />
        <input type="hidden" id="task" name="task" value="" />
        <?php if( !empty( $values['Itemid'] ) ): ?>
        <input type="hidden" name="Itemid" value="<?php echo ( int )$values['Itemid']; ?>" />
        <?php endif; ?>
        <?php echo JHTML::_( 'form.token' ); ?>
        <input type="hidden" name="return" value="<?php echo $return; ?>" />

        <?php $onclick = "Dsc.formValidation( '".JRoute::_( $this->validation )."', 'validationmessage_".$item->product_id."', 'addtocart', document.".$formName.", true, '".JText::_('COM_CITRUSCART_VALIDATING')."' );"; ?>

        <?php
        if (empty($item->product_check_inventory) || (!empty($item->product_check_inventory) && empty($this->invalidQuantity)) ) :
            switch (Citruscart::getInstance()->get('cartbutton', 'image'))
            {
                case "image":
                	// Search for localized version of the image
                	Citruscart::load('CitruscartHelperImage', 'helpers.image');
                	$image = CitruscartHelperImage::getLocalizedName("addcart.png", Citruscart::getPath('images'));
                    ?>
                    <img class='addcart' src='<?php echo Citruscart::getUrl('images').$image; ?>' alt='<?php echo JText::_('COM_CITRUSCART_ADD_TO_CART'); ?>' onclick="<?php echo $onclick; ?>" />
                    <?php
                    break;
                case "button":
                default:
                    ?>
                    <input onclick="<?php echo $onclick; ?>" value="<?php echo JText::_('COM_CITRUSCART_ADD_TO_CART'); ?>" type="button" class="btn btn-success" />
                    <?php
                    break;
            }
        endif;
        ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($item->product_recurs)) : ?>
        <div id='product_recurs_<?php echo $item->product_id; ?>' class="product_recurs">
            <span class="title"><?php echo JText::_('COM_CITRUSCART_THIS_PRODUCTS_CHARGES_RECUR'); ?></span>
            <div id="product_recurs_prices_<?php echo $item->product_id; ?>" class="product_recurs_prices">
            <?php echo JText::_('COM_CITRUSCART_RECURRING_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_price); ?>
            (<?php echo $item->recurring_payments . " " . JText::_('COM_CITRUSCART_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIODS'); ?>)
	            <?php if( $item->subscription_prorated ) : ?>
                <br/>
	                <?php echo JText::_('COM_CITRUSCART_INITIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->prorated_price); ?>
	                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->prorated_interval." ". JText::_('$item->prorated_unit PERIOD UNIT')." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
	            <?php else : ?>
            <?php if ($item->recurring_trial) : ?>
                <br/>
	                <?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
	                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
	            <?php endif;?>
            <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($item->product_check_inventory)) : ?>
        <?php if (Citruscart::getInstance()->get('display_product_quantity', '1')) : ?>
        <div id='available_stock_<?php echo $item->product_id; ?>' class="available_stock">
          <?php echo JText::_('COM_CITRUSCART_AVAILABLE_STOCK'); ?> <label id="stock_<?php echo $item->product_id; ?>"><?php echo (int) $this->availableQuantity->quantity; ?></label>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($item->product_check_inventory) && !empty($this->invalidQuantity) ) : ?>
        <div id='out_of_stock_<?php echo $item->product_id; ?>' class="out_of_stock">
          <?php echo JText::_('COM_CITRUSCART_OUT_OF_STOCK'); ?>
        </div>
    <?php endif; ?>

	<?php if( $display_wishlist ): ?>
        <div id='add_to_wishlist_<?php echo $item->product_id; ?>' class="add_to_wishlist">
            <?php
            $xref_id = $this->user->id;
            $xref_type = 'user';
            if (empty($this->user->id)) {
                $xref_id = $this->session->getId();
                $xref_type = 'session';
            }
			sort( $selected_opts );
			array_shift( $selected_opts );
            ?>
            <?php if (!$this->getModel()->isInWishlist($item->product_id, $xref_id, $xref_type, implode(',', $selected_opts ))) { ?>
            <?php $onclick = "document.$formName.task.value='addtowishlist'; document.$formName.submit();"; ?>
            <a href="javascript:void(0);" onclick="<?php echo $onclick; ?>"><?php echo JText::_('COM_CITRUSCART_ADD_TO_WISHLIST'); ?></a>
            <?php } else { ?>
                <?php echo JText::_('COM_CITRUSCART_ITEM_ALREADY_IN_WISHLIST'); ?>
            <?php } ?>
        </div>
	<?php endif; ?>
    </form>
</div>
